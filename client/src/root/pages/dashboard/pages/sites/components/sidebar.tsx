import { useLocation, Link } from 'react-router-dom'
import { useState, useEffect } from 'react'
import { Globe, Settings, BarChart3, Plus } from 'lucide-react'

interface SidebarItemProps {
    path: string
    icon: React.ComponentType<{ size?: number; className?: string }>
    title: string
}

function SidebarItem({ path, icon: Icon, title }: SidebarItemProps) {
    const location = useLocation()
    const isActive = location.pathname === path || location.pathname.startsWith(path + '/')

    return (
        <Link
            to={path}
            className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium ${isActive
                ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
        >
            <Icon size={18} className={isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'} />
            <span className="truncate">{title}</span>
        </Link>
    )
}

export default function Sidebar() {
    const [sites, setSites] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        fetch('/root/post/sites/browse')
            .then(res => res.json())
            .then(data => {
                if (data.success) setSites(data.items || [])
            })
            .catch(err => console.error(err))
            .finally(() => setLoading(false))
    }, [])

    return (
        <aside className="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col h-full">
            <div className="flex-1 overflow-y-auto custom-scrollbar">
                {/* All Sites Section */}
                <div className="p-4">
                    <div className="px-3 mb-2 flex items-center justify-between">
                        <h3 className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">All Sites</h3>
                        <Link to="/dashboard/sites" className="hover:text-indigo-600 transition-colors" title="Manage All">
                            <Plus size={14} />
                        </Link>
                    </div>

                    <div className="space-y-1">
                        {loading ? (
                            <div className="px-3 py-2 text-xs text-slate-400 animate-pulse">Loading sites...</div>
                        ) : sites.length > 0 ? (
                            sites.map(site => (
                                <SidebarItem
                                    key={site.id}
                                    path={`/dashboard/sites/view/${site.id}`}
                                    icon={Globe}
                                    title={site.name}
                                />
                            ))
                        ) : (
                            <div className="px-3 py-4 text-center border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-xl">
                                <p className="text-[11px] text-slate-400 mb-2">No sites found</p>
                                <button className="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 mx-auto hover:underline">
                                    <Plus size={10} /> Create New
                                </button>
                            </div>
                        )}
                    </div>
                </div>

                {/* Management Section */}
                <div className="p-4 pt-0">
                    <div className="px-3 mb-2">
                        <h3 className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Management</h3>
                    </div>
                    <div className="space-y-1">
                        <SidebarItem path="/dashboard/sites/settings" icon={Settings} title="Settings" />
                        <SidebarItem path="/dashboard/sites/analytics" icon={BarChart3} title="Analytics" />
                    </div>
                </div>
            </div>
        </aside>
    )
}
