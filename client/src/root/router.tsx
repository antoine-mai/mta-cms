import {
    BrowserRouter,
    Routes,
    Route,
    Navigate,
    Outlet
} from 'react-router-dom'

import {
    Suspense,
    lazy
} from 'react'

import { useAuth } from './contexts/auth'
import DefaultLayout from './layouts/default'
import LoadingPage from './pages/loading'

// Main Pages
const DashboardCmsPages = lazy(() => import('./pages/dashboard/pages/cms/pages/pages'))
const DashboardEcommerce = lazy(() => import('./pages/dashboard/pages/ecommerce'))
const DashboardOverview = lazy(() => import('./pages/dashboard/pages/overview'))
const DashboardSites = lazy(() => import('./pages/dashboard/pages/sites'))
const DashboardCms = lazy(() => import('./pages/dashboard/pages/cms'))
const DashboardPage = lazy(() => import('./pages/dashboard'))

const WelcomePage = lazy(() => import('./pages/welcome'))

const BuilderPage = lazy(() => import('./pages/builder'))

const BackupPage = lazy(() => import('./pages/backup'))

const FilesPage = lazy(() => import('./pages/files'))

const LoginPage = lazy(() => import('./pages/login'))



function ProtectedRoute() {
    const { isAuthenticated, isLoading } = useAuth()

    if (isLoading) return <LoadingPage />

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />
    }

    return <Outlet />
}

export default function Router() {
    return (
        <BrowserRouter basename="/root">
            <Suspense fallback={<LoadingPage />}>
                <Routes>
                    <Route path="/login" element={<LoginPage />} />
                    <Route element={<ProtectedRoute />}>
                        <Route element={<DefaultLayout />}>
                            <Route index element={<WelcomePage />} />

                            <Route path="dashboard" element={<DashboardPage />}>
                                <Route index element={<DashboardOverview />} />
                                <Route path="sites/*" element={<DashboardSites />} />
                                <Route path="pages" element={<DashboardCmsPages />} />
                                <Route path="cms/*" element={<DashboardCms />} />
                                <Route path="ecommerce/*" element={<DashboardEcommerce />} />
                            </Route>
                            <Route path="builder" element={<BuilderPage />}>
                                <Route index element={<div className="text-2xl font-bold">Builder Home</div>} />
                                <Route path="templates" element={<div className="text-2xl font-bold">Templates Editor</div>} />
                                <Route path="components" element={<div className="text-2xl font-bold">Components Library</div>} />
                                <Route path="assets" element={<div className="text-2xl font-bold">Asset Manager</div>} />
                                <Route path="settings" element={<div className="text-2xl font-bold">Builder Settings</div>} />
                            </Route>
                            <Route path="backup/*" element={<BackupPage />} />
                            <Route path="files" element={<FilesPage />} />

                            <Route path="*" element={<Navigate to="dashboard" replace />} />
                        </Route>
                    </Route>
                </Routes>
            </Suspense>
        </BrowserRouter>
    )
}