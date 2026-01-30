import { Plus, Search, MoreVertical, Calendar } from 'lucide-react'

export default function CmsPagesPage() {
    const pages = [
        { id: 1, title: 'About Us', slug: 'about-us', status: 'published', lastModified: '2024-01-15', template: 'Default' },
        { id: 2, title: 'Contact', slug: 'contact', status: 'published', lastModified: '2024-01-14', template: 'Contact Form' },
        { id: 3, title: 'Privacy Policy', slug: 'privacy-policy', status: 'draft', lastModified: '2024-01-13', template: 'Legal' },
        { id: 4, title: 'Terms of Service', slug: 'terms-of-service', status: 'published', lastModified: '2024-01-12', template: 'Legal' },
    ]

    return (
        <div className="flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden m-6">
            <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900">
                <div className="flex items-center gap-4 flex-1 max-w-lg">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            placeholder="Search pages..."
                            className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                        />
                    </div>
                </div>

                <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                    <Plus size={18} />
                    New Page
                </button>
            </div>

            <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">
                <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    <table className="w-full">
                        <thead className="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th className="text-left px-6 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Page</th>
                                <th className="text-left px-6 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Template</th>
                                <th className="text-left px-6 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Status</th>
                                <th className="text-left px-6 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Last Modified</th>
                                <th className="text-right px-6 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-200 dark:divide-slate-700">
                            {pages.map(page => (
                                <tr key={page.id} className="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors group">
                                    <td className="px-6 py-4">
                                        <div>
                                            <div className="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {page.title}
                                            </div>
                                            <div className="text-xs text-slate-500 dark:text-slate-400">/{page.slug}</div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="text-sm text-slate-600 dark:text-slate-300">{page.template}</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`text-xs font-bold px-2 py-1 rounded-md ${page.status === 'published'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
                                            }`}>
                                            {page.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                            <Calendar size={14} />
                                            {page.lastModified}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <button className="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                            <MoreVertical size={16} className="text-slate-400" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    )
}
