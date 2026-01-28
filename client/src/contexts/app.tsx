import { createContext, useContext, useState, useEffect, type ReactNode } from 'react'
import { useAuth } from './auth'

interface MenuItem {
    path: string
    name: string
    icon: string
}

interface AppContextType {
    moduleItems: MenuItem[]
    isLoading: boolean
    isInstalled: boolean
    refresh: () => Promise<void>
}

const AppContext = createContext<AppContextType | undefined>(undefined)

export function AppProvider({ children }: { children: ReactNode }) {
    const { isAuthenticated, isLoading: authLoading } = useAuth()
    const [moduleItems, setModuleItems] = useState<MenuItem[]>([])
    const [isLoading, setIsLoading] = useState(true)
    const [isInstalled, setIsInstalled] = useState(true) // Default to true to prevent flash of install page

    const fetchAppConfig = async () => {
        try {
            const response = await fetch('/post/app', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })

            if (response.ok) {
                const data = await response.json()

                if (data.installed !== undefined) {
                    setIsInstalled(data.installed)
                }

                if (isAuthenticated) {
                    // Server now returns a map of modules, convert to array for frontend usage
                    const modules = data.modules || {}
                    const modulesArray = Array.isArray(modules) ? modules : Object.values(modules)
                    setModuleItems(modulesArray as MenuItem[])
                }
            }
        } catch (error) {
            console.error('Failed to fetch app configuration:', error)
        } finally {
            setIsLoading(false)
        }
    }

    useEffect(() => {
        if (!authLoading) {
            fetchAppConfig()
        }
    }, [isAuthenticated, authLoading])

    return (
        <AppContext.Provider value={{ moduleItems, isLoading, isInstalled, refresh: fetchAppConfig }}>
            {children}
        </AppContext.Provider>
    )
}

export function useApp() {
    const context = useContext(AppContext)
    if (context === undefined) {
        throw new Error('useApp must be used within an AppProvider')
    }
    return context
}
