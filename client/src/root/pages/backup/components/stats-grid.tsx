import type { LucideIcon } from 'lucide-react'

interface Stat {
    icon: LucideIcon
    label: string
    value: string
    color: string
}

interface StatsGridProps {
    stats: Stat[]
}

export function StatsGrid({ stats }: StatsGridProps) {
    return (
        <div className="px-6 py-6">
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {stats.map((stat, index) => (
                    <div
                        key={index}
                        className="group relative bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 lg:p-6 hover:shadow-xl transition-all duration-300 overflow-hidden"
                    >
                        <div className={`absolute inset-0 bg-gradient-to-br ${stat.color} opacity-0 group-hover:opacity-5 transition-opacity`}></div>
                        <div className="relative flex items-center justify-between">
                            <div>
                                <div className="text-[10px] lg:text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{stat.label}</div>
                                <div className="text-xl lg:text-2xl font-bold text-slate-900 dark:text-white">{stat.value}</div>
                            </div>
                            <div className={`p-2 lg:p-3 bg-gradient-to-br ${stat.color} rounded-xl shadow-lg`}>
                                <stat.icon className="w-4 h-4 lg:w-5 lg:h-5 text-white" />
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    )
}
