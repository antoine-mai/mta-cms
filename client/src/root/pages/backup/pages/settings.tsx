import { Settings as SettingsIcon } from 'lucide-react'

export default function SettingsPage() {
    return (
        <div className="flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden m-6 font-display">
            {/* Header */}
            <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900 flex-shrink-0">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl">
                        <SettingsIcon className="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h2 className="font-bold text-lg text-slate-900 dark:text-white">Backup Settings</h2>
                    </div>
                </div>
            </div>

            {/* Content area - Scrollable */}
            <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">

                <div className="max-w-4xl">
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6">
                        <div className="flex flex-col items-center justify-center py-20">
                            <div className="w-20 h-20 bg-slate-100 dark:bg-slate-900 rounded-2xl flex items-center justify-center mb-4">
                                <SettingsIcon className="w-10 h-10 text-slate-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-slate-900 dark:text-white mb-2">Settings Coming Soon</h3>
                            <p className="text-sm text-slate-500 dark:text-slate-400">
                                Configure backup schedule, retention, and other settings
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
