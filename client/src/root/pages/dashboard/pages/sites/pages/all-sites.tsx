import { useState, useEffect } from 'react'
import { Globe, Plus, ExternalLink, Settings, Trash2, Eye } from 'lucide-react'

interface Site {
    id: number
    name: string
    domain: string
    status: 'active' | 'inactive'
    created_at: string
}

export default function AllSitesPage() {
    const [sites, setSites] = useState<Site[]>([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        fetchSites()
    }, [])

    const fetchSites = async () => {
        setLoading(true)
        try {
            const res = await fetch('/root/post/sites/browse')
            const data = await res.json()
            if (data.success) {
                setSites(data.items || [])
            }
        } catch (error) {
            console.error('Failed to fetch sites:', error)
        } finally {
            setLoading(false)
        }
    }

    const handleDelete = async (site: Site) => {
        if (!confirm(`Are you sure you want to delete ${site.name}?`)) return

        try {
            const res = await fetch('/root/post/sites/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: site.id })
            })
            const data = await res.json()
            if (data.success) {
                fetchSites()
            }
        } catch (error) {
            console.error('Failed to delete site:', error)
        }
    }

    return (
        <div className="h-full flex flex-col">
            {/* Header */}
            <div className="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="p-2 bg-slate-100 dark:bg-slate-800 rounded-xl">
                            <Globe className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 dark:text-white">All Sites</h1>
                        </div>
                    </div>

                    <button className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-300 flex items-center gap-2 text-sm shadow-sm">
                        <Plus className="w-4 h-4" />
                        Add Site
                    </button>
                </div>
            </div>

            {/* Content */}
            <div className="flex-1 overflow-y-auto p-6">
                {loading ? (
                    <div className="flex items-center justify-center py-20">
                        <div className="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                ) : sites.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl">
                        <div className="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                            <Globe className="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 className="text-lg font-bold text-slate-900 dark:text-white mb-1">No sites found</h3>
                        <p className="text-sm text-slate-500 dark:text-slate-400 mb-6">Create your first website to start managing content.</p>
                        <button className="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all duration-300 hover:scale-105 flex items-center gap-2">
                            <Plus className="w-5 h-5" />
                            Click to Create Site
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {sites.map((site) => (
                            <div
                                key={site.id}
                                className="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 transition-all duration-300 hover:-translate-y-1"
                            >
                                {/* Site Icon */}
                                <div className="flex items-start justify-between mb-4">
                                    <div className="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 transition-colors">
                                        <Globe className="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ${site.status === 'active'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                                        : 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-400'
                                        }`}>
                                        {site.status === 'active' ? 'Active' : 'Inactive'}
                                    </span>
                                </div>

                                {/* Site Info */}
                                <h3 className="text-lg font-bold text-slate-900 dark:text-white mb-1 truncate">
                                    {site.name}
                                </h3>
                                <div className="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-6 font-medium">
                                    <ExternalLink className="w-3.5 h-3.5" />
                                    <span className="truncate">{site.domain}</span>
                                </div>

                                {/* Actions */}
                                <div className="flex items-center gap-2">
                                    <button
                                        className="flex-1 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors flex items-center justify-center gap-2 text-xs font-bold"
                                    >
                                        <Eye className="w-4 h-4" />
                                        View
                                    </button>
                                    <button
                                        className="flex-1 px-3 py-2 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-xl transition-colors flex items-center justify-center gap-2 text-xs font-bold"
                                    >
                                        <Settings className="w-4 h-4" />
                                        Settings
                                    </button>
                                    <button
                                        onClick={() => handleDelete(site)}
                                        className="p-2 hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl transition-colors"
                                    >
                                        <Trash2 className="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    )
}
