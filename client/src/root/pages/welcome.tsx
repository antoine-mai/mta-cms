import { useNavigate } from 'react-router-dom'
import {
    ArrowRight,
    Shield,
    Lock,
    Zap,
    CheckCircle2,
    LayoutDashboard
} from 'lucide-react'

export default function WelcomePage() {
    const navigate = useNavigate()

    const benefits = [
        { icon: Lock, label: 'Single User Access', description: 'Defined via .env' },
        { icon: Zap, label: 'Fast & Lightweight', description: 'Zero overhead' },
        { icon: Shield, label: 'Secure by Default', description: 'Isolated core' },
        { icon: CheckCircle2, label: 'Full Control', description: 'Master administrator' }
    ]

    return (
        <div className="h-screen w-full bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-hidden flex items-center justify-center p-6">
            {/* Animated Background Elements */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                <div className="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 dark:from-indigo-600/10 dark:to-purple-600/10 rounded-full blur-3xl animate-pulse"></div>
                <div className="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 dark:from-cyan-600/10 dark:to-blue-600/10 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }}></div>
            </div>

            <div className="relative w-full max-w-4xl mx-auto">
                {/* Main Content Card */}
                <div className="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 dark:shadow-black/50 overflow-hidden">
                    <div className="p-8 md:p-12 text-center">
                        {/* Status Tag */}
                        <div className="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-full mb-8">
                            <Shield className="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            <span className="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Single User Core</span>
                        </div>

                        {/* Title */}
                        <h1 className="text-4xl md:text-6xl font-black text-slate-900 dark:text-white mb-6 tracking-tight leading-tight">
                            Welcome to
                            <span className="block mt-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
                                Root Dashboard
                            </span>
                        </h1>

                        <p className="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                            Your high-performance management hub. Secure, fast, and exclusive.
                        </p>

                        {/* CTA Buttons */}
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                            <button
                                onClick={() => navigate('/dashboard')}
                                className="group w-full sm:w-auto px-10 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-2xl font-bold shadow-xl shadow-slate-900/20 dark:shadow-white/10 transition-all duration-300 hover:scale-105 hover:bg-slate-800 dark:hover:bg-slate-100 flex items-center justify-center gap-2"
                            >
                                Enter Dashboard
                                <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </button>
                            <button
                                onClick={() => navigate('/dashboard/sites')}
                                className="w-full sm:w-auto px-10 py-4 bg-white dark:bg-slate-800 text-slate-900 dark:text-white border-2 border-slate-200 dark:border-slate-700 rounded-2xl font-bold transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:border-indigo-300 dark:hover:border-indigo-600 flex items-center justify-center gap-2"
                            >
                                <LayoutDashboard className="w-5 h-5" />
                                Manage Sites
                            </button>
                        </div>

                        {/* Compact Benefits Grid */}
                        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-slate-50 dark:bg-slate-950/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                            {benefits.map((benefit, index) => (
                                <div key={index} className="flex flex-col items-center text-center p-2">
                                    <div className="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl shadow-sm flex items-center justify-center mb-2 border border-slate-100 dark:border-slate-700">
                                        <benefit.icon className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <h3 className="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">{benefit.label}</h3>
                                    <p className="text-[11px] font-medium text-slate-600 dark:text-slate-400">{benefit.description}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Footer Info */}
                <div className="mt-8 text-center text-slate-400 dark:text-slate-600 text-xs font-medium">
                    Powering your digital assets with <span className="text-indigo-500 font-bold">MTA Core</span>
                </div>
            </div>
        </div>
    )
}