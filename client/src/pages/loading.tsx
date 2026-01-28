import { useEffect } from 'react'

interface LoadingLayoutProps {
    bodyClasses?: string[]
}

export default function LoadingLayout({ bodyClasses = [] }: LoadingLayoutProps) {
    useEffect(() => {
        // Default classes for loading layout
        const defaultClasses = [
            'bg-background-light',
            'dark:bg-background-dark',
            'font-display',
            'text-[#0d121b]',
            'dark:text-white',
            'transition-colors',
            'duration-200'
        ]

        // Combine default and custom classes
        const allClasses = [...defaultClasses, ...bodyClasses]

        // Get #root element
        const rootElement = document.getElementById('root')
        if (rootElement) {
            // Add classes to #root
            rootElement.classList.add(...allClasses)

            // Cleanup: remove classes when component unmounts
            return () => {
                rootElement.classList.remove(...allClasses)
            }
        }
    }, [bodyClasses])

    return (
        <div className="relative flex min-h-screen w-full flex-col items-center justify-center overflow-x-hidden p-6">
            <div className="flex flex-col items-center max-w-[560px] w-full">
                {/* Spinner Container */}
                <div className="relative mb-8">
                    <div className="bg-white dark:bg-[#1e293b] rounded-full shadow-lg p-12 flex items-center justify-center border border-slate-100 dark:border-slate-800">
                        <div className="relative w-16 h-16">
                            <div className="absolute inset-0 border-4 border-primary/10 rounded-full"></div>
                            <div className="absolute inset-0 border-4 border-transparent border-t-primary rounded-full animate-spin"></div>
                        </div>
                    </div>
                </div>

                {/* Loading Text */}
                <div className="flex flex-col items-center gap-3 text-center">
                    <h1 className="text-[#0d121b] dark:text-white text-xl font-medium tracking-tight">
                        Loading Application...
                    </h1>
                    <p className="text-slate-500 dark:text-slate-400 text-sm font-normal">
                        Setting up your secure environment
                    </p>
                </div>
            </div>

            {/* Progress Bar */}
            <div className="fixed bottom-0 left-0 w-full h-1 bg-primary/5">
                <div className="h-full bg-primary/30 w-1/3 animate-[loading_2s_ease-in-out_infinite]"></div>
            </div>

            <style>{`
                @keyframes loading {
                    0% { transform: translateX(-100%); }
                    100% { transform: translateX(300%); }
                }
            `}</style>
        </div>
    )
}
