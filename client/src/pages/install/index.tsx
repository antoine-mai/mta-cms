import { useState } from 'react'
import InstallModal from './components/modal'

export default function InstallPage() {
    const [showModal, setShowModal] = useState(false)

    return (
        <>
            <style>{`
                .material-symbols-outlined {
                    font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                }
                @keyframes modalEnter {
                    from { opacity: 0; transform: scale(0.95) translateY(10px); }
                    to { opacity: 1; transform: scale(1) translateY(0); }
                }
                .animate-modal-enter {
                    animation: modalEnter 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                }
                @keyframes backdropEnter {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .animate-backdrop-enter {
                    animation: backdropEnter 0.3s ease-out forwards;
                }
            `}</style>
            <main className="w-full max-w-lg bg-white dark:bg-slate-900 shadow-2xl shadow-slate-200/50 dark:shadow-none border border-white dark:border-slate-800 overflow-hidden">
                <div className="p-10 md:p-14 flex flex-col items-center text-center">
                    <div className="mb-8 p-4 bg-primary/10">
                        <span className="material-symbols-outlined text-primary text-5xl">auto_awesome</span>
                    </div>
                    <h1 className="text-slate-900 dark:text-white text-3xl md:text-4xl font-extrabold tracking-tight mb-4">
                        Welcome to MTA CMS
                    </h1>
                    <p className="text-slate-500 dark:text-slate-400 text-lg leading-relaxed mb-10 max-w-sm">
                        Everything is ready to go. Let's get your setup started and have your site running in minutes.
                    </p>
                    <div className="w-full">
                        <button
                            onClick={() => setShowModal(true)}
                            className="w-full bg-primary hover:bg-primary/90 text-white font-bold py-5 px-8 text-lg transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary/25 flex items-center justify-center gap-3"
                        >
                            Install Now
                            <span className="material-symbols-outlined">rocket_launch</span>
                        </button>
                    </div>
                </div>
            </main>
            <div className="fixed bottom-8 text-slate-400 dark:text-slate-600 text-sm font-medium">
                Version 1.0.0
            </div>

            <InstallModal isOpen={showModal} onClose={() => setShowModal(false)} />
        </>
    )
}