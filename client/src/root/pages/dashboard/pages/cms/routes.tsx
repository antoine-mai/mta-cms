import { lazy } from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'

const DashboardCmsCategories = lazy(() => import('./pages/categories'))
const DashboardCmsPosts = lazy(() => import('./pages/posts'))
const DashboardCmsTags = lazy(() => import('./pages/tags'))

export function CmsRoutes() {
    return (
        <Routes>
            <Route index element={<Navigate to="categories" replace />} />
            <Route path="categories" element={<DashboardCmsCategories />} />
            <Route path="posts" element={<DashboardCmsPosts />} />
            <Route path="tags" element={<DashboardCmsTags />} />
        </Routes>
    )
}
