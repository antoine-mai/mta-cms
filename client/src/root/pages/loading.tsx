export default function LoadingPage() {
    return (
        <div className="fixed inset-0 flex items-center justify-center bg-white dark:bg-slate-900 z-[9999]">
            <div className="flex flex-col items-center gap-6">
                <div className="relative size-20">
                    <div className="absolute inset-0 border-4 border-primary/10 rounded-full"></div>
                    <div className="absolute inset-0 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <div className="absolute inset-4 border-4 border-indigo-400/20 rounded-full animate-pulse"></div>
                </div>
                <div className="flex flex-col items-center gap-2">
                    <div className="flex items-center gap-2">
                        <div className="size-2 bg-primary rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                        <div className="size-2 bg-primary rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                        <div className="size-2 bg-primary rounded-full animate-bounce"></div>
                    </div>
                    <h2 className="text-xl font-bold text-slate-900 dark:text-white tracking-tight mt-2">MTA-APP Dashboard</h2>
                    <p className="text-sm text-slate-500 dark:text-slate-400 font-medium">Preparing your experience...</p>
                </div>
            </div>
            {/* Subtle background glow */}
            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 size-[400px] bg-primary/5 blur-[120px] rounded-full -z-10 pointer-events-none"></div>
        </div>
    )
}
