import Sidebar from './components/sidebar'
import { CmsRoutes } from './routes'

export default function CmsPage() {
    return (
        <div className="flex h-[calc(100%+3rem)] w-[calc(100%+3rem)] -m-6 overflow-hidden">
            <Sidebar />

            <main className="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900/50">
                <CmsRoutes />
            </main>
        </div>
    )
}
