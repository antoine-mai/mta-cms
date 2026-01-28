import { useEffect, useState, useRef } from 'react'
import FileMenu from './menu'
import { fileService } from '../services/api'
import { fileUtils } from '../services/utils'
import { useFileExplorer } from '../context'
import { uploadToFolder } from '../services/upload'

interface FileInfo {
    name: string
    is_dir: boolean
    path: string
}

interface FolderProps {
    name: string
    path: string
    is_dir: boolean
    level: number
}

const Folder = ({ name, path, is_dir, level }: FolderProps) => {
    const {
        handleNavigate,
        openFile,
        setActionModal,
        showHiddenFiles
    } = useFileExplorer()

    const [isExpanded, setIsExpanded] = useState(false)
    const [children, setChildren] = useState<FileInfo[]>([])
    const [hasLoaded, setHasLoaded] = useState(false)
    const [allChildren, setAllChildren] = useState<FileInfo[]>([])

    const fetchChildren = async () => {
        try {
            const data = await fileService.fetchFolder(path)
            if (data.files) {
                const mapped = data.files.map((f: any) => ({ ...f, path: fileUtils.joinPath(path, f.name) }))
                setAllChildren(mapped)
            }
            setHasLoaded(true)
        } catch (err) {
            console.error("Failed to load folder", err)
        }
    }

    const handleToggle = async (e: React.MouseEvent) => {
        e.stopPropagation()
        if (!is_dir) return

        setIsExpanded(!isExpanded)

        if (!hasLoaded && !isExpanded) {
            fetchChildren()
        }
    }

    useEffect(() => {
        const handleRefresh = () => {
            if (isExpanded) {
                fetchChildren()
            }
        }
        window.addEventListener('files-changed', handleRefresh)
        return () => window.removeEventListener('files-changed', handleRefresh)
    }, [isExpanded, path])

    useEffect(() => {
        if (allChildren.length > 0) {
            const filtered = allChildren.filter(f => showHiddenFiles || !f.name.startsWith('.'))
            setChildren(filtered)
        }
    }, [showHiddenFiles, allChildren])

    const [isOptionsOpen, setIsOptionsOpen] = useState(false)
    const optionsRef = useRef<HTMLButtonElement>(null)

    const meta = { name, path, is_dir }

    return (
        <div className="select-none">
            <div
                className={`flex items-center gap-2 py-1 px-2 rounded-lg cursor-pointer transition-all hover:bg-background-light group`}
                onClick={() => {
                    if (is_dir) {
                        handleNavigate(path)
                    } else {
                        openFile(meta)
                    }
                }}
            >
                <div onClick={handleToggle} className={`p-0.5 flex items-center justify-center ${is_dir ? '' : 'invisible'}`}>
                    <span className={`material-symbols-outlined text-[18px] text-text-muted transition-transform ${isExpanded ? 'rotate-90' : ''}`}>chevron_right</span>
                </div>
                <span className={`material-symbols-outlined text-[20px] leading-none ${is_dir ? 'text-amber-500' : 'text-blue-500'}`} style={is_dir ? { fontVariationSettings: "'FILL' 1" } : {}}>
                    {is_dir ? 'folder' : 'description'}
                </span>
                <span className="text-sm whitespace-nowrap pt-0.5">{name}</span>

                <div className="relative ml-auto">
                    <button
                        ref={optionsRef}
                        className={`p-0.5 rounded flex items-center justify-center transition-opacity ${isOptionsOpen ? 'opacity-100 text-text-main' : 'text-text-secondary opacity-0 group-hover:opacity-100 hover:text-text-main'}`}
                        onClick={(e) => {
                            e.stopPropagation()
                            setIsOptionsOpen(!isOptionsOpen)
                        }}
                    >
                        <span className="material-symbols-outlined text-[16px]">more_horiz</span>
                    </button>
                    <FileMenu
                        isOpen={isOptionsOpen}
                        onClose={() => setIsOptionsOpen(false)}
                        anchorRef={optionsRef}
                        isDirectory={is_dir}
                        onEdit={() => openFile(meta)}
                        onRename={() => setActionModal({ type: 'rename', target: meta })}
                        onDelete={() => setActionModal({ type: 'delete', target: meta })}
                        onNewFolder={() => setActionModal({ type: 'new-folder', target: meta })}
                        onNewFile={() => setActionModal({ type: 'new-file', target: meta })}
                        onDownload={() => console.log("Download", name)}
                    />
                </div>
            </div>

            {isExpanded && is_dir && (
                <div className="flex flex-col ml-4 pl-2 border-l border-border-light">
                    {children.map((child) => (
                        <Folder
                            key={child.path}
                            name={child.name}
                            path={child.path}
                            is_dir={child.is_dir}
                            level={level + 1}
                        />
                    ))}
                    {children.length === 0 && hasLoaded && <div className={`ml-4 pl-4 py-1 text-xs text-text-muted italic`}>Empty</div>}
                </div>
            )}
        </div>
    )
}

