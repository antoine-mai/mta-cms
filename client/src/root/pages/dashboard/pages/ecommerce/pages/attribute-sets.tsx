export default function EcommerceAttributeSetsPage() {
    return (
        <div className="max-w-7xl mx-auto p-6">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-bold text-slate-900 dark:text-white">Attribute Sets</h1>
                    <p className="text-slate-500 dark:text-slate-400 mt-1">Define groups of attributes for different product types.</p>
                </div>
                <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95 flex items-center gap-2">
                    <span className="material-symbols-outlined">add</span>
                    Create Set
                </button>
            </div>

            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
                <table className="w-full text-left border-collapse">
                    <thead>
                        <tr className="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                            <th className="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Set Name</th>
                            <th className="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Slug</th>
                            <th className="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Attributes</th>
                            <th className="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Modified</th>
                            <th className="px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-200 dark:divide-slate-700">
                        <tr className="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td className="px-6 py-4 font-bold text-slate-900 dark:text-white text-sm">Default Set</td>
                            <td className="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm">default-set</td>
                            <td className="px-6 py-4">
                                <span className="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-md text-[10px] font-bold">General</span>
                            </td>
                            <td className="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">Today, 10:30 AM</td>
                            <td className="px-6 py-4 text-right">
                                <button className="p-2 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-slate-400 transition-colors">
                                    <span className="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    )
}
