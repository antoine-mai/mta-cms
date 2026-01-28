import { useFileExplorer } from '../context'
import { fileUtils } from '../services/utils'
import { uploadToFolder } from '../services/upload'
import { FileMenuTrigger } from './trigger'

export const BrowserView = () => {
    const {
        currentPath,
        loading,
        selectedFile,
        setSelectedFile,
        openMenuFile,
        setOpenMenuFile,
        viewMode,
        setViewMode,
        setActionModal,
        handleNavigate,
        openFile,
        displayedFiles,
        pathParts,
        searchQuery,
        isTopLevel,
        clipboard,
        handleCopy,
        handleCut,
        handlePaste,
        handleCompress,
        handleExtract
    } = useFileExplorer()

    const handleFileDoubleClick = (file: any) => {
        if (file.is_dir) {
            handleNavigate(fileUtils.joinPath(currentPath, file.name))
        } else {
            openFile(file)
        }
    }

    const onCompressFile = (file: any) => {
        const destName = prompt("Enter archive name (e.g. archive.zip):", `${file.name}.zip`)
        if (destName) {
            // Determine format from name or default to zip
            const format = destName.endsWith('.tar.gz') || destName.endsWith('.tgz') ? 'tar.gz' :
                destName.endsWith('.tar') ? 'tar' : 'zip'
            handleCompress([file.name], destName, format)
        }
    }

    return (
        <div className="flex-1 overflow-y-auto p-6 lg:p-8" onClick={() => setSelectedFile(null)}>
            <div className="max-w-7xl mx-auto">
                <section>
                    <div className="flex items-center justify-between mb-6">
                        <div className="flex flex-col gap-1">
                            <h3 className="text-text-main font-bold text-base">Current Path</h3>
                            <div className="flex items-center gap-1 text-xs text-text-muted">
                                <span>root</span>
                                {pathParts.map((part, i) => (
                                    <span key={i} className="flex items-center gap-1">
                                        <span className="material-symbols-outlined text-[14px]">chevron_right</span>
                                        <span
                                            className="hover:text-primary cursor-pointer transition-colors"
                                            onClick={(e) => { e.stopPropagation(); handleNavigate('/' + pathParts.slice(0, i + 1).join('/')) }}
                                        >
                                            {part}
                                        </span>
                                    </span>
                                ))}
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="text-[13px] text-text-muted font-medium">{displayedFiles.length} items</div>
                            <div className="flex items-center gap-2">
                                <div className="flex p-0.5 rounded-md">
                                    <button
                                        onClick={(e) => { e.stopPropagation(); setViewMode('grid'); }}
                                        className={`p-1.5 transition-all ${viewMode === 'grid' ? 'text-primary' : 'text-text-secondary hover:text-text-main'}`}
                                    >
                                        <span className="material-symbols-outlined text-[20px] leading-none translate-y-[1px] block">grid_view</span>
                                    </button>
                                    <button
                                        onClick={(e) => { e.stopPropagation(); setViewMode('list'); }}
                                        className={`p-1.5 transition-all ${viewMode === 'list' ? 'text-primary' : 'text-text-secondary hover:text-text-main'}`}
                                    >
                                        <span className="material-symbols-outlined text-[20px] leading-none translate-y-[0px] block">format_list_bulleted</span>
                                    </button>
                                </div>

                                {clipboard && (
                                    <button
                                        onClick={(e) => {
                                            e.stopPropagation()
                                            handlePaste()
                                        }}
                                        className="flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-md transition-all shadow-sm"
                                        title={`Paste ${clipboard.mode === 'cut' ? 'cut' : 'copied'} item here`}
                                    >
                                        <span className="material-symbols-outlined text-[16px]">content_paste</span>
                                        Paste
                                    </button>
                                )}

                                <button
                                    onClick={(e) => {
                                        e.stopPropagation()
                                        uploadToFolder(currentPath, () => window.dispatchEvent(new CustomEvent('files-changed')))
                                    }}
                                    className="flex items-center gap-1.5 px-3 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-md transition-all shadow-sm"
                                >
                                    <span className="material-symbols-outlined text-[16px]">upload</span>
                                    Upload
                                </button>
                            </div>
                        </div>
                    </div>

                    {loading ? (
                        <div className="flex items-center justify-center py-20">
                            <span className="material-symbols-outlined animate-spin text-primary text-3xl">progress_activity</span>
                        </div>
                    ) : (
                        viewMode === 'grid' ? (
                            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                                {(!searchQuery && !isTopLevel) && (
                                    <div
                                        className="p-4 rounded-xl border bg-card-bg border-border-light hover:bg-primary/5 hover:border-primary/40 hover:shadow-md transition-all cursor-pointer group flex flex-col items-center justify-center text-center relative"
                                        onClick={(e) => { e.stopPropagation(); handleNavigate(fileUtils.getParentPath(currentPath)); }}
                                    >
                                        <span className="material-symbols-outlined text-[48px] text-text-muted mb-3">reply</span>
                                        <h4 className="text-sm font-medium text-text-main truncate w-full px-2">..</h4>
                                        <div className="text-xs text-text-secondary mt-1">Back</div>
                                    </div>
                                )}
                                {displayedFiles.map((file, idx) => (
                                    <div
                                        key={idx}
                                        className={`p-4 rounded-xl border transition-all cursor-pointer group flex flex-col items-center justify-center text-center relative
                                            ${selectedFile?.name === file.name ? 'bg-primary/5 border-primary ring-1 ring-primary' : 'bg-card-bg border-border-light hover:bg-primary/5 hover:border-primary/40 hover:shadow-md'}
                                        `}
                                        onClick={(e) => { e.stopPropagation(); setSelectedFile(file); }}
                                        onDoubleClick={(e) => { e.stopPropagation(); handleFileDoubleClick(file); }}
                                    >
                                        <div className={`absolute top-2 right-2 ${openMenuFile === file.name ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'} transition-opacity`}>
                                            <FileMenuTrigger
                                                isOpen={openMenuFile === file.name}
                                                onToggle={() => setOpenMenuFile(openMenuFile === file.name ? null : file.name)}
                                                onClose={() => setOpenMenuFile(null)}
                                                isDirectory={file.is_dir}
                                                onEdit={() => openFile(file)}
                                                onRename={() => setActionModal({ type: 'rename', target: file })}
                                                onDelete={() => setActionModal({ type: 'delete', target: file })}
                                                onNewFolder={() => setActionModal({ type: 'new-folder', target: file })}
                                                onNewFile={() => setActionModal({ type: 'new-file', target: file })}
                                                onDownload={() => console.log("Download", file.name)}
                                                onUpload={() => uploadToFolder(fileUtils.joinPath(currentPath, file.name), () => window.dispatchEvent(new CustomEvent('files-changed')))}
                                                onCopy={() => handleCopy(file.path || fileUtils.joinPath(currentPath, file.name))}
                                                onCut={() => handleCut(file.path || fileUtils.joinPath(currentPath, file.name))}
                                                onCompress={() => onCompressFile(file)}
                                                onExtract={(!file.is_dir && (file.name.endsWith('.zip') || file.name.endsWith('.tar.gz') || file.name.endsWith('.tar'))) ? () => handleExtract(file.path || fileUtils.joinPath(currentPath, file.name)) : undefined}
                                            />
                                        </div>

                                        {file.is_dir ? (
                                            <span className="material-symbols-outlined text-[48px] text-amber-500 mb-3" style={{ fontVariationSettings: "'FILL' 1" }}>folder</span>
                                        ) : (
                                            <span className={`material-symbols-outlined text-[48px] mb-3 ${file.is_image ? 'text-purple-500' : 'text-blue-500'}`}>
                                                {file.icon || 'description'}
                                            </span>
                                        )}
                                        <h4 className="text-sm font-medium text-text-main truncate w-full px-2" title={file.name}>{file.name}</h4>
                                        <div className="text-xs text-text-secondary mt-1">{file.is_dir ? 'Folder' : fileUtils.formatSize(file.size)}</div>
                                    </div>
                                ))}
                                {displayedFiles.length === 0 && (
                                    <div className="col-span-full py-10 text-center text-text-muted italic">This folder is empty</div>
                                )}
                            </div>
                        ) : (
                            <div className="flex flex-col border border-border-light overflow-hidden bg-card-bg">
                                <div className="grid grid-cols-[1fr,100px,100px,100px,80px,160px,40px] px-4 py-2 bg-background-light border-b border-border-light text-xs font-bold text-text-secondary uppercase tracking-wider">
                                    <div>Name</div>
                                    <div>Permission</div>
                                    <div>Owner</div>
                                    <div>Group</div>
                                    <div>Size</div>
                                    <div>Modified</div>
                                    <div></div>
                                </div>
                                <div className="divide-y divide-border-light">
                                    {(!searchQuery && !isTopLevel) && (
                                        <div
                                            className="grid grid-cols-[1fr,100px,100px,100px,80px,160px,40px] items-center px-4 py-2 transition-all cursor-pointer group hover:bg-primary/5"
                                            onClick={(e) => { e.stopPropagation(); handleNavigate(fileUtils.getParentPath(currentPath)); }}
                                        >
                                            <div className="flex items-center gap-3 overflow-hidden">
                                                <span className="material-symbols-outlined text-[20px] text-text-muted">reply</span>
                                                <span className="text-sm font-medium text-text-main truncate">..</span>
                                            </div>
                                            <div className="text-sm text-text-secondary font-mono"></div>
                                            <div className="text-sm text-text-secondary truncate pr-2"></div>
                                            <div className="text-sm text-text-secondary truncate pr-2"></div>
                                            <div className="text-sm text-text-secondary"></div>
                                            <div className="text-sm text-text-secondary"></div>
                                            <div className="flex justify-end"></div>
                                        </div>
                                    )}
                                    {displayedFiles.map((file, idx) => (
                                        <div
                                            key={idx}
                                            className={`grid grid-cols-[1fr,100px,100px,100px,80px,160px,40px] items-center px-4 py-2 transition-all cursor-pointer group hover:bg-primary/5
                                                ${selectedFile?.name === file.name ? 'bg-primary/10' : ''}
                                            `}
                                            onClick={(e) => { e.stopPropagation(); setSelectedFile(file); }}
                                            onDoubleClick={(e) => { e.stopPropagation(); handleFileDoubleClick(file); }}
                                        >
                                            <div className="flex items-center gap-3 overflow-hidden">
                                                <span className={`material-symbols-outlined text-[20px] ${file.is_dir ? 'text-amber-500' : (file.is_image ? 'text-purple-500' : 'text-blue-500')}`} style={file.is_dir ? { fontVariationSettings: "'FILL' 1" } : {}}>
                                                    {file.is_dir ? 'folder' : (file.icon || 'description')}
                                                </span>
                                                <span className="text-sm font-medium text-text-main truncate" title={file.path || file.name}>
                                                    {(searchQuery && file.path) ? file.path : file.name}
                                                </span>
                                            </div>
                                            <div className="text-sm text-text-secondary font-mono">{file.mode}</div>
                                            <div className="text-sm text-text-secondary truncate pr-2" title={file.owner}>{file.owner}</div>
                                            <div className="text-sm text-text-secondary truncate pr-2" title={file.group}>{file.group}</div>
                                            <div className="text-sm text-text-secondary">{file.is_dir ? '-' : fileUtils.formatSize(file.size)}</div>
                                            <div className="text-sm text-text-secondary">{fileUtils.formatDate(file.mod_time)}</div>
                                            <div className="flex justify-end">
                                                <div className={`${openMenuFile === file.name ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'} transition-opacity`}>
                                                    <FileMenuTrigger
                                                        isOpen={openMenuFile === file.name}
                                                        onToggle={() => setOpenMenuFile(openMenuFile === file.name ? null : file.name)}
                                                        onClose={() => setOpenMenuFile(null)}
                                                        isDirectory={file.is_dir}
                                                        onEdit={() => openFile(file)}
                                                        onRename={() => setActionModal({ type: 'rename', target: file })}
                                                        onDelete={() => setActionModal({ type: 'delete', target: file })}
                                                        onNewFolder={() => setActionModal({ type: 'new-folder', target: file })}
                                                        onNewFile={() => setActionModal({ type: 'new-file', target: file })}
                                                        onDownload={() => console.log("Download", file.name)}
                                                        onUpload={() => uploadToFolder(fileUtils.joinPath(currentPath, file.name), () => window.dispatchEvent(new CustomEvent('files-changed')))}
                                                        onCopy={() => handleCopy(file.path || fileUtils.joinPath(currentPath, file.name))}
                                                        onCut={() => handleCut(file.path || fileUtils.joinPath(currentPath, file.name))}
                                                        onCompress={() => onCompressFile(file)}
                                                        onExtract={(!file.is_dir && (file.name.endsWith('.zip') || file.name.endsWith('.tar.gz') || file.name.endsWith('.tar'))) ? () => handleExtract(file.path || fileUtils.joinPath(currentPath, file.name)) : undefined}
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    {displayedFiles.length === 0 && (
                                        <div className="py-10 text-center text-text-muted italic">This folder is empty</div>
                                    )}
                                </div>
                            </div>
                        )
                    )}
                </section>
            </div>
        </div>
    )
}
