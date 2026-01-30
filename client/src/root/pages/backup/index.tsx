import { Outlet } from 'react-router-dom'
import Sidebar from './components/sidebar'

export default function BackupPage() {
    return (
        <div className="flex h-full w-full overflow-hidden bg-slate-50 dark:bg-slate-900/50">
            <Sidebar />
            <main className="flex-1 overflow-y-auto">
                <Outlet />
            </main>
        </div>
    )
}