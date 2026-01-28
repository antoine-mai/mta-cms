import { createRoot } from 'react-dom/client'
import { StrictMode } from 'react'
import './assets/css/style.css'

import { ThemeProvider } from './contexts/theme'
import { AuthProvider } from './contexts/auth'
import { AppProvider } from './contexts/app'
import { UserProvider } from './contexts/user'

import Router from './router'

function Main() {
    return <StrictMode>
        <ThemeProvider defaultTheme="light" storageKey="vite-ui-theme">
            <AuthProvider>
                <UserProvider>
                    <AppProvider>
                        <Router />
                    </AppProvider>
                </UserProvider>
            </AuthProvider>
        </ThemeProvider>
    </StrictMode>
}

const root = document.getElementById('root')!
createRoot(root).render(<Main />)
