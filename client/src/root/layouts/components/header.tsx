import { useLocation, Link } from 'react-router-dom'
import { useState, useRef, useEffect } from 'react'
import { useTheme } from '@/root/contexts/theme'
import { useStats } from '@/root/contexts/stats'

export default function Header() {
    const [isThemeOpen, setIsThemeOpen] = useState(false)
    const themeRef = useRef<HTMLDivElement>(null)
    const location = useLocation()
    const { theme, setTheme } = useTheme()
    const { stats } = useStats()

    const isDashboardActive = location.pathname.startsWith('/dashboard')
    const isBackupActive = location.pathname.startsWith('/backup')
    const isFilesActive = location.pathname.startsWith('/files')

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (themeRef.current && !themeRef.current.contains(event.target as Node)) {
                setIsThemeOpen(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => document.removeEventListener('mousedown', handleClickOutside)
    }, [])

    const themes = [
        { id: 'light', label: 'Light', icon: 'light_mode' },
        { id: 'dark', label: 'Dark', icon: 'dark_mode' },
        { id: 'system', label: 'System', icon: 'desktop_windows' },
    ] as const

    const activeClass = 'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400'
    const inactiveClass = 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
    const commonClass = 'px-4 py-2 text-sm font-medium rounded-lg transition-colors'

    return (
        <header
            className="sticky top-0 z-50 w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm">
            <div className="px-6 lg:px-8 h-[56px] flex items-center justify-between">
                <div className="flex items-center gap-10">
                    <div className="flex items-center gap-3">
                        <div className="size-8 text-indigo-600 dark:text-indigo-500">
                            <Link to="/">
                                <svg className="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                    <nav className="hidden md:flex items-center gap-1">
                        <Link className={`${commonClass} ${isDashboardActive ? activeClass : inactiveClass}`} to="/dashboard">Dashboard</Link>
                        <Link className={`${commonClass} ${isFilesActive ? activeClass : inactiveClass}`} to="/files">Files</Link>
                        <Link className={`${commonClass} ${isBackupActive ? activeClass : inactiveClass}`} to="/backup">Backup</Link>
                    </nav>
                </div>



                <div className="flex items-center gap-3">
                    {/* System Stats */}
                    <div className="hidden xl:flex items-center gap-3 mr-2">
                        <StatBadge icon="💻" label="CPU" value={stats?.cpu?.percent} />
                        <StatBadge icon="🌐" label="Net" value={(stats?.network?.rx_kbs || 0) + (stats?.network?.tx_kbs || 0)} suffix="KB/s" />
                        <StatBadge icon="⚡" label="RAM" value={stats?.memory?.percent} />
                        <StatBadge icon="💾" label="Disk" value={stats?.storage?.percent} />
                    </div>

                    <button className="size-10 flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors relative">
                        <span className="material-symbols-outlined text-[20px]">notifications</span>
                        <span className="absolute top-2.5 right-2.5 size-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>

                    {/* Help Button */}
                    <button className="size-10 flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Help & Documentation">
                        <span className="material-symbols-outlined text-[20px]">help_outline</span>
                    </button>

                    {/* Theme Toggle Dropdown */}
                    <div className="relative" ref={themeRef}>
                        <button onClick={() => setIsThemeOpen(!isThemeOpen)} className="size-10 flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Change theme">
                            <span className="material-symbols-outlined text-[20px]">
                                {themes.find(t => t.id === theme)?.icon || 'light_mode'}
                            </span>
                        </button>

                        {isThemeOpen && (
                            <div className="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl py-1 z-[60]">
                                {themes.map((t) => (
                                    <button
                                        key={t.id}
                                        onClick={() => {
                                            setTheme(t.id)
                                            setIsThemeOpen(false)
                                        }}
                                        className={`w-full flex items-center gap-3 px-3 py-2 text-sm transition-colors ${theme === t.id
                                            ? 'text-primary bg-primary/5'
                                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'
                                            }`}
                                    >
                                        <span className="material-symbols-outlined text-[18px]">{t.icon}</span>
                                        {t.label}
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    <button
                        className="size-10 flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        onClick={() => {
                            fetch('/root/post/user/logout').then(() => {
                                window.location.href = '/root/login'
                            })
                        }}
                        title="Sign out"
                    >
                        <span className="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </div>
            </div>
        </header>
    )
}

function StatBadge({ icon, label, value, suffix = '%' }: { icon: string; label: string; value?: number; suffix?: string }) {
    if (value === undefined) return null

    return (
        <div className="flex items-center gap-1.5 px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
            <span className="text-sm">{icon}</span>
            <span className="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{label}</span>
            <span className="text-xs font-bold text-slate-900 dark:text-white">
                {suffix === 'KB/s' ? value.toFixed(1) : value.toFixed(0)}{suffix}
            </span>
        </div>
    )
}
