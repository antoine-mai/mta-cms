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
    refresh: () => Promise<void>
}

const AppContext = createContext<AppContextType | undefined>(undefined)

export function AppProvider({ children }: { children: ReactNode }) {
    const { isAuthenticated, isLoading: authLoading } = useAuth()
    const [moduleItems, setModuleItems] = useState<MenuItem[]>([])
    const [isLoading, setIsLoading] = useState(true)

    const fetchAppConfig = async () => {
        if (!isAuthenticated) {
            setIsLoading(false)
            return
        }

        try {
            const response = await fetch('/post/app', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })

            if (response.ok) {
                const data = await response.json()
                // Server now returns a map of modules, convert to array for frontend usage
                const modules = data.modules || {}
                const modulesArray = Array.isArray(modules) ? modules : Object.values(modules)
                setModuleItems(modulesArray as MenuItem[])
            }
        } catch (error) {
            console.error('Failed to fetch app configuration:', error)
        } finally {
            setIsLoading(false)
        }
    }

    useEffect(() => {
        if (!authLoading) {
            if (isAuthenticated) {
                fetchAppConfig()
            } else {
                setModuleItems([])
                setIsLoading(false)
            }
        }
    }, [isAuthenticated, authLoading])

    return (
        <AppContext.Provider value={{ moduleItems, isLoading, refresh: fetchAppConfig }}>
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