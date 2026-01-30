import { useState, useEffect } from 'react'
import {
    Folder,
    FolderOpen,
    FileText,
    LayoutGrid,
    List
} from 'lucide-react'
import type { FileNode } from './types'
import { OptionsMenu } from './components/options-menu'
import { TreeItem } from './components/tree-item'
import { Details } from './components/details'
import { Editor } from './components/editor'
import { FilesProvider } from './context'

export default function FilesPage() {
    const [currentPath, setCurrentPath] = useState('/')
    const [treeRoots, setTreeRoots] = useState<FileNode[]>([])
    const [files, setFiles] = useState<FileNode[]>([])
    const [loading, setLoading] = useState(false)
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid')
    const [selectedFile, setSelectedFile] = useState<FileNode | null>(null)
    const [editorFile, setEditorFile] = useState<FileNode | null>(null)

    useEffect(() => {
        loadTreeRoots()
    }, [])

    useEffect(() => {
        loadFile(currentPath)
        setSelectedFile(null) // Clear selection on path change
    }, [currentPath])

    const loadTreeRoots = async () => {
        try {
            const res = await fetch('/root/post/files/browse?path=/')
            const data = await res.json()
            setTreeRoots(data.items) // Load all items (files + dirs)
        } catch (error) {
            console.error(error)
        }
    }

    const loadFile = async (path: string) => {
        setLoading(true)
        try {
            const res = await fetch(`/root/post/files/browse?path=${encodeURIComponent(path)}`)
            if (res.ok) {
                const data = await res.json()
                setFiles(data.items)
            } else {
                setFiles([])
            }
        } catch (error) {
            console.error(error)
        } finally {
            setLoading(false)
        }
    }

    const handleEditFile = (file: FileNode) => {
        setEditorFile(file)
    }

    return (
        <FilesProvider onRefresh={() => { loadFile(currentPath); loadTreeRoots(); }}>
            <div className="flex flex-col h-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                {/* Toolbar */}
                <div className="h-[56px] flex items-center px-4 bg-slate-50 dark:bg-slate-800/50 justify-between border-b border-slate-200 dark:border-slate-700">
                    <div className="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium tracking-tight">
                        <FolderOpen size={18} className="text-indigo-600 dark:text-indigo-400" />
                        <span className="text-slate-900 dark:text-white font-bold">Files Manager</span>
                    </div>

                    <div className="flex flex-1 justify-center">
                        <div className="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs px-3 py-1 flex items-center gap-1.5 font-medium border border-red-100 dark:border-red-900/50">
                            <span className="material-symbols-outlined text-[16px]">warning</span>
                            Caution: Modifying files here can directly impact application stability.
                        </div>
                    </div>

                    <div className="flex bg-slate-200 dark:bg-slate-700 p-1 gap-1">
                        <button
                            onClick={() => setViewMode('grid')}
                            className={`p-1 transition-all ${viewMode === 'grid'
                                ? 'bg-white dark:bg-slate-600 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'}`}
                            title="Grid View"
                        >
                            <LayoutGrid size={16} />
                        </button>
                        <button
                            onClick={() => setViewMode('list')}
                            className={`p-1 transition-all ${viewMode === 'list'
                                ? 'bg-white dark:bg-slate-600 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'}`}
                            title="List View"
                        >
                            <List size={16} />
                        </button>
                    </div>
                </div>

                <div className="flex flex-1 overflow-hidden">
                    {/* Tree Browser (Left) */}
                    <div className="w-64 bg-slate-50 dark:bg-slate-900/50 flex flex-col overflow-y-auto custom-scrollbar border-r border-slate-200 dark:border-slate-700">
                        <div className="p-2">
                            <div className="flex items-center justify-between mb-2 px-2 group">
                                <div
                                    className="text-xs font-semibold text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-600 dark:hover:text-slate-200 transition-colors flex-1"
                                    onClick={() => setCurrentPath('/')}
                                >
                                    App Root
                                </div>
                                <div>
                                    <OptionsMenu
                                        file={{
                                            name: 'App Root',
                                            path: '/',
                                            type: 'dir',
                                            size: '',
                                            modified: ''
                                        }}
                                        isRoot={true}
                                    />
                                </div>
                            </div>

                            {/* Tree Items */}
                            <div>
                                {treeRoots.map(node => (
                                    <TreeItem
                                        key={node.path}
                                        node={node}
                                        onSelect={(n) => {
                                            setSelectedFile(n)
                                            if (n.type === 'dir') setCurrentPath(n.path)
                                        }}
                                        activePath={currentPath}
                                        onEdit={handleEditFile}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Main Content (File List or Editor) */}
                    <div className="flex-1 overflow-y-auto p-4 custom-scrollbar bg-white dark:bg-slate-800 relative">
                        {editorFile ? (
                            <div className="absolute inset-0 flex flex-col h-full w-full">
                                <Editor
                                    file={editorFile}
                                    onClose={() => setEditorFile(null)}
                                />
                            </div>
                        ) : loading ? (
                            <div className="flex h-full items-center justify-center">
                                <div className="h-8 w-8 animate-spin border-4 border-indigo-500 border-t-transparent"></div>
                            </div>
                        ) : (
                            viewMode === 'grid' ? (
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    {files.map((file) => (
                                        <div
                                            key={file.path}
                                            onClick={() => setSelectedFile(file)}
                                            onDoubleClick={() => file.type === 'dir' && setCurrentPath(file.path)}
                                            className={`
                                                group relative p-4 border transition-all cursor-pointer
                                                ${selectedFile?.path === file.path
                                                    ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800'
                                                    : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-indigo-500/50 hover:shadow-md'
                                                }
                                                ${file.type === 'dir' ? 'bg-slate-50/50' : ''}
                                            `}
                                        >
                                            <div className="flex items-start justify-between mb-2">
                                                <div className={`p-2 ${file.type === 'dir'
                                                    ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
                                                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'
                                                    }`}>
                                                    {file.type === 'dir' ? <Folder size={20} /> : <FileText size={20} />}
                                                </div>
                                                <div className="opacity-0 group-hover:opacity-100 transition-opacity absolute top-2 right-2">
                                                    <OptionsMenu file={file} onEdit={handleEditFile} />
                                                </div>
                                            </div>
                                            <h3 className="text-sm font-medium text-slate-900 dark:text-white truncate" title={file.name}>
                                                {file.name}
                                            </h3>
                                            <div className="text-xs text-slate-500 dark:text-slate-400 mt-1 flex justify-between">
                                                <span>{file.size || (file.type === 'dir' ? 'Folder' : '')}</span>
                                                <span>{file.modified}</span>
                                            </div>
                                        </div>
                                    ))}
                                    {files.length === 0 && (
                                        <div className="col-span-full py-12 text-center text-slate-400">
                                            <Folder size={48} className="mx-auto mb-4 opacity-20" />
                                            <p>Empty directory</p>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <div className="flex flex-col gap-1">
                                    {files.map((file) => (
                                        <div
                                            key={file.path}
                                            onClick={() => setSelectedFile(file)}
                                            onDoubleClick={() => file.type === 'dir' && setCurrentPath(file.path)}
                                            className={`
                                                group flex items-center justify-between p-3 border transition-all cursor-pointer
                                                ${selectedFile?.path === file.path
                                                    ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800'
                                                    : 'border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700'
                                                }
                                            `}
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className={`p-1.5 ${file.type === 'dir'
                                                    ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
                                                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'
                                                    }`}>
                                                    {file.type === 'dir' ? <Folder size={18} /> : <FileText size={18} />}
                                                </div>
                                                <div>
                                                    <div className="text-sm font-medium text-slate-900 dark:text-white">
                                                        {file.name}
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-8">
                                                <div className="text-xs text-slate-500 dark:text-slate-400 w-20 text-right">
                                                    {file.size || (file.type === 'dir' ? '-' : '')}
                                                </div>
                                                <div className="text-xs text-slate-500 dark:text-slate-400 w-32 text-right">
                                                    {file.modified}
                                                </div>
                                                <div className="opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <OptionsMenu file={file} onEdit={handleEditFile} />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    {files.length === 0 && (
                                        <div className="py-12 text-center text-slate-400">
                                            <p>Empty directory</p>
                                        </div>
                                    )}
                                </div>
                            )
                        )}
                    </div>

                    {/* File Details (Right) */}
                    <div className="border-l border-slate-200 dark:border-slate-700">
                        <Details
                            file={selectedFile}
                            onEdit={handleEditFile}
                        />
                    </div>
                </div>
            </div>
        </FilesProvider>
    )
}
