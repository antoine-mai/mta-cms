import { BarChart3 } from 'lucide-react'

export default function AnalyticsPage() {
    return (
        <div className="h-full flex flex-col">
            {/* Header */}
            <div className="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl">
                        <BarChart3 className="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900 dark:text-white">Analytics</h1>
                        <p className="text-sm text-slate-500 dark:text-slate-400">View site analytics and statistics</p>
                    </div>
                </div>
            </div>

            {/* Content */}
            <div className="flex-1 overflow-y-auto p-6">
                <div className="max-w-6xl">
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6">
                        <div className="flex flex-col items-center justify-center py-20">
                            <div className="w-20 h-20 bg-slate-100 dark:bg-slate-900 rounded-2xl flex items-center justify-center mb-4">
                                <BarChart3 className="w-10 h-10 text-slate-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-slate-900 dark:text-white mb-2">Analytics Coming Soon</h3>
                            <p className="text-sm text-slate-500 dark:text-slate-400">
                                View detailed analytics for all your sites
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