export default function Sidebar({ hideHeader, initialPath }: { hideHeader?: boolean, initialPath?: string }) {
    const {
        setActionModal,
        showFoldersFirst,
        setShowFoldersFirst,
        showHiddenFiles,
        setShowHiddenFiles,
    } = useFileExplorer()

    const [rootFolders, setRootFolders] = useState<FileInfo[]>([])
    const [isSettingsOpen, setIsSettingsOpen] = useState(false)
    const [homePath, setHomePath] = useState("")
    const settingsRef = useRef<HTMLDivElement>(null)

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (settingsRef.current && !settingsRef.current.contains(event.target as Node)) {
                setIsSettingsOpen(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => {
            document.removeEventListener('mousedown', handleClickOutside)
        }
    }, [])

    const fetchRoot = async () => {
        try {
            const data = await fileService.fetchFolder(initialPath || "")
            if (data.files) {
                const items = data.files.map((f: any) => ({ ...f, path: fileUtils.joinPath(data.current_path, f.name) }))
                setRootFolders(items)
                setHomePath(data.current_path)
            }
        } catch (err) {
            console.error("Failed to fetch root files", err)
        }
    }

    useEffect(() => {
        fetchRoot()
    }, [initialPath])

    useEffect(() => {
        const handleRefresh = () => {
            fetchRoot()
        }
        window.addEventListener('files-changed', handleRefresh)
        return () => window.removeEventListener('files-changed', handleRefresh)
    }, [])
    // ... (rest of code)
    const filteredRootFolders = rootFolders.filter(f => showHiddenFiles || !f.name.startsWith('.'))

    return <aside className="w-[280px] bg-sidebar-wide border-r border-border-light flex flex-col shrink-0 hidden md:flex">
        {!hideHeader && (
            <div className="h-12 flex items-center justify-between px-4 border-b border-border-light shrink-0 relative" ref={settingsRef}>
                <h2 className="text-text-main text-lg font-bold tracking-tight">Files</h2>
                <div className="flex items-center gap-1">
                    <button
                        className={`text-text-secondary hover:text-primary transition-colors p-1 rounded-md hover:bg-background-light flex items-center justify-center ${isSettingsOpen ? 'bg-background-light text-primary' : ''}`}
                        onClick={() => setIsSettingsOpen(!isSettingsOpen)}
                    >
                        <span className="material-symbols-outlined text-[20px] leading-none">settings</span>
                    </button>
                </div>

                {isSettingsOpen && (
                    <div className="absolute right-2 top-full mt-1 w-48 bg-card-bg rounded-lg shadow-xl border border-border-light z-50 animate-in fade-in slide-in-from-top-2 duration-200 overflow-hidden">
                        <div className="py-1">
                            <button
                                className="w-full flex items-center gap-2 px-4 py-2 text-sm text-text-main hover:bg-background-light text-left transition-colors"
                                onClick={() => {
                                    setActionModal({ type: 'new-folder', target: { path: "", name: "" } })
                                    setIsSettingsOpen(false)
                                }}
                            >
                                <span className="material-symbols-outlined text-[18px] text-text-secondary">create_new_folder</span>
                                New Folder
                            </button>
                            <button
                                className="w-full flex items-center gap-2 px-4 py-2 text-sm text-text-main hover:bg-background-light text-left transition-colors"
                                onClick={() => {
                                    setActionModal({ type: 'new-file', target: { path: "", name: "" } })
                                    setIsSettingsOpen(false)
                                }}
                            >
                                <span className="material-symbols-outlined text-[18px] text-text-secondary">note_add</span>
                                New File
                            </button>
                            <button
                                className="w-full flex items-center gap-2 px-4 py-2 text-sm text-text-main hover:bg-background-light text-left transition-colors"
                                onClick={() => {
                                    uploadToFolder(homePath, fetchRoot)
                                    setIsSettingsOpen(false)
                                }}
                            >
                                <span className="material-symbols-outlined text-[18px] text-text-secondary">upload</span>
                                Upload
                            </button>
                            <div className="border-t border-border-light my-1"></div>
                            <button
                                className="w-full flex items-center gap-2 px-4 py-2 text-sm text-text-main hover:bg-background-light text-left transition-colors"
                                onClick={() => setShowHiddenFiles(!showHiddenFiles)}
                            >
                                <span className="material-symbols-outlined text-[18px] text-text-secondary">
                                    {showHiddenFiles ? 'visibility_off' : 'visibility'}
                                </span>
                                {showHiddenFiles ? 'Hide Hidden Files' : 'Show Hidden Files'}
                            </button>
                            <button
                                className="w-full flex items-center gap-2 px-4 py-2 text-sm text-text-main hover:bg-background-light text-left transition-colors"
                                onClick={() => {
                                    setShowFoldersFirst(!showFoldersFirst)
                                    setIsSettingsOpen(false)
                                }}
                            >
                                <span className="material-symbols-outlined text-[18px] text-text-secondary">
                                    {showFoldersFirst ? 'check_box' : 'check_box_outline_blank'}
                                </span>
                                Show Folders First
                            </button>
                        </div>
                    </div>
                )}
            </div>
        )}
        <div className="flex-1 overflow-hidden flex flex-col">
            <div className="flex-1 overflow-x-auto overflow-y-auto p-4">
                <div className="space-y-1 min-w-max">
                    {filteredRootFolders.map((folder) => (
                        <Folder
                            key={folder.path}
                            name={folder.name}
                            path={folder.path}
                            is_dir={folder.is_dir}
                            level={0}
                        />
                    ))}
                </div>
            </div>
        </div>
    </aside>
}