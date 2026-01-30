import { lazy } from 'react'
import { Routes, Route } from 'react-router-dom'

const BackupsPage = lazy(() => import('./pages/backups'))

export function BackupRoutes() {
    return (
        <Routes>
            <Route index element={<BackupsPage />} />
        </Routes>
    )
}
