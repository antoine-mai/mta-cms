import { useState, useEffect, useRef } from 'react'
import { BaseModal } from './base-modal'

interface ModalNewFileProps {
    isOpen: boolean
    onClose: () => void
    onConfirm: (name: string) => void
    isLoading?: boolean
}

export default function ModalNewFile({ isOpen, onClose, onConfirm, isLoading }: ModalNewFileProps) {
    const [name, setName] = useState('')
    const inputRef = useRef<HTMLInputElement>(null)

    useEffect(() => {
        if (isOpen) {
            setName('')
            setTimeout(() => inputRef.current?.focus(), 100)
        }
    }, [isOpen])

    const handleSubmit = () => {
        if (name.trim()) {
            onConfirm(name)
        }
    }

    return (
        <BaseModal
            isOpen={isOpen}
            onClose={onClose}
            onConfirm={handleSubmit}
            title="Create New File"
            confirmText="Create"
            isLoading={isLoading}
        >
            <div className="space-y-1.5">
                <label className="text-[11px] font-bold text-text-muted uppercase tracking-wider ml-1">File Name</label>
                <div className="relative">
                    <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-[18px]">article</span>
                    <input
                        ref={inputRef}
                        type="text"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
                        placeholder="example.txt"
                        className="w-full bg-background-light border border-border-light rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    />
                </div>
            </div>
        </BaseModal>
    )
}
