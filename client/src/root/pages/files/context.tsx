import { createContext, useContext, useState, type ReactNode } from 'react'
import type { FileNode } from './types'

interface ClipboardState {
    file: FileNode
    mode: 'copy' | 'cut'
}

interface FilesContextType {
    clipboard: ClipboardState | null
    setClipboard: (state: ClipboardState | null) => void
    refresh: () => void
    refreshVersion: number
}

const FilesContext = createContext<FilesContextType | undefined>(undefined)

export function FilesProvider({ children, onRefresh }: { children: ReactNode, onRefresh: () => void }) {
    const [clipboard, setClipboard] = useState<ClipboardState | null>(null)
    const [refreshVersion, setRefreshVersion] = useState(0)

    const handleRefresh = () => {
        setRefreshVersion(v => v + 1)
        onRefresh()
    }

    return (
        <FilesContext.Provider value={{ clipboard, setClipboard, refresh: handleRefresh, refreshVersion }}>
            {children}
        </FilesContext.Provider>
    )
}

export function useFiles() {
    const context = useContext(FilesContext)
    if (!context) throw new Error('useFiles must be used within FilesProvider')
    return context
}
