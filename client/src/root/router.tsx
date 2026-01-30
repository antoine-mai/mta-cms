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

const DashboardEcommerceAttributeSets = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/attribute-sets'))
const DashboardEcommerceCategories = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/categories'))
const DashboardEcommerceAttributes = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/attributes'))
const DashboardEcommerceCustomers = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/customers'))
const DashboardEcommerceAnalytics = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/analytics'))
const DashboardEcommerceOrders = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/orders'))
const DashboardEcommerceTags = lazy(() => import('./pages/dashboard/pages/ecommerce/pages/tags'))
const DashboardCmsCategories = lazy(() => import('./pages/dashboard/pages/cms/pages/categories'))
const DashboardCmsPosts = lazy(() => import('./pages/dashboard/pages/cms/pages/posts'))
const DashboardCmsPages = lazy(() => import('./pages/dashboard/pages/cms/pages/pages'))
const DashboardCmsTags = lazy(() => import('./pages/dashboard/pages/cms/pages/tags'))
const DashboardCmsSites = lazy(() => import('./pages/dashboard/pages/cms/pages/sites'))
const DashboardEcommerce = lazy(() => import('./pages/dashboard/pages/ecommerce'))
const DashboardOverview = lazy(() => import('./pages/dashboard/pages/overview'))
const DashboardCms = lazy(() => import('./pages/dashboard/pages/cms'))
const DashboardPage = lazy(() => import('./pages/dashboard'))
const WelcomePage = lazy(() => import('./pages/welcome'))
const BackupPage = lazy(() => import('./pages/backup'))
const BackupsPage = lazy(() => import('./pages/backup/pages/backups'))
const BackupCloudSettings = lazy(() => import('./pages/backup/pages/cloud-settings'))
const BackupSettings = lazy(() => import('./pages/backup/pages/settings'))
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
                                <Route index element={<Navigate to="overview" replace />} />
                                <Route path="overview" element={<DashboardOverview />} />
                                <Route path="cms" element={<DashboardCms />}>
                                    <Route index element={<Navigate to="categories" replace />} />
                                    <Route path="categories" element={<DashboardCmsCategories />} />
                                    <Route path="posts" element={<DashboardCmsPosts />} />
                                    <Route path="pages" element={<DashboardCmsPages />} />
                                    <Route path="tags" element={<DashboardCmsTags />} />
                                    <Route path="sites" element={<DashboardCmsSites />} />
                                </Route>
                                <Route path="ecommerce" element={<DashboardEcommerce />}>
                                    <Route index element={<DashboardEcommerceCategories />} />
                                    <Route path="attribute-sets" element={<DashboardEcommerceAttributeSets />} />
                                    <Route path="attributes" element={<DashboardEcommerceAttributes />} />
                                    <Route path="tags" element={<DashboardEcommerceTags />} />
                                    <Route path="orders" element={<DashboardEcommerceOrders />} />
                                    <Route path="customers" element={<DashboardEcommerceCustomers />} />
                                    <Route path="analytics" element={<DashboardEcommerceAnalytics />} />
                                </Route>
                            </Route>

                            <Route path="backup" element={<BackupPage />}>
                                <Route index element={<BackupsPage />} />
                                <Route path="cloud-settings" element={<BackupCloudSettings />} />
                                <Route path="settings" element={<BackupSettings />} />
                            </Route>
                            <Route path="files" element={<FilesPage />} />

                            <Route path="*" element={<Navigate to="dashboard" replace />} />
                        </Route>
                    </Route>
                </Routes>
            </Suspense>
        </BrowserRouter>
    )
}