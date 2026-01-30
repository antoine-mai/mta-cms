import { Outlet } from 'react-router-dom'
import Sidebar from './components/sidebar'

export default function BuilderPage() {
    return (
        <div className="flex h-full">
            <Sidebar />

            {/* Main Content */}
            <main className="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900/50 p-6">
                <Outlet />
            </main>
        </div>
    )
}