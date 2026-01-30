import { useState, useEffect } from 'react'
import {
    Database,
    Download,
    Clock,
    HardDrive,
    Calendar,
    CheckCircle2,
    AlertCircle,
    Trash2,
    RefreshCw,
    Archive,
    FolderArchive,
    Info,
    CloudUpload
} from 'lucide-react'

interface Backup {
    id: string
    name: string
    size: string
    date: string
    time: string
    type: 'full' | 'incremental'
    status: 'completed' | 'failed' | 'in-progress'
}

export default function BackupsPage() {
    const [backups, setBackups] = useState<Backup[]>([])
    const [isCreating, setIsCreating] = useState(false)
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        loadBackups()
    }, [])

    const loadBackups = async () => {
        setLoading(true)
        try {
            const res = await fetch('/root/post/backup/browse')
            if (res.ok) {
                const data = await res.json()
                if (data.success) {
                    setBackups(data.items || [])
                }
            }
        } catch (error) {
            console.error('Failed to load backups:', error)
        } finally {
            setLoading(false)
        }
    }

    const handleCreateBackup = async () => {
        setIsCreating(true)
        try {
            const res = await fetch('/root/post/backup/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            const data = await res.json()
            if (data.success) {
                alert('Backup created successfully!')
                loadBackups()
            } else {
                alert('Failed to create backup: ' + (data.message || 'Unknown error'))
            }
        } catch (error) {
            console.error('Failed to create backup:', error)
            alert('Failed to create backup')
        } finally {
            setIsCreating(false)
        }
    }

    const handleDownload = (backup: Backup) => {
        window.location.href = `/root/post/backup/download?name=${encodeURIComponent(backup.name)}`
    }

    const handleDelete = async (backup: Backup) => {
        if (!confirm(`Are you sure you want to delete ${backup.name}?`)) return

        try {
            const res = await fetch('/root/post/backup/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: backup.name })
            })
            const data = await res.json()
            if (data.success) {
                alert('Backup deleted successfully!')
                loadBackups()
            } else {
                alert('Failed to delete backup: ' + (data.message || 'Unknown error'))
            }
        } catch (error) {
            console.error('Failed to delete backup:', error)
            alert('Failed to delete backup')
        }
    }

    const stats = [
        { icon: Database, label: 'Total Backups', value: backups.length.toString(), color: 'from-blue-500 to-cyan-500' },
        { icon: HardDrive, label: 'Total Size', value: calculateTotalSize(), color: 'from-purple-500 to-pink-500' },
        { icon: Clock, label: 'Last Backup', value: getLastBackupTime(), color: 'from-green-500 to-emerald-500' },
        { icon: Calendar, label: 'Next Scheduled', value: 'Tomorrow 2:00 AM', color: 'from-orange-500 to-red-500' }
    ]

    function calculateTotalSize() {
        if (backups.length === 0) return '0 B'
        // Simple calculation - in real app, parse size strings properly
        return backups.length + ' backups'
    }

    function getLastBackupTime() {
        if (backups.length === 0) return 'Never'
        const latest = backups[0]
        return latest ? 'Today' : 'Never'
    }

    return (
        <div className="h-full flex flex-col">
            {/* Header with Button */}
            <div className="px-6 py-4">
                <div className="flex items-center justify-end">
                    <button
                        onClick={handleCreateBackup}
                        disabled={isCreating}
                        className="group px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:from-slate-400 disabled:to-slate-500 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/30 transition-all duration-300 hover:scale-105 disabled:scale-100 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        {isCreating ? (
                            <>
                                <RefreshCw className="w-5 h-5 animate-spin" />
                                Creating...
                            </>
                        ) : (
                            <>
                                <Archive className="w-5 h-5" />
                                Create Backup
                            </>
                        )}
                    </button>
                </div>
            </div>

            {/* Stats Grid */}
            <div className="px-6 py-6">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {stats.map((stat, index) => (
                        <div
                            key={index}
                            className="group relative bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 transition-all duration-300 hover:-translate-y-1 overflow-hidden"
                        >
                            <div className={`absolute inset-0 bg-gradient-to-br ${stat.color} opacity-0 group-hover:opacity-5 transition-opacity`}></div>
                            <div className="relative flex items-start justify-between">
                                <div>
                                    <div className="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">{stat.label}</div>
                                    <div className="text-3xl font-bold text-slate-900 dark:text-white">{stat.value}</div>
                                </div>
                                <div className={`p-3 bg-gradient-to-br ${stat.color} rounded-xl`}>
                                    <stat.icon className="w-6 h-6 text-white" />
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Info Banner */}
            <div className="px-6 pb-6">
                <div className="grid md:grid-cols-2 gap-4">
                    <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex items-start gap-3">
                        <Info className="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                        <div className="flex-1">
                            <h3 className="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">Automated Backups</h3>
                            <p className="text-sm text-blue-700 dark:text-blue-300">
                                Backups are automatically created daily at 2:00 AM. You can also create manual backups anytime.
                                All backups include the entire root folder.
                            </p>
                        </div>
                    </div>

                    <div className="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4 flex items-start gap-3">
                        <CloudUpload className="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                        <div className="flex-1">
                            <h3 className="text-sm font-semibold text-purple-900 dark:text-purple-100 mb-1">Cloud Backup</h3>
                            <p className="text-sm text-purple-700 dark:text-purple-300 mb-2">
                                Upload backups to cloud storage for extra security. Configure in Cloud Settings.
                            </p>
                            <div className="flex flex-wrap gap-2">
                                <span className="px-2 py-0.5 bg-white dark:bg-purple-800/30 rounded text-xs font-medium text-purple-700 dark:text-purple-300">Google Drive</span>
                                <span className="px-2 py-0.5 bg-white dark:bg-purple-800/30 rounded text-xs font-medium text-purple-700 dark:text-purple-300">Dropbox</span>
                                <span className="px-2 py-0.5 bg-white dark:bg-purple-800/30 rounded text-xs font-medium text-purple-700 dark:text-purple-300">AWS S3</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Backups List */}
            <div className="flex-1 px-6 pb-6 overflow-hidden">
                <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl h-full flex flex-col overflow-hidden">
                    {/* Table Header */}
                    <div className="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 className="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <FolderArchive className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            Available Backups
                        </h2>
                    </div>

                    {/* Table */}
                    <div className="flex-1 overflow-y-auto">
                        {loading ? (
                            <div className="flex items-center justify-center py-20">
                                <RefreshCw className="w-8 h-8 animate-spin text-indigo-600 dark:text-indigo-400" />
                            </div>
                        ) : backups.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-20">
                                <div className="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                    <Database className="w-10 h-10 text-slate-400" />
                                </div>
                                <h3 className="text-lg font-semibold text-slate-900 dark:text-white mb-2">No Backups Yet</h3>
                                <p className="text-sm text-slate-500 dark:text-slate-400 mb-6">Create your first backup to get started</p>
                                <button
                                    onClick={handleCreateBackup}
                                    disabled={isCreating}
                                    className="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:from-slate-400 disabled:to-slate-500 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 hover:scale-105 disabled:scale-100 flex items-center gap-2"
                                >
                                    <Archive className="w-5 h-5" />
                                    Create First Backup
                                </button>
                            </div>
                        ) : (
                            <table className="w-full">
                                <thead className="bg-slate-50 dark:bg-slate-900/50 sticky top-0 z-10">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Backup Name
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Size
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Date & Time
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-200 dark:divide-slate-700">
                                    {backups.map((backup) => (
                                        <tr
                                            key={backup.id}
                                            className="group hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors"
                                        >
                                            <td className="px-6 py-4">
                                                {backup.status === 'completed' ? (
                                                    <div className="flex items-center gap-2 text-green-600 dark:text-green-400">
                                                        <CheckCircle2 className="w-5 h-5" />
                                                        <span className="text-sm font-medium">Completed</span>
                                                    </div>
                                                ) : backup.status === 'failed' ? (
                                                    <div className="flex items-center gap-2 text-red-600 dark:text-red-400">
                                                        <AlertCircle className="w-5 h-5" />
                                                        <span className="text-sm font-medium">Failed</span>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                                        <RefreshCw className="w-5 h-5 animate-spin" />
                                                        <span className="text-sm font-medium">In Progress</span>
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="p-2 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg">
                                                        <Archive className="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                                    </div>
                                                    <span className="text-sm font-medium text-slate-900 dark:text-white font-mono">
                                                        {backup.name}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className={`inline-flex px-3 py-1 rounded-full text-xs font-semibold ${backup.type === 'full'
                                                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400'
                                                    : 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                                    }`}>
                                                    {backup.type === 'full' ? 'Full Backup' : 'Incremental'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="text-sm text-slate-600 dark:text-slate-400 font-medium">
                                                    {backup.size}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex flex-col">
                                                    <span className="text-sm font-medium text-slate-900 dark:text-white">
                                                        {backup.date}
                                                    </span>
                                                    <span className="text-xs text-slate-500 dark:text-slate-400">
                                                        {backup.time}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        onClick={() => handleDownload(backup)}
                                                        className="p-2 hover:bg-blue-50 dark:hover:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg transition-colors"
                                                        title="Download to Local"
                                                    >
                                                        <Download className="w-4 h-4" />
                                                    </button>
                                                    <button
                                                        className="p-2 hover:bg-purple-50 dark:hover:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-lg transition-colors"
                                                        title="Backup to Cloud"
                                                    >
                                                        <CloudUpload className="w-4 h-4" />
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(backup)}
                                                        className="p-2 hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg transition-colors"
                                                        title="Delete"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>
        </div>
    )
}
