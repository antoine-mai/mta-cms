export default function EcommerceTagsPage() {
    return (
        <div className="max-w-7xl mx-auto p-6">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-bold text-slate-900 dark:text-white">Product Tags</h1>
                    <p className="text-slate-500 dark:text-slate-400 mt-1">Organize products with labels for better search and filtering.</p>
                </div>
            </div>

            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 shadow-sm">
                <div className="flex flex-wrap gap-3">
                    {['New Arrival', 'Best Seller', 'Sale 50%', 'Summer Collection', 'Winter Deal', 'Limited Edition'].map(tag => (
                        <div key={tag} className="flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl">
                            <span className="text-indigo-600 dark:text-indigo-400 font-bold text-sm">#{tag}</span>
                            <button className="text-indigo-300 hover:text-indigo-500 transition-colors">
                                <span className="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                    ))}
                    <button className="flex items-center gap-2 px-4 py-2 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all text-sm font-bold">
                        <span className="material-symbols-outlined text-[18px]">add</span>
                        New Tag
                    </button>
                </div>
            </div>
        </div>
    )
}
