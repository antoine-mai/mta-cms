import { useRef, useEffect } from "react"
import { createPortal } from "react-dom"

interface FileMenuProps {
    isOpen: boolean
    onClose: () => void
    anchorRef: React.RefObject<HTMLElement | null>
    onRename?: () => void
    onDownload?: () => void
    onDelete?: () => void
    onEdit?: () => void
    isDirectory?: boolean
    onNewFolder?: () => void
    onNewFile?: () => void
    onUpload?: () => void
    onCopy?: () => void
    onCut?: () => void
    onCompress?: () => void
    onExtract?: () => void
}

export default function FileMenu({ isOpen, onClose, anchorRef, onRename, onDownload, onDelete, onEdit, isDirectory, onNewFolder, onNewFile, onUpload, onCopy, onCut, onCompress, onExtract }: FileMenuProps) {
    const menuRef = useRef<HTMLDivElement>(null)

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                menuRef.current &&
                !menuRef.current.contains(event.target as Node) &&
                anchorRef.current &&
                !anchorRef.current.contains(event.target as Node)
            ) {
                onClose()
            }
        }

        if (isOpen) {
            document.addEventListener("mousedown", handleClickOutside)
        }
        return () => {
            document.removeEventListener("mousedown", handleClickOutside)
        }
    }, [isOpen, onClose])

    if (!isOpen || !anchorRef.current) return null

    const rect = anchorRef.current.getBoundingClientRect()
    // Simple positioning logic; for strict production use Popper.js
    const style: React.CSSProperties = {
        position: "fixed",
        top: rect.bottom + 4,
        left: rect.right - 148, // slightly wider for new options
        zIndex: 9999,
    }

    return createPortal(
        <div ref={menuRef} style={style} className="w-40 bg-card-bg rounded-lg shadow-xl border border-border-light animate-in fade-in zoom-in-95 duration-100 overflow-hidden text-left">
            <div className="py-1">
                {isDirectory && (
                    <>
                        <button
                            className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                            onClick={(e) => { e.stopPropagation(); onNewFolder?.(); onClose(); }}
                        >
                            <span className="material-symbols-outlined text-[14px] text-text-secondary">create_new_folder</span>
                            New Folder
                        </button>
                        <button
                            className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                            onClick={(e) => { e.stopPropagation(); onNewFile?.(); onClose(); }}
                        >
                            <span className="material-symbols-outlined text-[14px] text-text-secondary">note_add</span>
                            New File
                        </button>
                        <button
                            className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors border-b border-border-light mb-1"
                            onClick={(e) => { e.stopPropagation(); onUpload?.(); onClose(); }}
                        >
                            <span className="material-symbols-outlined text-[14px] text-text-secondary">upload</span>
                            Upload
                        </button>
                    </>
                )}
                {!isDirectory && (
                    <button
                        className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                        onClick={(e) => { e.stopPropagation(); onEdit?.(); onClose(); }}
                    >
                        <span className="material-symbols-outlined text-[14px] text-text-secondary">edit_note</span>
                        Edit
                    </button>
                )}

                <button
                    className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                    onClick={(e) => { e.stopPropagation(); onCopy?.(); onClose(); }}
                >
                    <span className="material-symbols-outlined text-[14px] text-text-secondary">content_copy</span>
                    Copy
                </button>
                <button
                    className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                    onClick={(e) => { e.stopPropagation(); onCut?.(); onClose(); }}
                >
                    <span className="material-symbols-outlined text-[14px] text-text-secondary">content_cut</span>
                    Cut
                </button>

                <div className="my-1 border-t border-border-light" />

                <button
                    className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                    onClick={(e) => { e.stopPropagation(); onCompress?.(); onClose(); }}
                >
                    <span className="material-symbols-outlined text-[14px] text-text-secondary">archive</span>
                    Compress
                </button>
                {/* Only show Extract if it's an archive */}
                {onExtract && (
                    <button
                        className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                        onClick={(e) => { e.stopPropagation(); onExtract(); onClose(); }}
                    >
                        <span className="material-symbols-outlined text-[14px] text-text-secondary">unarchive</span>
                        Extract
                    </button>
                )}

                <div className="my-1 border-t border-border-light" />

                <button
                    className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                    onClick={(e) => { e.stopPropagation(); onRename?.(); onClose(); }}
                >
                    <span className="material-symbols-outlined text-[14px] text-text-secondary">edit</span>
                    Rename
                </button>
                <button
                    className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-text-main hover:bg-background-light text-left transition-colors"
                    onClick={(e) => { e.stopPropagation(); onDownload?.(); onClose(); }}
                >
                    <span className="material-symbols-outlined text-[14px] text-text-secondary">download</span>
                    Download
                </button>
                <button
                    className="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 text-left transition-colors"
                    onClick={(e) => { e.stopPropagation(); onDelete?.(); onClose(); }}
                >
                    <span className="material-symbols-outlined text-[14px]">delete</span>
                    Delete
                </button>
            </div>
        </div>,
        document.body
    )
}
