import { useLocation, Link } from 'react-router-dom'

export default function Sidebar() {
    const location = useLocation()
    const currentPath = location.pathname

    const menuItems = [
        { path: '/dashboard/overview', icon: 'dashboard', title: 'Overview' },
        { path: '/dashboard/cms', icon: 'article', title: 'CMS' },
        { path: '/dashboard/ecommerce', icon: 'shopping_cart', title: 'Ecommerce' }
    ]

    return (
        <aside className="w-[56px] bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col items-center py-4 gap-2">
            {menuItems.map((item) => {
                const isActive = currentPath === item.path || currentPath.startsWith(item.path + '/')

                return (
                    <Link
                        key={item.path}
                        to={item.path}
                        className={`size-10 flex items-center justify-center rounded-lg transition-colors ${isActive
                            ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400'
                            : 'text-slate-400 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        title={item.title}
                    >
                        <span className="material-symbols-outlined text-[20px]">{item.icon}</span>
                    </Link>
                )
            })}
        </aside>
    )
}
