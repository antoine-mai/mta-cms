import { motion, AnimatePresence } from 'framer-motion';
import { X } from 'lucide-react';

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    children: React.ReactNode;
}

export function Modal({ isOpen, onClose, title, children }: ModalProps) {
    return (
        <AnimatePresence>
            {isOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95, y: 10 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.95, y: 10 }}
                        className="bg-white dark:bg-slate-800 w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden rounded-lg"
                    >
                        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                            <h3 className="text-lg font-semibold text-slate-900 dark:text-white">{title}</h3>
                            <button
                                onClick={onClose}
                                className="p-1 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors rounded-full"
                            >
                                <X size={20} />
                            </button>
                        </div>
                        <div className="p-6">
                            {children}
                        </div>
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
}

interface PromptModalProps extends Omit<ModalProps, 'children'> {
    value: string;
    setValue: (val: string) => void;
    onConfirm: () => void;
    placeholder?: string;
    confirmLabel?: string;
    confirmColor?: string;
}

export function PromptModal({
    isOpen,
    onClose,
    title,
    value,
    setValue,
    onConfirm,
    placeholder = "Enter value...",
    confirmLabel = "Confirm",
    confirmColor = "bg-indigo-600 hover:bg-indigo-700"
}: PromptModalProps) {
    return (
        <Modal isOpen={isOpen} onClose={onClose} title={title}>
            <div className="space-y-4">
                <input
                    type="text"
                    value={value}
                    onChange={(e) => setValue(e.target.value)}
                    placeholder={placeholder}
                    className="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all font-mono text-sm rounded"
                    autoFocus
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') onConfirm();
                        if (e.key === 'Escape') onClose();
                    }}
                />
                <div className="flex justify-end gap-3">
                    <button
                        onClick={onClose}
                        className="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        onClick={onConfirm}
                        className={`px-4 py-2 text-sm font-semibold text-white rounded shadow-sm transition-all active:scale-95 ${confirmColor}`}
                    >
                        {confirmLabel}
                    </button>
                </div>
            </div>
        </Modal>
    );
}

interface AlertModalProps extends Omit<ModalProps, 'children'> {
    message: string;
    type?: 'info' | 'error' | 'success';
}

export function AlertModal({ isOpen, onClose, title, message, type = 'info' }: AlertModalProps) {
    const colors = {
        info: 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20',
        error: 'text-red-600 bg-red-50 dark:bg-red-900/20',
        success: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20'
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={title}>
            <div className="space-y-4">
                <p className={`p-4 text-sm font-medium rounded ${colors[type]}`}>
                    {message}
                </p>
                <div className="flex justify-end">
                    <button
                        onClick={onClose}
                        className="px-6 py-2 bg-slate-900 dark:bg-slate-700 text-white text-sm font-semibold hover:bg-slate-800 dark:hover:bg-slate-600 transition-all active:scale-95 rounded"
                    >
                        OK
                    </button>
                </div>
            </div>
        </Modal>
    );
}
