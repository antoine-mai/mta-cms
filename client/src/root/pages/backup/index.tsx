import { BackupRoutes } from './routes'

export default function BackupPage() {
    return (
        <div className="flex-1 h-full overflow-y-auto bg-slate-50 dark:bg-slate-900/50">
            <div className="max-w-6xl mx-auto h-full flex flex-col">
                <BackupRoutes />
            </div>
        </div>
    )
}