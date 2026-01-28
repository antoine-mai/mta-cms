import { useState, useEffect, useMemo } from 'react'
import { fileService } from '../services/api'
import { fileUtils } from '../services/utils'
import type { FileActionType } from '../types'

export function useFiles(initialPath: string = "") {
    const [currentPath, setCurrentPath] = useState(initialPath)
    const [files, setFiles] = useState<any[]>([])
    const [isTopLevel, setIsTopLevel] = useState(false)
    const [loading, setLoading] = useState(false)
    const [selectedFile, setSelectedFile] = useState<any>(null)
    const [showFoldersFirst, setShowFoldersFirst] = useState(true)
    const [showHiddenFiles, setShowHiddenFiles] = useState(false)
    const [openMenuFile, setOpenMenuFile] = useState<string | null>(null)
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid')
    const [actionModal, setActionModal] = useState<{ type: FileActionType, target: any } | null>(null)

    // File Content State
    const [fileContent, setFileContent] = useState<string | null>(null)
    const [isFileLoading, setIsFileLoading] = useState(false)
    const [isImage, setIsImage] = useState(false)

    const fetchFolder = async (path: string, isBackground = false) => {
        if (!isBackground) setLoading(true)
        try {
            const data = await fileService.fetchFolder(path)
            if (data.files) {
                setFiles(data.files)
                setCurrentPath(data.current_path)
                setIsTopLevel(data.is_top_level || false)
            }
        } catch (err) {
            console.error("Failed to load folder", err)
        } finally {
            if (!isBackground) setLoading(false)
        }
    }

    // Real-time polling
    useEffect(() => {
        const interval = setInterval(() => {
            if (currentPath && !loading) {
                fetchFolder(currentPath, true)
            }
        }, 15000)

        return () => clearInterval(interval)
    }, [currentPath, loading])

    // Listen for file changes (uploads, deletes, etc.)
    useEffect(() => {
        const handleFilesChanged = () => {
            // ... existing code
            fetchFolder(currentPath)
        }
        window.addEventListener('files-changed', handleFilesChanged)
        return () => window.removeEventListener('files-changed', handleFilesChanged)
    }, [currentPath])

    const handleActionConfirm = async (payload: { action: FileActionType, name: string }) => {
        const { action, name } = payload
        const targetFile = actionModal?.target
        if (!targetFile && (action === 'rename' || action === 'delete')) return

        try {
            let itemPath = currentPath
            if (targetFile) {
                if (targetFile.path !== undefined) {
                    itemPath = targetFile.path
                } else {
                    itemPath = fileUtils.joinPath(currentPath, targetFile.name || "")
                }
            }

            let result;
            if (action === 'rename') {
                const parent = fileUtils.getParentPath(itemPath)
                const newPath = fileUtils.joinPath(parent, name)
                result = await fileService.rename(itemPath, newPath)
            } else if (action === 'delete') {
                result = await fileService.delete(itemPath)
            } else if (action === 'new-folder') {
                result = await fileService.mkdir(itemPath, name)
            } else if (action === 'new-file') {
                result = await fileService.create(itemPath, name)
            }

            if (result && result.error) {
                alert(result.error)
            } else {
                // Close editor if deleting or renaming the currently open file
                if ((action === 'delete' || action === 'rename') && selectedFile) {
                    const selectedPath = selectedFile.path || fileUtils.joinPath(currentPath, selectedFile.name)
                    if (selectedPath === itemPath) {
                        setFileContent(null)
                        setSelectedFile(null)
                    }
                }

                // Dispatch global event so Sidebar and others can refresh
                window.dispatchEvent(new CustomEvent('files-changed', { detail: { path: itemPath, action } }))
                fetchFolder(currentPath)
            }
        } catch (err) {
            console.error("File action failed:", err)
            alert("An error occurred. Please try again.")
        }
        setActionModal(null)
    }

    const openFile = async (file: any) => {
        setIsFileLoading(true)
        try {
            const fullPath = file.path || fileUtils.joinPath(currentPath, file.name)

            // Set the selected file state so other parts of UI (like details sidebar) know what's open
            setSelectedFile({ ...file, path: fullPath })

            const data = await fileService.readFile(fullPath)
            if (data.error) {
                alert(data.error)
            } else {
                setFileContent(data.content)
                setIsImage(data.is_image)
            }
        } catch (err) {
            console.error("Failed to read file", err)
            alert("Failed to read file content.")
        } finally {
            setIsFileLoading(false)
        }
    }

    const handleNavigate = (path: string) => {
        setFileContent(null)
        setSelectedFile(null)
        fetchFolder(path)
    }

    const [searchQuery, setSearchQuery] = useState('')
    const [searchResults, setSearchResults] = useState<any[] | null>(null)

    useEffect(() => {
        if (!searchQuery.trim()) {
            setSearchResults(null)
            return
        }

        const timer = setTimeout(async () => {
            setLoading(true)
            try {
                // Pass true for recursive if needed, but endpoint will handle it
                const res = await fileService.search(currentPath, searchQuery)
                setSearchResults(res.files || [])
            } catch (err) {
                console.error("Search failed", err)
            } finally {
                setLoading(false)
            }
        }, 300)

        return () => clearTimeout(timer)
    }, [searchQuery, currentPath])

    const displayedFiles = useMemo(() => {
        // If we have search results, use them. Otherwise use current folder files.
        const source = searchResults !== null ? searchResults : files

        let filtered = [...source]
            .filter(f => showHiddenFiles || !f.name.startsWith('.'))

        return filtered.sort((a, b) => {
            if (showFoldersFirst) {
                if (a.is_dir === b.is_dir) {
                    return a.name.localeCompare(b.name)
                }
                return a.is_dir ? -1 : 1
            }
            return a.name.localeCompare(b.name)
        })
    }, [files, searchResults, showHiddenFiles, showFoldersFirst])

    const pathParts = useMemo(() => currentPath.split('/').filter(p => p), [currentPath])

    useEffect(() => {
        fetchFolder(initialPath)
    }, [initialPath])

    const handleSaveFile = async (content: string) => {
        if (!selectedFile) return
        setIsFileLoading(true)
        try {
            const fullPath = selectedFile.path || fileUtils.joinPath(currentPath, selectedFile.name)
            const data = await fileService.saveFile(fullPath, content)
            if (data.error) {
                alert(data.error)
            } else {
                setFileContent(content)
                // Optional: show a toast or message
            }
        } catch (err) {
            console.error("Failed to save file", err)
            alert("Failed to save file.")
        } finally {
            setIsFileLoading(false)
        }
    }

    const [clipboard, setClipboard] = useState<{ path: string, mode: 'copy' | 'cut' } | null>(null)

    const handleCopy = (path: string) => {
        setClipboard({ path, mode: 'copy' })
    }

    const handleCut = (path: string) => {
        setClipboard({ path, mode: 'cut' })
    }

    const handlePaste = async () => {
        if (!clipboard) return

        try {
            const fileName = clipboard.path.split('/').pop() || 'unknown'
            const destPath = fileUtils.joinPath(currentPath, fileName)

            setLoading(true)
            if (clipboard.mode === 'copy') {
                await fileService.copy(clipboard.path, destPath)
            } else {
                await fileService.rename(clipboard.path, destPath)
                setClipboard(null)
            }
            fetchFolder(currentPath)
        } catch (err) {
            console.error("Paste failed", err)
            alert("Paste failed")
        } finally {
            setLoading(false)
        }
    }

    const handleCompress = async (items: string[], destName: string, format: string) => {
        setLoading(true)
        try {
            await fileService.compress(currentPath, items, destName, format)
            fetchFolder(currentPath)
        } catch (err) {
            console.error("Compress failed", err)
            alert("Compress failed")
        } finally {
            setLoading(false)
        }
    }

    const handleExtract = async (archivePath: string) => {
        setLoading(true)
        try {
            await fileService.extract(archivePath, currentPath)
            fetchFolder(currentPath)
        } catch (err) {
            console.error("Extract failed", err)
            alert("Extract failed")
        } finally {
            setLoading(false)
        }
    }

    return {
        currentPath,
        files,
        loading,
        selectedFile,
        setSelectedFile,
        showFoldersFirst,
        setShowFoldersFirst,
        showHiddenFiles,
        setShowHiddenFiles,
        openMenuFile,
        setOpenMenuFile,
        viewMode,
        setViewMode,
        actionModal,
        setActionModal,
        fileContent,
        setFileContent,
        isFileLoading,
        isImage,
        handleActionConfirm,
        handleNavigate,
        openFile,
        handleSaveFile,
        displayedFiles,
        pathParts,
        searchQuery,
        setSearchQuery,
        isTopLevel,
        clipboard,
        handleCopy,
        handleCut,
        handlePaste,
        handleCompress,
        handleExtract
    }
}
