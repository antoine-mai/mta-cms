import { lazy } from 'react'
import { Routes, Route } from 'react-router-dom'

const DashboardEcommerceCategories = lazy(() => import('./pages/categories'))
const DashboardEcommerceAttributeSets = lazy(() => import('./pages/attribute-sets'))
const DashboardEcommerceAttributes = lazy(() => import('./pages/attributes'))
const DashboardEcommerceTags = lazy(() => import('./pages/tags'))
const DashboardEcommerceOrders = lazy(() => import('./pages/orders'))
const DashboardEcommerceCustomers = lazy(() => import('./pages/customers'))
const DashboardEcommerceAnalytics = lazy(() => import('./pages/analytics'))

export function EcommerceRoutes() {
    return (
        <Routes>
            <Route index element={<DashboardEcommerceCategories />} />
            <Route path="attribute-sets" element={<DashboardEcommerceAttributeSets />} />
            <Route path="attributes" element={<DashboardEcommerceAttributes />} />
            <Route path="tags" element={<DashboardEcommerceTags />} />
            <Route path="orders" element={<DashboardEcommerceOrders />} />
            <Route path="customers" element={<DashboardEcommerceCustomers />} />
            <Route path="analytics" element={<DashboardEcommerceAnalytics />} />
        </Routes>
    )
}
