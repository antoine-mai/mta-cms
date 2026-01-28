import { useEffect, lazy } from 'react'
import { Outlet } from 'react-router-dom'
import { useApp } from '@/contexts/app'

const InstallPage = lazy(() => import('@/pages/install'))

interface PublicLayoutProps {
    bodyClasses?: string[]
}

export default function PublicLayout({ bodyClasses = [] }: PublicLayoutProps) {
    const { isInstalled, isLoading } = useApp()

    useEffect(() => {
        // Default classes for public layout (login, register pages)
        const defaultClasses = [
            'bg-background-main',
            'text-text-main',
            'min-h-screen',
            'w-full',
            'flex',
            'items-center',
            'justify-center',
            'font-sans'
        ]

        const installClasses = [
            'bg-gradient-to-br',
            'from-slate-50',
            'to-slate-200',
            'dark:from-slate-900',
            'dark:to-slate-800',
            'font-display',
            'min-h-screen',
            'flex',
            'items-center',
            'justify-center',
            'p-6'
        ]

        const activeClasses = isInstalled ? defaultClasses : installClasses

        // Combine default and custom classes
        const allClasses = [...activeClasses, ...bodyClasses]

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
    }, [bodyClasses, isInstalled])

    if (isLoading) {
        return null // Or a loading spinner
    }

    if (!isInstalled) {
        return <InstallPage />
    }

    return <Outlet />
}