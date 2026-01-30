import { Globe } from 'lucide-react'

export default function CmsSitesPage() {
    const sites = [
        { id: 1, name: 'Main Website', domain: 'example.com', status: 'active', posts: 45, pages: 12 },
        { id: 2, name: 'Blog', domain: 'blog.example.com', status: 'active', posts: 128, pages: 8 },
        { id: 3, name: 'Documentation', domain: 'docs.example.com', status: 'inactive', posts: 67, pages: 24 },
    ]

    return (
        <div className="flex h-full">
            <div className="flex-1 flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden m-6">
                <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900">
                    <h2 className="text-lg font-bold text-slate-900 dark:text-white">Sites</h2>
                    <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                        <Globe size={18} />
                        New Site
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {sites.map(site => (
                            <div key={site.id} className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:shadow-lg transition-all duration-300">
                                <div className="flex items-start justify-between mb-3">
                                    <div className="flex-1">
                                        <h3 className="font-bold text-lg text-slate-900 dark:text-white mb-1">
                                            {site.name}
                                        </h3>
                                        <p className="text-xs text-slate-500 dark:text-slate-400">{site.domain}</p>
                                    </div>
                                    <span className={`text-xs font-bold px-2 py-1 rounded-md ${site.status === 'active'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                                            : 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-400'
                                        }`}>
                                        {site.status}
                                    </span>
                                </div>
                                <div className="flex items-center gap-4 pt-3 border-t border-slate-100 dark:border-slate-700">
                                    <div className="text-sm">
                                        <span className="font-bold text-slate-900 dark:text-white">{site.posts}</span>
                                        <span className="text-slate-500 dark:text-slate-400 ml-1">posts</span>
                                    </div>
                                    <div className="text-sm">
                                        <span className="font-bold text-slate-900 dark:text-white">{site.pages}</span>
                                        <span className="text-slate-500 dark:text-slate-400 ml-1">pages</span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    )
}
