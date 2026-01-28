import { useLocation, Link } from 'react-router-dom'
import { useEffect, useState, useRef } from 'react'
import { useTheme } from '@/contexts/theme'
import { useAuth } from '@/contexts/auth'
import { useApp } from '@/contexts/app'
import { useUser } from '@/contexts/user'

import Logo from '@/assets/images/logo.svg'

export default function Sidebar() {
    const [isDropdownOpen, setIsDropdownOpen] = useState(false)
    const dropdownRef = useRef<HTMLDivElement>(null)
    const location = useLocation()
    const { logout } = useAuth()
    const { moduleItems } = useApp()
    const { user } = useUser()
    const { theme, setTheme } = useTheme()



    // Helper to check if link is active
    const isActive = (path: string) => {
        if (path === '/' && location.pathname === '/') return true
        if (path === '/admin' && (location.pathname === '/admin' || location.pathname === '/admin/')) return true
        if (path !== '/' && path !== '/admin' && location.pathname.startsWith(path)) return true
        return false
    }

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsDropdownOpen(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => {
            document.removeEventListener('mousedown', handleClickOutside)
        }
    }, [])

    return <aside className="w-[50px] bg-sidebar-slim border-r border-border-light flex flex-col items-center gap-0 z-50 shrink-0 pb-6">
        <div className="h-12 w-full flex items-center justify-center border-b border-border-light shrink-0 mb-2">
            <img src={Logo} alt="Logo" className="size-8" />
        </div>
        <nav className="flex flex-col gap-1 w-full items-center">
            {/* Overview / Dashboard */}
            <Link to="/admin" className={`w-full aspect-square flex items-center justify-center transition-all group relative ${isActive('/admin') ? 'text-primary' : 'text-text-secondary hover:text-primary'}`}>
                {isActive('/admin') && <div className="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-primary"></div>}
                <span className={`material-symbols-outlined text-[24px] ${isActive('/admin') ? 'filled shadow-sm' : ''}`} style={isActive('/admin') ? { fontVariationSettings: "'FILL' 1" } : {}}>dashboard</span>
                <span className="absolute left-14 bg-text-main text-background-main text-[11px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium">Dashboard</span>
            </Link>

            {/* Dynamic Modules & Tools */}
            {moduleItems.filter(item => item.path !== '/').map((item) => (
                <Link key={item.path} to={item.path} className={`w-full aspect-square flex items-center justify-center transition-all group relative ${isActive(item.path) ? 'text-primary' : 'text-text-secondary hover:text-primary'}`}>
                    {isActive(item.path) && <div className="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-primary"></div>}
                    <span className={`material-symbols-outlined text-[24px] ${isActive(item.path) ? 'filled shadow-sm' : ''}`} style={isActive(item.path) ? { fontVariationSettings: "'FILL' 1" } : {}}>{item.icon || 'circle'}</span>
                    <span className="absolute left-14 bg-text-main text-background-main text-[11px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium">{item.name}</span>
                </Link>
            ))}
        </nav>

        <div className="mt-auto flex flex-col gap-4 items-center relative" ref={dropdownRef}>
            <button
                className="size-8 rounded-full bg-slate-200 dark:bg-slate-700 border border-border-light cursor-pointer transition-transform hover:scale-105 active:scale-95 flex items-center justify-center overflow-hidden"
                onClick={() => setIsDropdownOpen(!isDropdownOpen)}
            >
                {user?.username ? (
                    <span className="text-[10px] font-bold text-text-main uppercase">{user.username.substring(0, 2)}</span>
                ) : (
                    <span className="material-symbols-outlined text-[18px]">person</span>
                )}
            </button>

            {isDropdownOpen && <div className="absolute left-[calc(100%+8px)] bottom-0 w-64 bg-card-bg rounded-lg shadow-2xl border border-border-light py-2 z-50 animate-in fade-in slide-in-from-left-2 duration-200">
                <div className="px-5 py-3 border-b border-border-light bg-background-light/30 rounded-t-lg -mt-2 mb-1">
                    <div className="flex items-center gap-3">
                        <div className="size-9 rounded-full bg-primary/10 flex items-center justify-center border border-border-light">
                            <span className="text-xs font-bold text-primary uppercase">{user?.username?.substring(0, 2)}</span>
                        </div>
                        <div className="overflow-hidden">
                            <p className="text-sm font-bold text-text-main truncate">{user?.username}</p>
                            <p className="text-[10px] text-text-secondary font-medium truncate">Administrator</p>
                        </div>
                    </div>
                </div>
                <div className="py-1 px-1">
                    <Link to="/admin/profile" className={`flex items-center gap-3 px-3 py-1.5 text-[13px] rounded transition-colors font-medium ${isActive('/admin/profile') ? 'bg-primary/10 text-primary' : 'text-text-secondary hover:bg-primary/5 hover:text-primary'}`}>
                        <span className="material-symbols-outlined text-[18px]">person</span>
                        Account Profile
                    </Link>
                </div>
                <div className="border-t border-border-light mt-1 pt-3 px-3 pb-2">
                    <p className="text-[10px] font-bold text-text-muted uppercase tracking-wider mb-2">Color Mode</p>
                    <div className="flex bg-background-light p-1 rounded-lg">
                        <button
                            onClick={() => setTheme('light')}
                            className={`flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-md text-[10px] font-bold transition-all flex-1 ${theme === 'light' ? 'bg-card-bg text-primary shadow-sm' : 'text-text-secondary hover:text-text-main'}`}
                        >
                            <span className="material-symbols-outlined text-[14px]">light_mode</span>
                            Light
                        </button>
                        <button
                            onClick={() => setTheme('dark')}
                            className={`flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-md text-[10px] font-bold transition-all flex-1 ${theme === 'dark' ? 'bg-card-bg text-primary shadow-sm' : 'text-text-secondary hover:text-text-main'}`}
                        >
                            <span className="material-symbols-outlined text-[14px]">dark_mode</span>
                            Dark
                        </button>
                    </div>
                </div>
                <div className="border-t border-border-light mt-1 pt-1 px-1">
                    <button onClick={logout} className="w-full flex items-center gap-3 px-3 py-1.5 text-[13px] text-red-500 hover:bg-red-500/10 rounded transition-colors text-left font-bold">
                        <span className="material-symbols-outlined text-[18px]">logout</span>
                        Sign Out
                    </button>
                </div>
            </div>}
        </div>
    </aside>
}
