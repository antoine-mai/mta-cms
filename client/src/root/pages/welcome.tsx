import { useNavigate } from 'react-router-dom'
import {
    Shield,
    FileText,
    ShoppingBag,
    FolderOpen,
    Database,
    Users,
    Settings,
    ArrowRight,
    Lock,
    Zap,
    CheckCircle2,
    Info
} from 'lucide-react'

export default function WelcomePage() {
    const navigate = useNavigate()

    const features = [
        {
            icon: FileText,
            title: 'Content Management',
            description: 'Manage your posts, pages, and media with an intuitive CMS interface',
            color: 'from-blue-500 to-cyan-500',
            path: '/dashboard/cms'
        },
        {
            icon: ShoppingBag,
            title: 'E-Commerce',
            description: 'Complete e-commerce solution with products, orders, and analytics',
            color: 'from-purple-500 to-pink-500',
            path: '/dashboard/ecommerce'
        },
        {
            icon: FolderOpen,
            title: 'File Manager',
            description: 'Organize and manage your files with a powerful file management system',
            color: 'from-orange-500 to-red-500',
            path: '/files'
        },
        {
            icon: Database,
            title: 'Backup & Restore',
            description: 'Keep your data safe with automated backup and restore capabilities',
            color: 'from-green-500 to-emerald-500',
            path: '/backup'
        }
    ]

    const benefits = [
        { icon: Lock, label: 'Single User Access', description: 'Configured via .env file' },
        { icon: Zap, label: 'Fast & Lightweight', description: 'Optimized for solo usage' },
        { icon: Shield, label: 'Secure by Default', description: 'No multi-user overhead' },
        { icon: CheckCircle2, label: 'Full Control', description: 'Complete admin access' }
    ]

    return (
        <div className="min-h-full bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
            {/* Hero Section */}
            <div className="relative overflow-hidden">
                {/* Animated Background Elements */}
                <div className="absolute inset-0 overflow-hidden pointer-events-none">
                    <div className="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-indigo-400/20 to-purple-400/20 dark:from-indigo-600/10 dark:to-purple-600/10 rounded-full blur-3xl animate-pulse"></div>
                    <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-cyan-400/20 to-blue-400/20 dark:from-cyan-600/10 dark:to-blue-600/10 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }}></div>
                </div>

                <div className="relative max-w-7xl mx-auto px-6 lg:px-8 py-20">
                    {/* Welcome Header */}
                    <div className="text-center mb-16">
                        <div className="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 dark:bg-indigo-500/10 rounded-full mb-6">
                            <Shield className="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            <span className="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Single User Mode</span>
                        </div>

                        <h1 className="text-5xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6 tracking-tight">
                            Welcome to
                            <span className="block bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
                                Root Dashboard
                            </span>
                        </h1>

                        <p className="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto mb-4">
                            Your personal management platform designed for a single user defined in <code className="px-2 py-1 bg-slate-200 dark:bg-slate-800 rounded text-sm font-mono text-indigo-600 dark:text-indigo-400">.env</code>
                        </p>

                        <div className="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 mb-8">
                            <Info className="w-5 h-5" />
                            <p className="text-sm">
                                Need multi-user support? You can enable <strong>User Dashboard</strong> for collaborative access
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center justify-center gap-4">
                            <button
                                onClick={() => navigate('/dashboard')}
                                className="group px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-indigo-500/40 flex items-center gap-2"
                            >
                                Get Started
                                <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </button>
                            <button
                                onClick={() => navigate('/dashboard/overview')}
                                className="px-8 py-4 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-900 dark:text-white border-2 border-slate-200 dark:border-slate-700 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:border-indigo-300 dark:hover:border-indigo-600"
                            >
                                View Dashboard
                            </button>
                        </div>
                    </div>

                    {/* Benefits Grid */}
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-20">
                        {benefits.map((benefit, index) => (
                            <div
                                key={index}
                                className="group relative bg-white dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200 dark:border-slate-700 rounded-2xl p-6 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 transition-all duration-300 hover:-translate-y-1"
                            >
                                <div className="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 dark:from-indigo-500/10 dark:to-purple-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div className="relative">
                                    <benefit.icon className="w-8 h-8 text-indigo-600 dark:text-indigo-400 mb-3" />
                                    <div className="text-sm font-bold text-slate-900 dark:text-white mb-1">{benefit.label}</div>
                                    <div className="text-xs text-slate-500 dark:text-slate-400">{benefit.description}</div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Features Grid */}
                    <div className="mb-12">
                        <h2 className="text-3xl font-bold text-slate-900 dark:text-white text-center mb-4">
                            Powerful Features
                        </h2>
                        <p className="text-slate-600 dark:text-slate-400 text-center mb-12 max-w-2xl mx-auto">
                            Everything you need to manage your digital presence, all in one place
                        </p>

                        <div className="grid md:grid-cols-2 gap-6">
                            {features.map((feature, index) => (
                                <div
                                    key={index}
                                    onClick={() => navigate(feature.path)}
                                    className="group relative bg-white dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200 dark:border-slate-700 rounded-2xl p-8 hover:shadow-2xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 transition-all duration-300 hover:-translate-y-2 cursor-pointer overflow-hidden"
                                >
                                    {/* Gradient Overlay */}
                                    <div className={`absolute inset-0 bg-gradient-to-br ${feature.color} opacity-0 group-hover:opacity-5 transition-opacity duration-300`}></div>

                                    {/* Icon with Gradient Background */}
                                    <div className="relative mb-6">
                                        <div className={`inline-flex p-4 bg-gradient-to-br ${feature.color} rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-300`}>
                                            <feature.icon className="w-8 h-8 text-white" />
                                        </div>
                                    </div>

                                    {/* Content */}
                                    <div className="relative">
                                        <h3 className="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {feature.title}
                                        </h3>
                                        <p className="text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">
                                            {feature.description}
                                        </p>
                                        <div className="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-sm group-hover:gap-3 transition-all">
                                            Explore
                                            <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                    </div>

                                    {/* Corner Accent */}
                                    <div className={`absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br ${feature.color} opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity`}></div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Multi-User Info Card */}
                    <div className="bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 border-2 border-slate-300 dark:border-slate-700 rounded-3xl p-8 md:p-12 mb-12">
                        <div className="flex flex-col md:flex-row items-center gap-8">
                            <div className="flex-shrink-0">
                                <div className="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <Users className="w-10 h-10 text-white" />
                                </div>
                            </div>
                            <div className="flex-1 text-center md:text-left">
                                <h3 className="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                                    Need Multi-User Access?
                                </h3>
                                <p className="text-slate-600 dark:text-slate-400 mb-4">
                                    If you're working with a team or need to manage multiple users, you can enable the <strong>User Dashboard</strong> feature.
                                    This allows multiple people to collaborate with role-based access control.
                                </p>
                                <div className="flex flex-wrap gap-2 justify-center md:justify-start">
                                    <span className="px-3 py-1 bg-white dark:bg-slate-700 rounded-full text-xs font-semibold text-slate-700 dark:text-slate-300">Role Management</span>
                                    <span className="px-3 py-1 bg-white dark:bg-slate-700 rounded-full text-xs font-semibold text-slate-700 dark:text-slate-300">Team Collaboration</span>
                                    <span className="px-3 py-1 bg-white dark:bg-slate-700 rounded-full text-xs font-semibold text-slate-700 dark:text-slate-300">Access Control</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Quick Actions */}
                    <div className="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-700 dark:to-purple-700 rounded-3xl p-8 md:p-12 text-center shadow-2xl shadow-indigo-500/20">
                        <Settings className="w-12 h-12 text-white mx-auto mb-4" />
                        <h2 className="text-3xl font-bold text-white mb-4">
                            Ready to Get Started?
                        </h2>
                        <p className="text-indigo-100 mb-8 max-w-2xl mx-auto">
                            Start managing your content, products, and files with full administrative control.
                        </p>
                        <button
                            onClick={() => navigate('/dashboard')}
                            className="group px-8 py-4 bg-white text-indigo-600 hover:bg-indigo-50 rounded-xl font-bold shadow-lg transition-all duration-300 hover:scale-105 inline-flex items-center gap-2"
                        >
                            Go to Dashboard
                            <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    )
}