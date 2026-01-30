import { useState, useRef, useEffect } from 'react'
import { MoreVertical, Trash2, FolderPlus, FilePlus, Edit3, Download, Copy, Scissors, Clipboard as ClipboardIcon, Upload as UploadIcon, FileEdit, FileArchive } from 'lucide-react'
import type { FileNode } from '../types'
import { isEditable } from '../utils'
import { useFiles } from '../context'
import { AlertModal, PromptModal, Modal } from '../../../components/modal'

interface OptionsMenuProps {
    file: FileNode
    onEdit?: (file: FileNode) => void
    isRoot?: boolean
}

type ModalType = 'none' | 'create_file' | 'create_dir' | 'rename' | 'alert' | 'delete_confirm';

export function OptionsMenu({ file, onEdit, isRoot = false }: OptionsMenuProps) {
    const [isOpen, setIsOpen] = useState(false)
    const [modal, setModal] = useState<ModalType>('none')
    const [modalValue, setModalValue] = useState('')
    const [alertConfig, setAlertConfig] = useState<{ title: string; message: string; type: 'info' | 'error' | 'success' }>({
        title: '',
        message: '',
        type: 'info'
    });

    const menuRef = useRef<HTMLDivElement>(null)
    const uploadInputRef = useRef<HTMLInputElement>(null)
    const { clipboard, setClipboard, refresh } = useFiles()

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
                setIsOpen(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => document.removeEventListener('mousedown', handleClickOutside)
    }, [])

    const showAlert = (title: string, message: string, type: 'info' | 'error' | 'success' = 'info') => {
        setAlertConfig({ title, message, type });
        setModal('alert');
    }

    const handleCreate = async () => {
        const type = modal === 'create_dir' ? 'dir' : 'file';
        const name = modalValue;
        if (!name) return

        try {
            const res = await fetch('/root/post/files/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    path: file.path,
                    name,
                    type
                })
            })

            if (res.ok) {
                refresh()
                setModal('none')
            } else {
                const data = await res.json()
                showAlert('Error', data.error || 'Failed to create item', 'error')
            }
        } catch (err) {
            showAlert('Error', 'An unexpected error occurred', 'error')
        }
        setIsOpen(false)
    }

    const handleRenameConfirm = async () => {
        const newName = modalValue;
        if (!newName || newName === file.name) {
            setModal('none');
            return;
        }

        try {
            const res = await fetch('/root/post/files/rename', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    path: file.path,
                    name: newName
                })
            })

            if (res.ok) {
                refresh()
                setModal('none')
            } else {
                const data = await res.json()
                showAlert('Error', data.error || 'Failed to rename item', 'error')
            }
        } catch (err) {
            showAlert('Error', 'An unexpected error occurred', 'error')
        }
        setIsOpen(false)
    }

    const handleDelete = async () => {
        try {
            const res = await fetch('/root/post/files/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ path: file.path })
            })

            if (res.ok) {
                refresh()
                setModal('none')
            } else {
                const data = await res.json()
                showAlert('Error', data.error || 'Failed to delete item', 'error')
            }
        } catch (err) {
            showAlert('Error', 'An unexpected error occurred', 'error')
        }
    }

    const handleUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const fileToUpload = e.target.files?.[0]
        if (!fileToUpload) return

        const formData = new FormData()
        formData.append('file', fileToUpload)
        formData.append('path', file.path)

        try {
            const res = await fetch('/root/post/files/upload', {
                method: 'POST',
                body: formData
            })

            if (res.ok) {
                refresh()
            } else {
                const data = await res.json()
                showAlert('Error', data.error || 'Failed to upload file', 'error')
            }
        } catch (err) {
            showAlert('Error', 'An error occurred during upload', 'error')
        }
        setIsOpen(false)
        if (uploadInputRef.current) uploadInputRef.current.value = ''
    }

    const handleDownload = () => {
        window.location.href = `/root/post/files/download?path=${encodeURIComponent(file.path)}`
        setIsOpen(false)
    }

    const handleCopy = () => {
        setClipboard({ file, mode: 'copy' })
        setIsOpen(false)
    }

    const handleCut = () => {
        setClipboard({ file, mode: 'cut' })
        setIsOpen(false)
    }

    const handlePaste = async () => {
        if (!clipboard) return

        const endpoint = clipboard.mode === 'copy' ? '/root/post/files/copy' : '/root/post/files/move'

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    path: clipboard.file.path,
                    destination: (file.type === 'dir' ? file.path : file.path.substring(0, file.path.lastIndexOf('/'))) || '/'
                })
            })

            if (res.ok) {
                refresh()
                if (clipboard.mode === 'cut') setClipboard(null)
            } else {
                const data = await res.json()
                showAlert('Error', data.error || `Failed to ${clipboard.mode} item`, 'error')
            }
        } catch (err) {
            showAlert('Error', 'An error occurred during paste operation', 'error')
        }
        setIsOpen(false)
    }

    const canPaste = !!clipboard

    return (
        <div className="relative" ref={menuRef} onClick={e => e.stopPropagation()}>
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="p-1 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                title="Options"
            >
                <MoreVertical size={16} />
            </button>

            {isOpen && (
                <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl py-1 z-10">

                    {!isRoot && (
                        <>
                            {file.type !== 'dir' && isEditable(file.name) && onEdit && (
                                <button
                                    onClick={() => { onEdit(file); setIsOpen(false); }}
                                    className="w-full text-left px-3 py-1.5 text-xs text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 flex items-center gap-2"
                                >
                                    <Edit3 size={14} />
                                    Edit
                                </button>
                            )}

                            <button
                                onClick={() => {
                                    setModalValue(file.name);
                                    setModal('rename');
                                    setIsOpen(false);
                                }}
                                className="w-full text-left px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                            >
                                <FileEdit size={14} />
                                Rename
                            </button>

                            <div className="h-px bg-slate-100 dark:bg-slate-700 my-1" />

                            <button
                                onClick={handleCopy}
                                className="w-full text-left px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                            >
                                <Copy size={14} />
                                Copy
                            </button>

                            <button
                                onClick={handleCut}
                                className="w-full text-left px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                            >
                                <Scissors size={14} />
                                Cut
                            </button>
                        </>
                    )}

                    {canPaste && (
                        <button
                            onClick={handlePaste}
                            className="w-full text-left px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                        >
                            <ClipboardIcon size={14} />
                            Paste
                        </button>
                    )}

                    {!isRoot && (
                        <>
                            <div className="h-px bg-slate-100 dark:bg-slate-700 my-1" />
                            <button
                                onClick={handleDownload}
                                className="w-full text-left px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                            >
                                <Download size={14} />
                                Download
                            </button>
                            <button
                                onClick={handleDownload} // Same as download (zip)
                                className="w-full text-left px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                            >
                                <FileArchive size={14} />
                                Compress
                            </button>
                        </>
                    )}

                    {(file.type === 'dir' || isRoot) && (
                        <>
                            {!isRoot && <div className="h-px bg-slate-100 dark:bg-slate-700 my-1" />}
                            <button
                                onClick={() => {
                                    setModalValue('');
                                    setModal('create_dir');
                                    setIsOpen(false);
                                }}
                                className="w-full text-left px-3 py-1.5 text-xs text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 flex items-center gap-2"
                            >
                                <FolderPlus size={14} />
                                New Folder
                            </button>
                            <button
                                onClick={() => {
                                    setModalValue('');
                                    setModal('create_file');
                                    setIsOpen(false);
                                }}
                                className="w-full text-left px-3 py-1.5 text-xs text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 flex items-center gap-2"
                            >
                                <FilePlus size={14} />
                                New File
                            </button>

                            <button
                                onClick={() => uploadInputRef.current?.click()}
                                className="w-full text-left px-3 py-1.5 text-xs text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 flex items-center gap-2"
                            >
                                <UploadIcon size={14} />
                                Upload File
                            </button>
                            <input
                                type="file"
                                ref={uploadInputRef}
                                className="hidden"
                                onChange={handleUpload}
                            />
                        </>
                    )}

                    {!isRoot && (
                        <>
                            <div className="h-px bg-slate-100 dark:bg-slate-700 my-1" />
                            <button
                                onClick={() => {
                                    setModal('delete_confirm');
                                    setIsOpen(false);
                                }}
                                className="w-full text-left px-3 py-1.5 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2"
                            >
                                <Trash2 size={14} />
                                Delete
                            </button>
                        </>
                    )}
                </div>
            )}

            {/* Modals */}
            <PromptModal
                isOpen={modal === 'create_file' || modal === 'create_dir'}
                onClose={() => setModal('none')}
                title={modal === 'create_dir' ? 'New Folder' : 'New File'}
                placeholder={modal === 'create_dir' ? 'Enter folder name...' : 'Enter file name...'}
                value={modalValue}
                setValue={setModalValue}
                onConfirm={handleCreate}
                confirmLabel="Create"
            />

            <PromptModal
                isOpen={modal === 'rename'}
                onClose={() => setModal('none')}
                title="Rename Item"
                value={modalValue}
                setValue={setModalValue}
                onConfirm={handleRenameConfirm}
                confirmLabel="Rename"
            />

            <Modal
                isOpen={modal === 'delete_confirm'}
                onClose={() => setModal('none')}
                title="Confirm Delete"
            >
                <div className="space-y-4">
                    <p className="text-sm text-slate-600 dark:text-slate-400">
                        Are you sure you want to delete <span className="font-mono font-bold text-slate-900 dark:text-white">{file.name}</span>? This action cannot be undone.
                    </p>
                    <div className="flex justify-end gap-3">
                        <button onClick={() => setModal('none')} className="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button onClick={handleDelete} className="px-4 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded shadow-sm transition-all active:scale-95">
                            Delete
                        </button>
                    </div>
                </div>
            </Modal>

            <AlertModal
                isOpen={modal === 'alert'}
                onClose={() => setModal('none')}
                title={alertConfig.title}
                message={alertConfig.message}
                type={alertConfig.type}
            />
        </div>
    )
}

