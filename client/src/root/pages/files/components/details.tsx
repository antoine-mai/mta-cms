import type { FileNode } from '../types'
import { isEditable } from '../utils'
import { FileText, Folder, Calendar, HardDrive, Edit3 } from 'lucide-react'

export function Details({ file, onEdit }: { file: FileNode | null, onEdit: (file: FileNode) => void }) {
    if (!file) {
        return (
            <div className="w-64 border-l border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center text-slate-400 p-4 text-center text-sm">
                Select an item to view details
            </div>
        )
    }

    return (
        <div className="w-64 bg-white dark:bg-slate-900 flex flex-col overflow-y-auto">
            <div className="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col items-center text-center">
                <div className={`p-4 mb-3 ${file.type === 'dir'
                    ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'
                    }`}>
                    {file.type === 'dir' ? <Folder size={32} /> : <FileText size={32} />}
                </div>
                <h3 className="font-semibold text-slate-900 dark:text-white break-words w-full">
                    {file.name}
                </h3>
                <span className="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wider font-medium">
                    {file.type === 'dir' ? 'Folder' : 'File'}
                </span>

                {/* Edit Button */}
                {file.type !== 'dir' && isEditable(file.name) && (
                    <button
                        onClick={() => onEdit(file)}
                        className="mt-4 w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 text-sm font-medium transition-colors shadow-sm"
                    >
                        <Edit3 size={16} />
                        Edit File
                    </button>
                )}
            </div>

            <div className="p-4">
                <h4 className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">
                    Information
                </h4>

                <div className="space-y-3">
                    <div className="flex items-start gap-3">
                        <HardDrive size={16} className="text-slate-400 mt-0.5" />
                        <div>
                            <div className="text-xs text-slate-500 dark:text-slate-400">Size</div>
                            <div className="text-sm font-medium text-slate-700 dark:text-slate-200">
                                {file.size || '-'}
                            </div>
                        </div>
                    </div>
                    <div className="flex items-start gap-3">
                        <Calendar size={16} className="text-slate-400 mt-0.5" />
                        <div>
                            <div className="text-xs text-slate-500 dark:text-slate-400">Modified</div>
                            <div className="text-sm font-medium text-slate-700 dark:text-slate-200">
                                {file.modified || '-'}
                            </div>
                        </div>
                    </div>
                    <div className="flex items-start gap-3">
                        <Folder size={16} className="text-slate-400 mt-0.5" />
                        <div>
                            <div className="text-xs text-slate-500 dark:text-slate-400">Path</div>
                            <div className="text-sm font-medium text-slate-700 dark:text-slate-200 break-words font-mono text-xs mt-1">
                                {file.path}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
