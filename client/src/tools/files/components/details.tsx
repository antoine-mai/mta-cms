import { useFileExplorer } from '../context'
import { fileUtils } from '../services/utils'
import { FileMenuTrigger } from './trigger'

export const DetailsView = () => {
    const {
        currentPath,
        selectedFile,
        openMenuFile,
        setOpenMenuFile,
        setActionModal,
        openFile,
        displayedFiles,
        pathParts
    } = useFileExplorer()

    return (
        <aside className="w-[300px] border-l border-border-light bg-background-main flex flex-col shrink-0 overflow-y-auto z-10 hidden lg:flex">
            {selectedFile ? (
                <div className="p-6 flex flex-col gap-6 h-full relative">
                    <div className="absolute top-4 right-4 z-10">
                        <FileMenuTrigger
                            isOpen={openMenuFile === 'details-selected'}
                            onToggle={() => setOpenMenuFile(openMenuFile === 'details-selected' ? null : 'details-selected')}
                            onClose={() => setOpenMenuFile(null)}
                            isDirectory={selectedFile.is_dir}
                            onEdit={() => openFile(selectedFile)}
                            onRename={() => setActionModal({ type: 'rename', target: selectedFile })}
                            onDelete={() => setActionModal({ type: 'delete', target: selectedFile })}
                            onNewFolder={() => setActionModal({ type: 'new-folder', target: selectedFile })}
                            onNewFile={() => setActionModal({ type: 'new-file', target: selectedFile })}
                            onDownload={() => console.log("Download", selectedFile.name)}
                        />
                    </div>
                    <div className="w-full aspect-square bg-background-light rounded-xl border border-border-light flex flex-col items-center justify-center p-4">
                        <span className={`material-symbols-outlined text-6xl ${selectedFile.is_dir ? 'text-amber-500' : 'text-blue-500'}`} style={selectedFile.is_dir ? { fontVariationSettings: "'FILL' 1" } : {}}>
                            {selectedFile.is_dir ? 'folder' : (selectedFile.icon || 'description')}
                        </span>
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-text-main break-all">{selectedFile.name}</h3>
                        <p className="text-xs text-text-secondary mt-1">{selectedFile.is_dir ? 'Directory' : 'File'}</p>
                    </div>
                    <div className="pt-4 border-t border-border-light space-y-3">
                        <div className="flex justify-between text-xs">
                            <span className="text-text-secondary">Size</span>
                            <span className="text-text-main font-medium">{selectedFile.is_dir ? '-' : fileUtils.formatSize(selectedFile.size)}</span>
                        </div>
                        <div className="flex justify-between text-xs">
                            <span className="text-text-secondary">Modified</span>
                            <span className="text-text-main font-medium">{fileUtils.formatDate(selectedFile.mod_time)}</span>
                        </div>
                        <div className="flex justify-between text-xs">
                            <span className="text-text-secondary">Owner</span>
                            <span className="text-text-main font-medium truncate ml-4" title={selectedFile.owner}>{selectedFile.owner}</span>
                        </div>
                        <div className="flex justify-between text-xs">
                            <span className="text-text-secondary">Group</span>
                            <span className="text-text-main font-medium truncate ml-4" title={selectedFile.group}>{selectedFile.group}</span>
                        </div>
                        <div className="flex justify-between text-xs font-mono">
                            <span className="text-text-secondary font-sans leading-none pt-0.5">Permission</span>
                            <span className="text-text-main font-medium">{selectedFile.mode}</span>
                        </div>
                    </div>
                </div>
            ) : (
                <div className="p-6 flex flex-col gap-6 h-full">
                    <div className="w-full aspect-square bg-background-light rounded-xl border border-border-light flex flex-col items-center justify-center p-4">
                        <span className="material-symbols-outlined text-6xl text-amber-500" style={{ fontVariationSettings: "'FILL' 1" }}>
                            folder
                        </span>
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-text-main break-all">{pathParts.length > 0 ? pathParts[pathParts.length - 1] : 'Home'}</h3>
                        <p className="text-xs text-text-secondary mt-1">Directory</p>
                    </div>
                    <div className="pt-4 border-t border-border-light space-y-3">
                        <div className="flex justify-between text-xs">
                            <span className="text-text-secondary">Items</span>
                            <span className="text-text-main font-medium">{displayedFiles.length} items</span>
                        </div>
                        <div className="flex justify-between text-xs">
                            <span className="text-text-secondary">Location</span>
                            <span className="text-text-main font-medium text-right break-all">{currentPath || '/'}</span>
                        </div>
                    </div>
                </div>
            )}
        </aside>
    )
}
