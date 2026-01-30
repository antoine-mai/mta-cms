import { useState, useEffect } from 'react'
import MonacoEditor from '@monaco-editor/react'
import { X, Save, Loader2 } from 'lucide-react'
import type { FileNode } from '../types'
import { AlertModal } from '../../../components/modal'

interface EditorProps {
    file: FileNode;
    onClose: () => void;
}

export function Editor({ file, onClose }: EditorProps) {
    const [content, setContent] = useState('')
    const [loading, setLoading] = useState(true)
    const [saving, setSaving] = useState(false)
    const [alert, setAlert] = useState<{ isOpen: boolean; title: string; message: string; type: 'info' | 'error' | 'success'; onAfterClose?: () => void }>({
        isOpen: false,
        title: '',
        message: '',
        type: 'info'
    })

    const showAlert = (title: string, message: string, type: 'info' | 'error' | 'success' = 'info', onAfterClose?: () => void) => {
        setAlert({ isOpen: true, title, message, type, onAfterClose })
    }

    useEffect(() => {
        const loadContent = async () => {
            try {
                const res = await fetch(`/root/post/files/read?path=${encodeURIComponent(file.path)}`)
                const data = await res.json()
                if (res.ok) {
                    setContent(data.content || '')
                } else {
                    showAlert('Error', data.error || 'Failed to load file content', 'error', onClose)
                }
            } catch (error) {
                console.error(error)
                showAlert('Error', 'Failed to load file', 'error', onClose)
            } finally {
                setLoading(false)
            }
        }
        loadContent()
    }, [file.path, onClose])

    const handleSave = async () => {
        setSaving(true)
        try {
            const res = await fetch('/root/post/files/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    path: file.path,
                    content
                })
            })
            const data = await res.json()
            if (res.ok) {
                // simple feedback - could use a toast eventually but let's stick to modals for now if needed or silent success
            } else {
                showAlert('Error', data.error || 'Failed to save', 'error')
            }
        } catch (error) {
            console.error(error)
            showAlert('Error', 'Failed to save', 'error')
        } finally {
            setSaving(false)
        }
    }

    // Determine language from extension
    const getLanguage = (filename: string) => {
        const ext = filename.split('.').pop()?.toLowerCase()
        if (ext === 'js' || ext === 'jsx') return 'javascript'
        if (ext === 'ts' || ext === 'tsx') return 'typescript'
        if (ext === 'css') return 'css'
        if (ext === 'html') return 'html'
        if (ext === 'json') return 'json'
        if (ext === 'php') return 'php'
        if (ext === 'md') return 'markdown'
        return 'plaintext'
    }

    // Detect dark mode (simple check for 'dark' class on html/body or system preference)
    const [theme, setTheme] = useState('vs-dark')

    useEffect(() => {
        const isDark = document.documentElement.classList.contains('dark')
        setTheme(isDark ? 'vs-dark' : 'light') // use 'light' instead of 'vs' for better contrast in light mode usually

        // Optional: Listen for class changes if theme toggling doesn't unmount this component
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark')
            setTheme(isDark ? 'vs-dark' : 'light')
        })
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
        return () => observer.disconnect()
    }, [])

    return (
        <div className="flex flex-col h-full bg-white dark:bg-slate-900 overflow-hidden">
            {/* Header */}
            <div className="h-12 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 bg-slate-50 dark:bg-slate-800/50 flex-shrink-0">
                <h3 className="font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>Editing:</span>
                    <span className="font-mono text-sm opacity-75">{file.name}</span>
                </h3>
                <div className="flex items-center gap-2">
                    <button
                        onClick={handleSave}
                        disabled={saving || loading}
                        className="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 text-sm font-medium flex items-center gap-1.5 disabled:opacity-50 transition-colors"
                    >
                        {saving ? <Loader2 size={16} className="animate-spin" /> : <Save size={16} />}
                        Save
                    </button>
                    <button
                        onClick={onClose}
                        className="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-3 py-1.5 text-sm font-medium flex items-center gap-1.5 transition-colors"
                    >
                        <X size={16} />
                        Close
                    </button>
                </div>
            </div>

            {/* Editor Content */}
            <div className="flex-1 overflow-hidden relative">
                {loading ? (
                    <div className="absolute inset-0 flex items-center justify-center bg-white dark:bg-slate-900 z-10">
                        <Loader2 size={32} className="animate-spin text-indigo-500" />
                    </div>
                ) : (
                    <MonacoEditor
                        height="100%"
                        defaultLanguage={getLanguage(file.name)}
                        value={content}
                        onChange={(val) => setContent(val || '')}
                        theme={theme}
                        options={{
                            minimap: { enabled: true },
                            fontSize: 14,
                            wordWrap: 'on',
                            padding: { top: 16, bottom: 16 }
                        }}
                    />
                )}
                {/* Modals */}
                <AlertModal
                    isOpen={alert.isOpen}
                    onClose={() => {
                        setAlert(prev => ({ ...prev, isOpen: false }));
                        if (alert.onAfterClose) alert.onAfterClose();
                    }}
                    title={alert.title}
                    message={alert.message}
                    type={alert.type}
                />
            </div>
        </div>
    )
}
