import { Cloud, X, RefreshCw, CheckCircle2, Save, Key } from 'lucide-react'
import type { CloudProvider } from '../types'

interface CloudModalProps {
    isOpen: boolean
    onClose: () => void
    providers: CloudProvider[]
    selectedProviderId: string
    onSelectProvider: (id: string) => void
    onUpdateProvider: (id: string, field: string, value: string | boolean) => void
    onSave: () => void
    saving: boolean
    saveStatus: 'idle' | 'success' | 'error'
    frequency: string
    hour: string
    onUpdateSchedule: (field: 'frequency' | 'hour', value: string) => void
}

export function CloudModal({
    isOpen,
    onClose,
    providers,
    selectedProviderId,
    onSelectProvider,
    onUpdateProvider,
    onSave,
    saving,
    saveStatus,
    frequency,
    hour,
    onUpdateSchedule
}: CloudModalProps) {
    if (!isOpen) return null

    const selectedProvider = providers.find(p => p.id === selectedProviderId)

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 duration-200">
                {/* Modal Header */}
                <div className="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
                    <div className="flex items-center gap-3">
                        <div className="p-2 bg-indigo-500 rounded-xl">
                            <Cloud className="w-5 h-5 text-white" />
                        </div>
                        <h3 className="font-bold text-slate-900 dark:text-white">Cloud Configuration</h3>
                    </div>
                    <button onClick={onClose} className="p-2 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-full transition-colors">
                        <X className="w-5 h-5 text-slate-400" />
                    </button>
                </div>

                {/* Modal Body */}
                <div className="p-6 space-y-6">
                    {/* Schedule Section */}
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Frequency</label>
                            <select
                                value={frequency}
                                onChange={(e) => onUpdateSchedule('frequency', e.target.value)}
                                className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                            >
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Backup Time</label>
                            <select
                                value={hour}
                                onChange={(e) => onUpdateSchedule('hour', e.target.value)}
                                className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                            >
                                {Array.from({ length: 24 }).map((_, i) => (
                                    <option key={i} value={i.toString().padStart(2, '0')}>
                                        {i.toString().padStart(2, '0')}:00
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Provider Selector */}
                    <div>
                        <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Select Provider</label>
                        <select
                            value={selectedProviderId}
                            onChange={(e) => onSelectProvider(e.target.value)}
                            className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                        >
                            {providers.map(p => (
                                <option key={p.id} value={p.id}>{p.name}</option>
                            ))}
                        </select>
                    </div>

                    {selectedProvider && (
                        <div className="space-y-4 animate-in slide-in-from-top-2 duration-300">
                            <div className="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                                <div className="flex items-center gap-3">
                                    <div className="size-2 rounded-full bg-indigo-500 animate-pulse"></div>
                                    <span className="text-sm font-bold text-slate-700 dark:text-slate-300">Enable Backup to {selectedProvider.name}</span>
                                </div>
                                <label className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={selectedProvider.enabled}
                                        onChange={(e) => onUpdateProvider(selectedProvider.id, 'enabled', e.target.checked)}
                                        className="sr-only peer"
                                    />
                                    <div className="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            {selectedProvider.enabled && (
                                <div className="space-y-3">
                                    {Object.entries(selectedProvider.config).map(([key, value]) => (
                                        <div key={key}>
                                            <label className="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">
                                                {key.split('_').join(' ')}
                                            </label>
                                            <div className="relative group">
                                                <Key className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
                                                <input
                                                    type={key.includes('secret') || key.includes('token') || key.includes('key') ? 'password' : 'text'}
                                                    value={value}
                                                    onChange={(e) => onUpdateProvider(selectedProvider.id, key, e.target.value)}
                                                    placeholder={`Enter ${key.split('_').join(' ')}`}
                                                    className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}
                </div>

                {/* Modal Footer */}
                <div className="p-6 pt-0 flex gap-3">
                    <button
                        onClick={onClose}
                        className="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-2xl font-bold transition-all active:scale-95"
                    >
                        Cancel
                    </button>
                    <button
                        onClick={onSave}
                        disabled={saving}
                        className="flex-[2] px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        {saving ? (
                            <RefreshCw className="w-4 h-4 animate-spin" />
                        ) : saveStatus === 'success' ? (
                            <CheckCircle2 className="w-4 h-4" />
                        ) : (
                            <Save className="w-4 h-4" />
                        )}
                        {saving ? 'Saving...' : saveStatus === 'success' ? 'Saved!' : 'Save Changes'}
                    </button>
                </div>
            </div>
        </div>
    )
}
