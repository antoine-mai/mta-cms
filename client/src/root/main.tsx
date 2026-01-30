import { createRoot } from 'react-dom/client'
import { StrictMode, Suspense } from 'react'

import './assets/styles.css'

import { ThemeProvider } from './contexts/theme'
import { AuthProvider } from './contexts/auth'
import { StatsProvider } from './contexts/stats'
import Router from './router'
import LoadingPage from './pages/loading'

function Main() {
    return <StrictMode>
        <Suspense fallback={<LoadingPage />}>
            <ThemeProvider defaultTheme="system" storageKey="vite-ui-theme">
                <AuthProvider>
                    <StatsProvider>
                        <Router />
                    </StatsProvider>
                </AuthProvider>
            </ThemeProvider>
        </Suspense>
    </StrictMode>
}

const root = document.getElementById('root')!
createRoot(root).render(<Main />)
