import Header from '@/layouts/admin/components/header'
import { useUser } from '@/contexts/user'

export default function ProfilePage() {
    const { user } = useUser()

    return (
        <main className="flex-1 flex flex-col min-w-0 bg-background-main relative">
            <Header />
            <div className="flex-1 p-8">
                <div className="max-w-2xl mx-auto">
                    <div className="mb-8">
                        <h1 className="text-2xl font-bold text-text-main mb-2">User Profile</h1>
                        <p className="text-sm text-text-secondary">Manage your account information and preferences.</p>
                    </div>

                    <div className="bg-card-bg rounded-xl border border-border-light shadow-sm overflow-hidden">
                        <div className="p-6 border-b border-border-light flex items-center gap-6">
                            <div className="size-20 rounded-full bg-cover bg-center border-2 border-primary shadow-md" style={{ backgroundImage: 'url("https://lh3.googleusercontent.com/aida-public/AB6AXuDMZx_Qjb8Qr3QNVYAUinm_dXvemKEXziZNN3lGjT-xG_CbfGNBBDmLzCsq3RmFT9sh0gP7tsyzCS1iC9TB47NRFFw1f7v8Pl74S5TAuQOe7MIAx7xLofWvQ-Axm5-NQArCHnbC3TAc-7MYpn1ohgXiGSV12GRUyInB29J3k3ua7VujSoW0SB0pvZwz7jRQEmYw340WCze2IdlQNLv690XCMcpJ9QIoGXYp1bP2VhOvYr25I6eGt9XnaAKbvbQXMXsIeJZSFldlTYDq")' }}></div>
                            <div>
                                <h2 className="text-xl font-bold text-text-main">{user?.username}</h2>
                                <p className="text-sm text-text-secondary">Administrator</p>
                            </div>
                        </div>

                        <div className="p-6 space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-xs font-bold text-text-muted uppercase tracking-wider mb-2">Username</label>
                                    <div className="px-4 py-2.5 bg-background-light border border-border-light rounded-lg text-text-secondary font-medium">
                                        {user?.username}
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-text-muted uppercase tracking-wider mb-2">Role</label>
                                    <div className="px-4 py-2.5 bg-background-light border border-border-light rounded-lg text-text-secondary font-medium">
                                        Admin
                                    </div>
                                </div>
                            </div>

                            <div className="pt-4 border-t border-border-light">
                                <button className="px-6 py-2.5 bg-primary text-white font-bold rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95">
                                    Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}
