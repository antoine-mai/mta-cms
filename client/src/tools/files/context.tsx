import { createContext, useContext, type ReactNode } from 'react'
import { useFiles } from './hooks/use-files'

type FileExplorerContextType = ReturnType<typeof useFiles>

const FileExplorerContext = createContext<FileExplorerContextType | null>(null)

export function FileExplorerProvider({ children, initialPath }: { children: ReactNode, initialPath?: string }) {
    const explorer = useFiles(initialPath)
    return (
        <FileExplorerContext.Provider value={explorer}>
            {children}
        </FileExplorerContext.Provider>
    )
}

export function useFileExplorer() {
    const context = useContext(FileExplorerContext)
    if (!context) {
        throw new Error('useFileExplorer must be used within a FileExplorerProvider')
    }
    return context
}
