import {
    BrowserRouter,
    Routes,
    Route
} from 'react-router-dom'

import {
    Suspense,
    lazy
} from 'react'

import PublicLayout from '@/layouts/public'
import AdminLayout from '@/layouts/admin'

import LoadingPage from '@/pages/loading'

const LoginPage = lazy(() => import('@/pages/login'))
const HomePage = lazy(() => import('@/pages/home'))
const SubPage = lazy(() => import('@/pages/sub'))

const AdminPage = lazy(() => import('@/pages/admin'))
const AdminProfilePage = lazy(() => import('@/pages/admin/profile'))
const FilesPage = lazy(() => import('@/tools/files'))

export default function Router() {
    return (
        <BrowserRouter>
            <Suspense fallback={<LoadingPage />}>
                <Routes>
                    <Route path="/" element={<PublicLayout />}>
                        <Route index element={<HomePage />} />
                        <Route path="login" element={<LoginPage />} />
                        <Route path="*" element={<SubPage />} />
                    </Route>
                    <Route path="admin/" element={<AdminLayout />}>
                        <Route index element={<AdminPage />} />
                        <Route path="profile" element={<AdminProfilePage />} />
                        <Route path="tools/files" element={<FilesPage />} />
                    </Route>
                </Routes>
            </Suspense>
        </BrowserRouter>
    )
}