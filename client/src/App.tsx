import { useState, useEffect } from 'react'

interface ApiResponse {
    status: string;
    message: string;
    data: any;
}

function App() {
    const [data, setData] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch('http://localhost:8000/api/status')
            .then(res => res.json())
            .then((json: ApiResponse) => {
                setData(json.data);
                setLoading(false);
            })
            .catch(err => {
                console.error("Failed to fetch from PHP server:", err);
                setLoading(false);
            });
    }, []);

    return (
        <div className="min-h-screen p-8 max-w-5xl mx-auto">
            <header className="mb-12">
                <h1 className="text-4xl font-extrabold mb-2">MTA-APP <span className="gradient-text">Dashboard</span></h1>
                <p className="text-gray-400">Powered by PHP Backend & React Frontend</p>
            </header>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="glass-panel p-6">
                    <h3 className="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Backend Status</h3>
                    <div className="flex items-center gap-2">
                        <div className={`w-2 h-2 rounded-full ${loading ? 'bg-yellow-500 animate-pulse' : 'bg-green-500'}`}></div>
                        <span className="text-2xl font-bold">{loading ? 'Connecting...' : (data?.online ? 'Online' : 'Offline')}</span>
                    </div>
                </div>

                <div className="glass-panel p-6">
                    <h3 className="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">PHP Version</h3>
                    <span className="text-2xl font-bold">{data?.php_version || 'N/A'}</span>
                </div>

                <div className="glass-panel p-6">
                    <h3 className="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Environment</h3>
                    <span className="text-2xl font-bold text-indigo-400">Development</span>
                </div>
            </div>

            <div className="glass-panel p-8">
                <h2 className="text-2xl font-bold mb-6">System Information</h2>
                {loading ? (
                    <div className="space-y-4">
                        <div className="h-4 bg-white/5 rounded w-3/4 animate-pulse"></div>
                        <div className="h-4 bg-white/5 rounded w-1/2 animate-pulse"></div>
                        <div className="h-4 bg-white/5 rounded w-2/3 animate-pulse"></div>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-white/10 text-gray-400 text-sm">
                                    <th className="pb-4">Property</th>
                                    <th className="pb-4">Value</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-white/5">
                                <tr>
                                    <td className="py-4 text-gray-300">Server Software</td>
                                    <td className="py-4 font-mono text-sm">{data?.server}</td>
                                </tr>
                                <tr>
                                    <td className="py-4 text-gray-300">Memory Usage</td>
                                    <td className="py-4 font-mono text-sm">{(data?.memory_usage / 1024 / 1024).toFixed(2)} MB</td>
                                </tr>
                                <tr>
                                    <td className="py-4 text-gray-300">API Endpoint</td>
                                    <td className="py-4 font-mono text-sm">/api/status</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            <footer className="mt-12 text-center text-gray-500 text-sm">
                &copy; 2026 MTA-APP Modern CMS. Built with PHP (No Composer) & React.
            </footer>
        </div>
    )
}

export default App
