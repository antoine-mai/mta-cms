import { createContext, useContext, useState, useEffect, type ReactNode } from 'react'

interface AuthContextType {
    isAuthenticated: boolean
    isLoading: boolean
    isRoot: boolean
    login: (username: string, password: string) => Promise<void>
    logout: () => void
    checkAuth: () => Promise<boolean>
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
    const [isAuthenticated, setIsAuthenticated] = useState(false)
    const [isLoading, setIsLoading] = useState(true)
    const [isRoot, setIsRoot] = useState(false)

    // Check if user is authenticated by checking session with backend
    const checkAuth = async (): Promise<boolean> => {
        try {
            // Check session via API
            // We use /post/user which likely requires auth and returns user info
            const response = await fetch('/post/user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })

            if (response.ok) {
                const data = await response.json()
                // If the request succeeds, we are authenticated
                setIsAuthenticated(true)
                setIsRoot(data.user?.is_root === true)
                return true
            } else {
                // 401 Unauthorized or other error
                setIsAuthenticated(false)
                setIsRoot(false)
                return false
            }
        } catch (error) {
            console.error('Auth check failed:', error)
            setIsAuthenticated(false)
            setIsRoot(false)
            return false
        } finally {
            setIsLoading(false)
        }
    }

    // Login function
    const login = async (username: string, password: string) => {
        try {
            const response = await fetch('/post/user/auth', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            })

            if (!response.ok) {
                throw new Error('Login failed')
            }

            const data = await response.json()
            if (data.status === 'ok') {
                setIsAuthenticated(true)
                // Check privilege after login
                await checkAuth()
            } else {
                throw new Error('Login failed')
            }
        } catch (error) {
            console.error('Login error:', error)
            throw error
        }
    }

    // Logout function
    const logout = () => {
        // Clear all mta_session cookies
        const cookies = document.cookie.split(';')
        cookies.forEach(cookie => {
            const cookieName = cookie.split('=')[0].trim()
            if (cookieName.startsWith('mta_session_')) {
                document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`
            }
        })
        setIsAuthenticated(false)
        setIsRoot(false)
    }

    // Check auth on mount
    useEffect(() => {
        checkAuth()
    }, [])

    return (
        <AuthContext.Provider value={{ isAuthenticated, isLoading, isRoot, login, logout, checkAuth }}>
            {children}
        </AuthContext.Provider>
    )
}

export function useAuth() {
    const context = useContext(AuthContext)
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider')
    }
    return context
}
