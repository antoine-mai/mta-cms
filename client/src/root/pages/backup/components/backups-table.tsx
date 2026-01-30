import {
    RefreshCw,
    Database,
    FolderArchive,
    CheckCircle2,
    Download,
    Trash2
} from 'lucide-react'
import type { Backup } from '../types'

interface BackupsTableProps {
    loading: boolean
    backups: Backup[]
    onDownload: (backup: Backup) => void
    onDelete: (backup: Backup) => void
}

export function BackupsTable({ loading, backups, onDownload, onDelete }: BackupsTableProps) {
    return (
        <div className="px-6 pb-6 overflow-hidden">
            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl flex flex-col overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    <h2 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <FolderArchive className="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        Available Backups
                    </h2>
                </div>

                <div className="overflow-x-auto">
                    {loading ? (
                        <div className="flex items-center justify-center py-20">
                            <RefreshCw className="w-8 h-8 animate-spin text-indigo-600 dark:text-indigo-400" />
                        </div>
                    ) : backups.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-20">
                            <Database className="w-12 h-12 text-slate-200 dark:text-slate-700 mb-4" />
                            <p className="text-sm text-slate-500">No backups found.</p>
                        </div>
                    ) : (
                        <table className="w-full text-left border-collapse">
                            <thead className="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th className="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                    <th className="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Name</th>
                                    <th className="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Size</th>
                                    <th className="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {backups.map((backup) => (
                                    <tr key={backup.id} className="group hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-colors">
                                        <td className="px-6 py-4">
                                            {backup.status === 'completed' ? (
                                                <CheckCircle2 className="w-5 h-5 text-green-500" />
                                            ) : (
                                                <RefreshCw className="w-5 h-5 text-blue-500 animate-spin" />
                                            )}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="text-sm font-bold text-slate-900 dark:text-white">{backup.name}</div>
                                            <div className="text-[10px] text-slate-500">{backup.date} at {backup.time}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="text-xs font-mono text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">
                                                {backup.size}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button onClick={() => onDownload(backup)} className="p-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg transition-colors" title="Download">
                                                    <Download className="w-4 h-4" />
                                                </button>
                                                <button onClick={() => onDelete(backup)} className="p-2 hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg transition-colors" title="Delete">
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </div>
    )
}
