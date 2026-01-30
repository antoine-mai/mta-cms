import { useState, useEffect } from 'react'
import { Cloud, Save, Key, CheckCircle2, AlertCircle } from 'lucide-react'

interface CloudProvider {
    id: string
    name: string
    enabled: boolean
    config: Record<string, string>
}

export default function CloudSettingsPage() {
    const [providers, setProviders] = useState<CloudProvider[]>([
        {
            id: 'google_drive',
            name: 'Google Drive',
            enabled: false,
            config: {
                client_id: '',
                client_secret: '',
                refresh_token: ''
            }
        },
        {
            id: 'dropbox',
            name: 'Dropbox',
            enabled: false,
            config: {
                access_token: '',
                app_key: '',
                app_secret: ''
            }
        },
        {
            id: 'aws_s3',
            name: 'AWS S3',
            enabled: false,
            config: {
                access_key_id: '',
                secret_access_key: '',
                bucket_name: '',
                region: 'us-east-1'
            }
        }
    ])

    const [saving, setSaving] = useState(false)
    const [saveStatus, setSaveStatus] = useState<'idle' | 'success' | 'error'>('idle')

    useEffect(() => {
        loadSettings()
    }, [])

    const loadSettings = async () => {
        try {
            const res = await fetch('/root/post/backup/cloud-settings')
            if (res.ok) {
                const data = await res.json()
                if (data.success && data.providers) {
                    setProviders(data.providers)
                }
            }
        } catch (error) {
            console.error('Failed to load cloud settings:', error)
        }
    }

    const handleSave = async () => {
        setSaving(true)
        setSaveStatus('idle')

        try {
            const res = await fetch('/root/post/backup/cloud-settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ providers })
            })
            const data = await res.json()

            if (data.success) {
                setSaveStatus('success')
                setTimeout(() => setSaveStatus('idle'), 3000)
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

    return (
        <div className="h-full flex flex-col">
            {/* Header */}
            <div className="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900 dark:text-white">Cloud Settings</h1>
                        <p className="text-sm text-slate-500 dark:text-slate-400">Configure cloud storage providers for backup</p>
                    </div>

                    <button
                        onClick={handleSave}
                        disabled={saving}
                        className="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:from-slate-400 disabled:to-slate-500 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 hover:scale-105 disabled:scale-100 flex items-center gap-2"
                    >
                        {saving ? (
                            <>
                                <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                Saving...
                            </>
                        ) : saveStatus === 'success' ? (
                            <>
                                <CheckCircle2 className="w-5 h-5" />
                                Saved!
                            </>
                        ) : saveStatus === 'error' ? (
                            <>
                                <AlertCircle className="w-5 h-5" />
                                Error
                            </>
                        ) : (
                            <>
                                <Save className="w-5 h-5" />
                                Save Settings
                            </>
                        )}
                    </button>
                </div>
            </div>

            {/* Content */}
            <div className="flex-1 overflow-y-auto p-6">
                <div className="max-w-4xl space-y-6">
                    {providers.map((provider) => (
                        <div
                            key={provider.id}
                            className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden"
                        >
                            {/* Provider Header */}
                            <div className="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl">
                                        <Cloud className="w-5 h-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-bold text-slate-900 dark:text-white">{provider.name}</h3>
                                        <p className="text-xs text-slate-500 dark:text-slate-400">
                                            {provider.enabled ? 'Enabled' : 'Disabled'}
                                        </p>
                                    </div>
                                </div>

                                <label className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={provider.enabled}
                                        onChange={(e) => updateProvider(provider.id, 'enabled', e.target.checked)}
                                        className="sr-only peer"
                                    />
                                    <div className="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            {/* Provider Config */}
                            {provider.enabled && (
                                <div className="px-6 py-4 space-y-4">
                                    {Object.entries(provider.config).map(([key, value]) => (
                                        <div key={key}>
                                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                                <div className="flex items-center gap-2">
                                                    <Key className="w-4 h-4 text-slate-400" />
                                                    {key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')}
                                                </div>
                                            </label>
                                            <input
                                                type={key.includes('secret') || key.includes('token') || key.includes('key') ? 'password' : 'text'}
                                                value={value}
                                                onChange={(e) => updateProvider(provider.id, key, e.target.value)}
                                                placeholder={`Enter ${key.split('_').join(' ')}`}
                                                className="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            />
                                        </div>
                                    ))}

                                    {/* Help Text */}
                                    <div className="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <p className="text-xs text-blue-700 dark:text-blue-300">
                                            {provider.id === 'google_drive' && 'Get your credentials from Google Cloud Console. Create OAuth 2.0 credentials and enable Google Drive API.'}
                                            {provider.id === 'dropbox' && 'Create an app in Dropbox App Console to get your access token and app credentials.'}
                                            {provider.id === 'aws_s3' && 'Get your AWS credentials from IAM console. Make sure the bucket exists and has proper permissions.'}
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </div>
    )
}
