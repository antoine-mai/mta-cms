import { Outlet, Navigate } from 'react-router-dom'
import { useEffect } from 'react'

import { useAuth } from '@/contexts/auth'

import Sidebar from './components/sidebar'

interface PrivateLayoutProps {
    bodyClasses?: string[]
}

export default function AdminLayout({ bodyClasses = [] }: PrivateLayoutProps) {
    const { isAuthenticated, isLoading } = useAuth()

    useEffect(() => {
        // Default classes for this layout
        const defaultClasses = [
            'bg-background-main',
            'text-text-main',
            'h-screen',
            'w-full',
            'flex',
            'overflow-hidden',
            'font-sans'
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

    if (isLoading) {
        return null
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />
    }

    return (
        <div className="bg-background-main text-text-main h-screen w-full flex overflow-hidden font-sans">
            <Sidebar />
            <Outlet />
        </div>
    )
}