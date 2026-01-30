import { lazy } from 'react'
import { Routes, Route } from 'react-router-dom'

const DashboardSitesAll = lazy(() => import('./pages/all-sites'))
const DashboardSitesSettings = lazy(() => import('./pages/settings'))
const DashboardSitesAnalytics = lazy(() => import('./pages/analytics'))

export function SitesRoutes() {
    return (
        <Routes>
            <Route index element={<DashboardSitesAll />} />
            <Route path="settings" element={<DashboardSitesSettings />} />
            <Route path="analytics" element={<DashboardSitesAnalytics />} />
        </Routes>
    )
}
