import { useRef } from 'react'
import FileMenu from './menu'

interface FileMenuTriggerProps {
    isOpen: boolean
    onToggle: () => void
    onClose: () => void
    isDirectory?: boolean
    onRename?: () => void
    onDelete?: () => void
    onDownload?: () => void
    onEdit?: () => void
    onNewFolder?: () => void
    onNewFile?: () => void
    onUpload?: () => void
    onCopy?: () => void
    onCut?: () => void
    onCompress?: () => void
    onExtract?: () => void
}

export const FileMenuTrigger = (props: FileMenuTriggerProps) => {
    const btnRef = useRef<HTMLButtonElement>(null)

    return (
        <>
            <button
                ref={btnRef}
                className={`p-1 rounded-md text-text-secondary hover:text-text-main transition-colors ${props.isOpen ? 'text-text-main' : ''}`}
                onClick={(e) => {
                    e.stopPropagation()
                    props.onToggle()
                }}
            >
                <span className="material-symbols-outlined text-[20px]">more_vert</span>
            </button>
            <FileMenu
                isOpen={props.isOpen}
                onClose={props.onClose}
                anchorRef={btnRef}
                onRename={props.onRename}
                onDelete={props.onDelete}
                onDownload={props.onDownload}
                onEdit={props.onEdit}
                isDirectory={props.isDirectory}
                onNewFolder={props.onNewFolder}
                onNewFile={props.onNewFile}
                onUpload={props.onUpload}
                onCopy={props.onCopy}
                onCut={props.onCut}
                onCompress={props.onCompress}
                onExtract={props.onExtract}
            />
        </>
    )
}
