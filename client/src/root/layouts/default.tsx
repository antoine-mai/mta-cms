import { Outlet } from 'react-router-dom'
import { useEffect } from 'react'

import Header from './components/header'

interface DefaultLayoutProps {
    bodyClasses?: string[]
}

export default function DefaultLayout({ bodyClasses = [] }: DefaultLayoutProps) {

    useEffect(() => {
        // Default classes for this layout
        const defaultClasses = [
            'dark:bg-background-dark',
            'bg-background-light',
            'overflow-x-hidden',
            'dark:text-white',
            'text-slate-900',
            'font-display',
            'h-screen',
            'overflow-hidden',
            'flex-col',
            'flex'
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

    return <>
        <Header />
        <main className="flex-1 w-full max-w-[1920px] mx-auto flex flex-col">
            <Outlet />
        </main>
    </>
}
