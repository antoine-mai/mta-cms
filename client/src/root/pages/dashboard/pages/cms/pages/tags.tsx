import { Plus, Search, Hash, MoreVertical } from 'lucide-react'

export default function CmsTagsPage() {
    const tags = [
        { id: 1, name: 'React', slug: 'react', count: 45, color: '#61DAFB' },
        { id: 2, name: 'JavaScript', slug: 'javascript', count: 67, color: '#F7DF1E' },
        { id: 3, name: 'TypeScript', slug: 'typescript', count: 32, color: '#3178C6' },
        { id: 4, name: 'CSS', slug: 'css', count: 28, color: '#1572B6' },
        { id: 5, name: 'Node.js', slug: 'nodejs', count: 19, color: '#339933' },
        { id: 6, name: 'Python', slug: 'python', count: 24, color: '#3776AB' },
        { id: 7, name: 'Design', slug: 'design', count: 41, color: '#FF6B6B' },
        { id: 8, name: 'Tutorial', slug: 'tutorial', count: 53, color: '#9B59B6' },
    ]

    return (
        <div className="flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden m-6">
            <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900">
                <div className="flex items-center gap-4 flex-1 max-w-lg">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            placeholder="Search tags..."
                            className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                        />
                    </div>
                </div>

                <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                    <Plus size={18} />
                    New Tag
                </button>
            </div>

            <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    {tags.map(tag => (
                        <div key={tag.id} className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:shadow-lg transition-all duration-300 group relative">
                            <div className="flex items-start justify-between mb-3">
                                <div
                                    className="size-10 rounded-lg flex items-center justify-center text-white font-bold"
                                    style={{ backgroundColor: tag.color }}
                                >
                                    <Hash size={20} />
                                </div>
                                <button className="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                    <MoreVertical size={14} className="text-slate-400" />
                                </button>
                            </div>
                            <h3 className="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {tag.name}
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mb-2">/{tag.slug}</p>
                            <div className="pt-3 border-t border-slate-100 dark:border-slate-700">
                                <span className="text-xs font-medium text-slate-600 dark:text-slate-300">
                                    {tag.count} posts
                                </span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    )
}
