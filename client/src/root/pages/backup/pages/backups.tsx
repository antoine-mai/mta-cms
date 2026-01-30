import { useState, useEffect } from 'react'
import {
    Database,
    RefreshCw,
    Archive,
    Settings,
    Clock,
    HardDrive,
    Calendar
} from 'lucide-react'

import type { Backup, CloudProvider } from '../types'
import { StatsGrid } from '../components/stats-grid'
import { BackupsTable } from '../components/backups-table'
import { CloudModal } from '../components/cloud-modal'

export default function BackupsPage() {
    const [backups, setBackups] = useState<Backup[]>([])
    const [isCreating, setIsCreating] = useState(false)
    const [loading, setLoading] = useState(true)
    const [showCloudModal, setShowCloudModal] = useState(false)

    // Cloud Settings State
    const [providers, setProviders] = useState<CloudProvider[]>([
        {
            id: 'google_drive',
            name: 'Google Drive',
            enabled: false,
            config: { client_id: '', client_secret: '', refresh_token: '' }
        },
        {
            id: 'dropbox',
            name: 'Dropbox',
            enabled: false,
            config: { access_token: '', app_key: '', app_secret: '' }
        },
        {
            id: 'aws_s3',
            name: 'AWS S3',
            enabled: false,
            config: { access_key_id: '', secret_access_key: '', bucket_name: '', region: 'us-east-1' }
        }
    ])
    const [selectedProviderId, setSelectedProviderId] = useState('google_drive')
    const [saving, setSaving] = useState(false)
    const [saveStatus, setSaveStatus] = useState<'idle' | 'success' | 'error'>('idle')
    const [frequency, setFrequency] = useState('daily')
    const [hour, setHour] = useState('02')

    useEffect(() => {
        loadBackups()
        loadCloudSettings()
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

    const loadCloudSettings = async () => {
        try {
            const res = await fetch('/root/post/backup/cloud-settings')
            if (res.ok) {
                const data = await res.json()
                if (data.success) {
                    if (data.providers) setProviders(data.providers)
                    if (data.schedule) {
                        setFrequency(data.schedule.frequency || 'daily')
                        setHour(data.schedule.hour || '02')
                    }
                }
            }
        } catch (error) {
            console.error('Failed to load cloud settings:', error)
        }
    }

    const handleSaveCloudSettings = async () => {
        setSaving(true)
        setSaveStatus('idle')
        try {
            const res = await fetch('/root/post/backup/cloud-settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    providers,
                    schedule: { frequency, hour }
                })
            })
            const data = await res.json()
            if (data.success) {
                setSaveStatus('success')
                setTimeout(() => {
                    setSaveStatus('idle')
                    setShowCloudModal(false)
                }, 1500)
            } else {
                setSaveStatus('error')
            }
        } catch (error) {
            console.error('Failed to save cloud settings:', error)
            setSaveStatus('error')
        } finally {
            setSaving(false)
        }
    }

    const updateSchedule = (field: 'frequency' | 'hour', value: string) => {
        if (field === 'frequency') setFrequency(value)
        else setHour(value)
    }

    const updateProvider = (providerId: string, field: string, value: string | boolean) => {
        setProviders(prev => prev.map(p => {
            if (p.id === providerId) {
                if (field === 'enabled') {
                    return { ...p, enabled: value as boolean }
                } else {
                    return { ...p, config: { ...p.config, [field]: value as string } }
                }
            }
            return p
        }))
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
                loadBackups()
            }
        } catch (error) {
            console.error('Failed to create backup:', error)
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
                loadBackups()
            }
        } catch (error) {
            console.error('Failed to delete backup:', error)
        }
    }

    const stats = [
        {
            icon: Database,
            label: 'Total Backups',
            value: backups.length.toString(),
            color: 'from-blue-500 to-cyan-500'
        },
        {
            icon: HardDrive,
            label: 'Total Size',
            value: backups.length > 0 ? `${backups.length} backups` : '0 B',
            color: 'from-purple-500 to-pink-500'
        },
        {
            icon: Clock,
            label: 'Last Backup',
            value: backups.length > 0 ? 'Today' : 'Never',
            color: 'from-green-500 to-emerald-500'
        },
        {
            icon: Calendar,
            label: 'Next Scheduled',
            value: 'Tomorrow 2:00 AM',
            color: 'from-orange-500 to-red-500'
        }
    ]

    return (
        <div className="flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden my-6 font-display">
            {/* Header */}
            <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900 flex-shrink-0">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl">
                        <Database className="w-5 h-5 text-white" />
                    </div>
                    <h2 className="font-bold text-lg text-slate-900 dark:text-white">Backup Storage</h2>
                </div>

                <div className="flex items-center gap-2">
                    <button
                        onClick={handleCreateBackup}
                        disabled={isCreating}
                        className="group px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:from-slate-400 disabled:to-slate-500 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all duration-300 hover:scale-105 active:scale-95 disabled:scale-100 disabled:cursor-not-allowed flex items-center gap-2 text-sm"
                    >
                        {isCreating ? (
                            <RefreshCw className="w-4 h-4 animate-spin" />
                        ) : (
                            <Archive className="w-4 h-4" />
                        )}
                        {isCreating ? 'Creating...' : 'Create Backup'}
                    </button>
                    <button
                        onClick={() => setShowCloudModal(true)}
                        className="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl transition-all active:scale-95"
                    >
                        <Settings className="w-5 h-5" />
                    </button>
                </div>
            </div>

            {/* Scrollable Content */}
            <div className="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30 dark:bg-slate-900/20">
                <StatsGrid stats={stats} />
                <BackupsTable
                    loading={loading}
                    backups={backups}
                    onDownload={handleDownload}
                    onDelete={handleDelete}
                />
            </div>

            <CloudModal
                isOpen={showCloudModal}
                onClose={() => setShowCloudModal(false)}
                providers={providers}
                selectedProviderId={selectedProviderId}
                onSelectProvider={setSelectedProviderId}
                onUpdateProvider={updateProvider}
                onSave={handleSaveCloudSettings}
                saving={saving}
                saveStatus={saveStatus}
                frequency={frequency}
                hour={hour}
                onUpdateSchedule={updateSchedule}
            />
        </div>
    )
}
