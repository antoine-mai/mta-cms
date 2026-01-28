import Header from '@/layouts/admin/components/header'

export default function AdminPage() {
    return (
        <main className="flex-1 flex flex-col min-w-0 bg-background-main relative">
            <Header />
            <div className="flex-1 p-6 flex flex-col items-center justify-center text-center">
                <div className="bg-card-bg p-8 rounded-xl border border-border-light shadow-sm max-w-md w-full">
                    <span className="material-symbols-outlined text-4xl text-primary mb-4">dashboard</span>
                    <h1 className="text-xl font-bold text-text-main mb-2">Welcome to MTA-APP</h1>
                    <p className="text-sm text-text-secondary">
                        Select a module from the sidebar to get started.
                    </p>
                </div>
            </div>
        </main>
    )
}