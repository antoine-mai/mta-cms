import { useState, useEffect } from 'react'

interface InstallModalProps {
    isOpen: boolean
    onClose: () => void
}

export default function InstallModal({ isOpen, onClose }: InstallModalProps) {
    const [dbType, setDbType] = useState('sqlite')
    const [dbHost, setDbHost] = useState('localhost')
    const [dbPort, setDbPort] = useState('5432')
    const [dbUser, setDbUser] = useState('root')
    const [dbPass, setDbPass] = useState('')
    const [dbName, setDbName] = useState('mta_app')

    const [adminPath, setAdminPath] = useState('/admin')
    const [adminUser, setAdminUser] = useState('admin')
    const [adminPass, setAdminPass] = useState('admin@123')

    const [testStatus, setTestStatus] = useState<'idle' | 'testing' | 'success' | 'error'>('idle')
    const [testMessage, setTestMessage] = useState('')

    useEffect(() => {
        const handleEsc = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen) onClose()
        }
        window.addEventListener('keydown', handleEsc)
        return () => window.removeEventListener('keydown', handleEsc)
    }, [isOpen, onClose])

    const handleTestConnection = async () => {
        setTestStatus('testing')
        setTestMessage('')

        try {
            const res = await fetch('/post/app/install/test', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: dbType,
                    host: dbHost,
                    port: dbPort,
                    user: dbUser,
                    pass: dbPass,
                    name: dbName
                })
            })

            const data = await res.json()

            if (data.status === 'success') {
                setTestStatus('success')
                setTestMessage(data.message)
            } else {
                setTestStatus('error')
                setTestMessage(data.message || 'Connection failed')
            }
        } catch (error) {
            setTestStatus('error')
            setTestMessage('Network error or server unreachable')
        }
    }

    const handleDbTypeChange = (type: string) => {
        setDbType(type)
        if (type === 'postgres') setDbPort('5432')
        if (type === 'mysql') setDbPort('3306')
    }

    const [isInstalling, setIsInstalling] = useState(false)

    const handleInstall = async (e: React.FormEvent) => {
        e.preventDefault()
        setIsInstalling(true)

        try {
            const res = await fetch('/post/app/install/action', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: dbType,
                    host: dbHost,
                    port: dbPort,
                    name: dbName,
                    user: dbUser,
                    pass: dbPass,
                    admin_path: adminPath,
                    admin_user: adminUser,
                    admin_pass: adminPass
                })
            })

            const data = await res.json()

            if (data.status === 'success') {
                // Reload to apply changes (backend has updated config and re-init DB)
                window.location.reload()
            } else {
                alert(data.message || 'Installation failed')
                setIsInstalling(false)
            }
        } catch (error) {
            alert('Network error during installation')
            setIsInstalling(false)
        }
    }

    if (!isOpen) return null

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div
                className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm animate-backdrop-enter"
                onClick={onClose}
            ></div>

            <div className="relative w-full max-w-4xl bg-white dark:bg-slate-900 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-8 animate-modal-enter overflow-hidden">
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 z-10 text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors bg-slate-100 dark:bg-slate-800 p-1"
                >
                    <span className="material-symbols-outlined text-xl">close</span>
                </button>

                <div className="mb-8">
                    <h2 className="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span className="material-symbols-outlined text-primary">settings_suggest</span>
                        Installation Configuration
                    </h2>
                    <p className="text-slate-500 dark:text-slate-400 mt-2 text-sm">Configure your database and administrator access.</p>
                </div>

                <form onSubmit={handleInstall}>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Left Column: Database */}
                        <div className="space-y-5">
                            <div className="pb-2 border-b border-slate-100 dark:border-slate-800 mb-4 flex justify-between items-center">
                                <div className="flex items-center gap-3">
                                    <h3 className="text-lg font-bold text-slate-800 dark:text-slate-200">Database</h3>
                                    <div className="relative">
                                        <select
                                            value={dbType}
                                            onChange={(e) => handleDbTypeChange(e.target.value)}
                                            className="appearance-none cursor-pointer pl-3 pr-9 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm"
                                        >
                                            <option value="sqlite">SQLite</option>
                                            <option value="postgres">PostgreSQL</option>
                                            <option value="mysql">MySQL</option>
                                        </select>
                                        <span className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-[20px] pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                                {dbType !== 'sqlite' && (
                                    <div className="flex items-center gap-2">
                                        {testStatus === 'success' && (
                                            <span className="text-emerald-500 flex items-center animate-fadeIn" title={testMessage}>
                                                <span className="material-symbols-outlined text-[20px]">check_circle</span>
                                            </span>
                                        )}
                                        {testStatus === 'error' && (
                                            <span className="text-red-500 flex items-center animate-fadeIn" title={testMessage}>
                                                <span className="material-symbols-outlined text-[20px]">error</span>
                                            </span>
                                        )}
                                        <button
                                            type="button"
                                            onClick={handleTestConnection}
                                            disabled={testStatus === 'testing'}
                                            className="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg"
                                            title="Test Connection"
                                        >
                                            {testStatus === 'testing' ? (
                                                <span className="material-symbols-outlined text-[16px] animate-spin">refresh</span>
                                            ) : (
                                                <span className="material-symbols-outlined text-[16px]">wifi</span>
                                            )}
                                        </button>
                                    </div>
                                )}
                            </div>



                            {dbType === 'sqlite' && (
                                <div className="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 flex gap-3 text-sm text-blue-700 dark:text-blue-300">
                                    <span className="material-symbols-outlined text-[20px]">folder_open</span>
                                    <div>
                                        <p className="font-bold mb-1">Database Location</p>
                                        <p className="font-mono text-xs">~/.mta/cms/database</p>
                                    </div>
                                </div>
                            )}

                            {/* Postgres/MySQL Inputs */}
                            {dbType !== 'sqlite' && (
                                <div className="space-y-4 animate-fadeIn">
                                    <div className="grid grid-cols-3 gap-4">
                                        <div className="col-span-2">
                                            <label className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1">Host</label>
                                            <input
                                                type="text"
                                                value={dbHost}
                                                onChange={(e) => setDbHost(e.target.value)}
                                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all"
                                                placeholder="localhost"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1">Port</label>
                                            <input
                                                type="text"
                                                value={dbPort}
                                                onChange={(e) => setDbPort(e.target.value)}
                                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all"
                                                placeholder="5432"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1">Database Name</label>
                                        <input
                                            type="text"
                                            value={dbName}
                                            onChange={(e) => setDbName(e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all"
                                            placeholder="mta_app"
                                        />
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1">User</label>
                                            <input
                                                type="text"
                                                value={dbUser}
                                                onChange={(e) => setDbUser(e.target.value)}
                                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all"
                                                placeholder="root"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1">Password</label>
                                            <input
                                                type="password"
                                                value={dbPass}
                                                onChange={(e) => setDbPass(e.target.value)}
                                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all"
                                                placeholder="••••••"
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Right Column: Admin */}
                        <div className="space-y-5">
                            <div className="pb-2 border-b border-slate-100 dark:border-slate-800 mb-4">
                                <h3 className="text-lg font-bold text-slate-800 dark:text-slate-200">Administrator</h3>
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Admin Path</label>
                                <div className="relative">
                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-[20px]">link</span>
                                    <input
                                        type="text"
                                        value={adminPath}
                                        onChange={(e) => setAdminPath(e.target.value)}
                                        className="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all placeholder:text-slate-400"
                                        placeholder="/admin"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Username</label>
                                <div className="relative">
                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-[20px]">person</span>
                                    <input
                                        type="text"
                                        value={adminUser}
                                        onChange={(e) => setAdminUser(e.target.value)}
                                        className="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all placeholder:text-slate-400"
                                        placeholder="admin"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password</label>
                                <div className="relative">
                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-[20px]">lock</span>
                                    <input
                                        type="text"
                                        value={adminPass}
                                        onChange={(e) => setAdminPass(e.target.value)}
                                        className="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-900 dark:text-white transition-all placeholder:text-slate-400"
                                        placeholder="password"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="mt-10 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-6 py-3.5 font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={isInstalling}
                            className="bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-8 transition-all shadow-lg shadow-primary/25 flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            {isInstalling ? (
                                <>
                                    Installing...
                                    <span className="material-symbols-outlined text-[20px] animate-spin">refresh</span>
                                </>
                            ) : (
                                <>
                                    Complete Installation
                                    <span className="material-symbols-outlined text-[20px]">check</span>
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    )
}
