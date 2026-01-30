import { useState } from 'react'
import { useAuth } from '../contexts/auth'
import { Navigate } from 'react-router-dom'

export default function LoginPage() {
    const { login, isAuthenticated, isLoading } = useAuth()
    const [username, setUsername] = useState('')
    const [password, setPassword] = useState('')
    const [error, setError] = useState<string | null>(null)
    const [isSubmitting, setIsSubmitting] = useState(false)

    if (isLoading) return null
    if (isAuthenticated) return <Navigate to="/" />

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault()
        setError(null)
        setIsSubmitting(true)

        try {
            await login(username, password)
        } catch (err: any) {
            setError(err.message || 'Invalid username or password')
        } finally {
            setIsSubmitting(false)
        }
    }

    return (
        <div className="min-h-screen w-full flex items-center justify-center bg-slate-50 dark:bg-[#1e1e1e] p-4 relative overflow-hidden">
            <div className="w-full max-w-md z-10">
                <div className="bg-white dark:bg-[#252526] border border-slate-200 dark:border-[#3c3c3c] rounded-lg shadow-2xl p-8 md:p-10 transition-all duration-300">
                    <div className="flex flex-col items-center mb-8">
                        <div className="size-16 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-4">
                            <svg className="size-10" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path clipRule="evenodd" d="M24 18.4228L42 11.475V34.3663C42 34.7796 41.7457 35.1504 41.3601 35.2992L24 42V18.4228Z" fill="currentColor" fillRule="evenodd"></path>
                                <path clipRule="evenodd" d="M24 8.18819L33.4123 11.574L24 15.2071L14.5877 11.574L24 8.18819ZM9 15.8487L21 20.4805V37.6263L9 32.9945V15.8487ZM27 37.6263V20.4805L39 15.8487V32.9945L27 37.6263ZM25.354 2.29885C24.4788 1.98402 23.5212 1.98402 22.646 2.29885L4.98454 8.65208C3.7939 9.08038 3 10.2097 3 11.475V34.3663C3 36.0196 4.01719 37.5026 5.55962 38.098L22.9197 44.7987C23.6149 45.0671 24.3851 45.0671 25.0803 44.7987L42.4404 38.098C43.9828 37.5026 45 36.0196 45 34.3663V11.475C45 10.2097 44.2061 9.08038 43.0155 8.65208L25.354 2.29885Z" fill="currentColor" fillRule="evenodd"></path>
                            </svg>
                        </div>
                        <h1 className="text-xl font-medium text-slate-900 dark:text-[#cccccc] tracking-tight">MTA-APP CMS</h1>
                        <p className="text-slate-500 dark:text-[#aaaaaa] mt-2 text-center text-sm">Sign in to start your session</p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        {error && (
                            <div className="bg-red-50 dark:bg-[#4a2323] border border-red-200 dark:border-[#ff5a5a]/20 text-red-600 dark:text-[#f48771] text-sm p-3 rounded flex items-center gap-3">
                                <span className="material-symbols-outlined text-[18px]">error</span>
                                {error}
                            </div>
                        )}

                        <div className="space-y-4">
                            <div className="space-y-2">
                                <label className="text-xs font-medium text-slate-700 dark:text-[#cccccc] uppercase tracking-wider" htmlFor="username">
                                    Username
                                </label>
                                <div className="relative group">
                                    <input
                                        id="username"
                                        type="text"
                                        required
                                        className="block w-full px-4 h-10 bg-slate-50 dark:bg-[#3c3c3c] border border-slate-200 dark:border-[#3c3c3c] rounded text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-[#007acc] transition-all placeholder:text-slate-500"
                                        placeholder="Username"
                                        value={username}
                                        onChange={(e) => setUsername(e.target.value)}
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label className="text-xs font-medium text-slate-700 dark:text-[#cccccc] uppercase tracking-wider" htmlFor="password">
                                    Password
                                </label>
                                <div className="relative group">
                                    <input
                                        id="password"
                                        type="password"
                                        required
                                        className="block w-full px-4 h-10 bg-slate-50 dark:bg-[#3c3c3c] border border-slate-200 dark:border-[#3c3c3c] rounded text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-[#007acc] transition-all placeholder:text-slate-500"
                                        placeholder="Password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={isSubmitting}
                            className="w-full h-10 bg-[#007acc] hover:bg-[#0062a3] text-white font-medium rounded shadow-sm flex items-center justify-center gap-2 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            {isSubmitting ? (
                                <>
                                    <div className="size-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    <span>Signing in...</span>
                                </>
                            ) : (
                                <span>Sign In</span>
                            )}
                        </button>
                    </form>
                </div>

                <p className="mt-8 text-center text-xs text-slate-500 dark:text-[#858585]">
                    &copy; {new Date().getFullYear()} MTA-APP CMS. All rights reserved.
                </p>
            </div>
        </div>
    )
}
