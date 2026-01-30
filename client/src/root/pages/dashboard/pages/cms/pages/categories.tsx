import { Plus, Search, MoreVertical } from 'lucide-react'

export default function CmsCategoriesPage() {
    const categories = [
        { id: 1, name: 'Technology', slug: 'technology', postCount: 24, description: 'Tech news and tutorials' },
        { id: 2, name: 'Lifestyle', slug: 'lifestyle', postCount: 18, description: 'Life, health, and wellness' },
        { id: 3, name: 'Business', slug: 'business', postCount: 12, description: 'Business insights and tips' },
    ]

    return (
        <div className="flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden m-6">
            <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900">
                <div className="flex items-center gap-4 flex-1 max-w-lg">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            placeholder="Search categories..."
                            className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                        />
                    </div>
                </div>

                <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                    <Plus size={18} />
                    New Category
                </button>
            </div>

            <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {categories.map(category => (
                        <div key={category.id} className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:shadow-lg transition-all duration-300 group">
                            <div className="flex items-start justify-between mb-3">
                                <div className="flex-1">
                                    <h3 className="font-bold text-lg text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {category.name}
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">/{category.slug}</p>
                                </div>
                                <button className="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    <MoreVertical size={16} className="text-slate-400" />
                                </button>
                            </div>
                            <p className="text-sm text-slate-600 dark:text-slate-300 mb-4">{category.description}</p>
                            <div className="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700">
                                <span className="text-xs font-medium text-slate-500 dark:text-slate-400">
                                    {category.postCount} posts
                                </span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    )
}
