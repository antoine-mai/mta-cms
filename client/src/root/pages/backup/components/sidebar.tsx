import { useLocation, Link } from 'react-router-dom'
import { Database, Settings, Cloud } from 'lucide-react'

interface SidebarItemProps {
    path: string
    icon: React.ComponentType<{ size?: number; className?: string }>
    title: string
}

function SidebarItem({ path, icon: Icon, title }: SidebarItemProps) {
    const location = useLocation()
    const isActive = path === '/backup'
        ? location.pathname === '/backup' || location.pathname === '/backup/'
        : location.pathname === path

    return (
        <Link
            to={path}
            className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium ${isActive
                ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
        >
            <Icon size={18} className={isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'} />
            <span>{title}</span>
        </Link>
    )
}

export default function Sidebar() {
    return (
        <aside className="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col h-full">
            <div className="p-4 border-b border-slate-200 dark:border-slate-800">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl">
                        <Database className="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h2 className="text-lg font-bold text-slate-900 dark:text-white">Backup</h2>
                        <p className="text-xs text-slate-500 dark:text-slate-400">Management</p>
                    </div>
                </div>
            </div>

            <div className="flex-1 overflow-y-auto p-4">
                <div className="space-y-1">
                    <SidebarItem path="/backup" icon={Database} title="Backups" />
                    <SidebarItem path="/backup/cloud-settings" icon={Cloud} title="Cloud Settings" />
                    <SidebarItem path="/backup/settings" icon={Settings} title="Settings" />
                </div>
            </div>
        </aside>
    )
}
