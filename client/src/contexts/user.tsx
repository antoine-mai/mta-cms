import { createContext, useContext, useState, useEffect, type ReactNode } from 'react'
import { useAuth } from './auth'

interface User {
    username: string
    is_root: boolean
}

interface UserContextType {
    user: User | null
    isLoading: boolean
    refreshUser: () => Promise<void>
}

const UserContext = createContext<UserContextType | undefined>(undefined)

export function UserProvider({ children }: { children: ReactNode }) {
    const { isAuthenticated, isLoading: authLoading } = useAuth()
    const [user, setUser] = useState<User | null>(null)
    const [isLoading, setIsLoading] = useState(true)

    const fetchUser = async () => {
        if (!isAuthenticated) {
            setUser(null)
            setIsLoading(false)
            return
        }

        try {
            const response = await fetch('/post/user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })

            if (response.ok) {
                const data = await response.json()
                if (data.user) {
                    setUser(data.user)
                }
            }
        } catch (error) {
            console.error('Failed to fetch user configuration:', error)
        } finally {
            setIsLoading(false)
        }
    }

    useEffect(() => {
        if (!authLoading) {
            if (isAuthenticated) {
                fetchUser()
            } else {
                setUser(null)
                setIsLoading(false)
            }
        }
    }, [isAuthenticated, authLoading])

    return (
        <UserContext.Provider value={{ user, isLoading, refreshUser: fetchUser }}>
            {children}
        </UserContext.Provider>
    )
}

export function useUser() {
    const context = useContext(UserContext)
    if (context === undefined) {
        throw new Error('useUser must be used within a UserProvider')
    }
    return context
}
