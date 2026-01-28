import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '@/contexts/auth'

export default function LoginPage() {
    const [username, setUsername] = useState('')
    const [password, setPassword] = useState('')
    const [error, setError] = useState('')
    const [isLoading, setIsLoading] = useState(false)

    const { login } = useAuth()
    const navigate = useNavigate()

    const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        setError('')
        setIsLoading(true)

        try {
            await login(username, password)
            navigate('/')
        } catch (err) {
            setError('Invalid credentials. Please try again.')
        } finally {
            setIsLoading(false)
        }
    }

    return <div className="w-full max-w-[440px] px-6">
        <div className="flex justify-center mb-10">
            <div className="size-14 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-blue-500/20">
                <span className="material-symbols-outlined text-white text-3xl">grid_view</span>
            </div>
        </div>
        <div className="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 sm:p-12">
            <div className="text-center mb-10">
                <h1 className="text-2xl font-bold text-text-main tracking-tight mb-2">Welcome Back</h1>
                <p className="text-sm text-text-secondary">Enter your credentials to access the dashboard</p>
            </div>
            <form className="space-y-5" onSubmit={handleSubmit}>
                {error && <div className="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                    {error}
                </div>}
                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-2" htmlFor="username">
                        Username
                    </label>
                    <input
                        className="w-full px-4 py-3 rounded-lg border border-border-light focus:border-primary outline-none transition-all placeholder:text-text-muted text-text-main font-normal"
                        id="username"
                        name="username"
                        placeholder="username"
                        required
                        type="text"
                        value={username}
                        onChange={(e) => setUsername(e.target.value)}
                        disabled={isLoading}
                    />
                </div>
                <div>
                    <div className="flex items-center justify-between mb-2">
                        <label className="block text-xs font-bold uppercase tracking-wider text-text-secondary" htmlFor="password">
                            Password
                        </label>
                        <div className="relative group">
                            <span className="text-xs font-medium text-primary hover:underline transition-all cursor-help">
                                Forgot Password?
                            </span>
                            <div className="absolute right-0 bottom-full mb-2 w-[180px] p-2 bg-text-main text-background-main text-[10px] rounded-lg shadow-xl opacity-0 translate-y-2 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-10 flex flex-col gap-1 text-center border border-white/10">
                                <span className="font-semibold text-white">Password Loacation</span>
                                <div className="bg-white/10 px-1.5 py-1 rounded font-mono text-[9px] text-white/80 break-all border border-white/5">
                                    ~/.mta/app/config.yaml
                                </div>
                                <div className="absolute right-4 -bottom-1 w-2 h-2 bg-text-main rotate-45 border-r border-b border-white/10"></div>
                            </div>
                        </div>
                    </div>
                    <input
                        className="w-full px-4 py-3 rounded-lg border border-border-light focus:border-primary outline-none transition-all placeholder:text-text-muted text-text-main font-normal"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        disabled={isLoading}
                    />
                </div>
                <div className="pt-4">
                    <button
                        className="w-full py-3.5 px-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2 text-sm shadow-md shadow-blue-500/10 disabled:opacity-50 disabled:cursor-not-allowed"
                        type="submit"
                        disabled={isLoading}
                    >
                        {isLoading ? 'Signing In...' : 'Sign In'}
                    </button>
                </div>
            </form>
        </div>

        <div className="mt-12 flex flex-col items-center gap-4 text-text-muted text-[11px] font-medium tracking-wide uppercase">
            <div className="flex gap-6">
                <a className="hover:text-primary transition-colors" href="#">Privacy Policy</a>
                <a className="hover:text-primary transition-colors" href="#">Terms of Service</a>
                <a className="hover:text-primary transition-colors" href="#">Support</a>
            </div>
            <span>© 2024 MTA-APP</span>
        </div>

        <style>{`
            input:focus {
                box-shadow: 0 0 0 2px white, 0 0 0 3px #135bec;
            }
        `}</style>
    </div>
}