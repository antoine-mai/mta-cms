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

    // Check if user is authenticated
    const checkAuth = async (): Promise<boolean> => {
        try {
            const response = await fetch('/root/post/user', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })

            if (response.ok) {
                const data = await response.json()
                setIsAuthenticated(data.isLoggedIn === true)
                setIsRoot(data.isLoggedIn === true) // For now assume admin is root if logged in
                return data.isLoggedIn === true
            }

            setIsAuthenticated(false)
            setIsRoot(false)
            return false
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
            const formData = new FormData()
            formData.append('username', username)
            formData.append('password', password)

            const response = await fetch('/root/post/user/login', {
                method: 'POST',
                body: formData,
            })

            const data = await response.json()
            if (response.ok && data.success) {
                setIsAuthenticated(true)
                await checkAuth()
            } else {
                throw new Error(data.message || 'Login failed')
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

    // Check auth on mount and poll
    useEffect(() => {
        checkAuth()

        const intervalId = setInterval(() => {
            if (isAuthenticated) {
                checkAuth()
            }
        }, 5000)

        return () => clearInterval(intervalId)
    }, [isAuthenticated])

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