import { createPortal } from 'react-dom'

export interface BaseModalProps {
    isOpen: boolean
    onClose: () => void
    title: string
    children: React.ReactNode
    onConfirm: () => void
    confirmText?: string
    confirmColor?: "primary" | "red"
    isLoading?: boolean
}

export const BaseModal = ({
    isOpen,
    onClose,
    title,
    children,
    onConfirm,
    confirmText = "Confirm",
    confirmColor = "primary",
    isLoading = false
}: BaseModalProps) => {
    if (!isOpen) return null

    return createPortal(
        <div
            className="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in duration-200 cursor-pointer"
            onClick={onClose}
        >
            <div
                className="bg-card-bg border border-border-light w-full max-w-[400px] rounded-2xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200 cursor-default"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="flex items-center justify-between px-6 py-4 border-b border-border-light bg-background-light/30">
                    <h3 className="font-bold text-text-main">{title}</h3>
                    <button onClick={onClose} className="text-text-muted hover:text-text-main transition-colors">
                        <span className="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <div className="p-6">
                    {children}
                </div>

                <div className="flex items-center justify-end gap-3 px-6 py-4 border-t border-border-light bg-background-light/30">
                    <button
                        onClick={onClose}
                        className="px-4 py-2 text-sm font-bold text-text-secondary hover:bg-background-light rounded-xl transition-all"
                    >
                        Cancel
                    </button>
                    <button
                        onClick={onConfirm}
                        disabled={isLoading}
                        className={`px-5 py-2 text-sm font-bold text-white rounded-xl shadow-lg transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 ${confirmColor === 'red'
                                ? 'bg-red-600 hover:bg-red-700 shadow-red-600/20'
                                : 'bg-primary hover:bg-primary-hover shadow-primary/20'
                            }`}
                    >
                        {isLoading && <span className="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>}
                        {confirmText}
                    </button>
                </div>
            </div>
        </div>,
        document.body
    )
}
