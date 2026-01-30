export default function EcommerceAttributesPage() {
    return (
        <div className="max-w-7xl mx-auto p-6">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-bold text-slate-900 dark:text-white">Product Attributes</h1>
                    <p className="text-slate-500 dark:text-slate-400 mt-1">Manage color, size, material and other product specifications.</p>
                </div>
                <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95 flex items-center gap-2">
                    <span className="material-symbols-outlined">add</span>
                    New Attribute
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {['Color', 'Size', 'Material', 'Brand'].map(attr => (
                    <div key={attr} className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm hover:border-indigo-500/50 transition-all group">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="font-bold text-slate-900 dark:text-white">{attr}</h3>
                            <button className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <span className="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <span className="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-lg text-xs">Value A</span>
                            <span className="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-lg text-xs">Value B</span>
                            <button className="px-2 py-1 border border-dashed border-slate-300 dark:border-slate-600 text-slate-400 rounded-lg text-xs hover:border-indigo-500 hover:text-indigo-500 transition-colors">
                                + Add
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    )
}
