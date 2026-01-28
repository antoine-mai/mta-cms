import { useState, useEffect, useRef } from 'react'
import { BaseModal } from './base-modal'

interface ModalRenameProps {
    isOpen: boolean
    onClose: () => void
    onConfirm: (newName: string) => void
    currentName: string
    isLoading?: boolean
}

export default function ModalRename({ isOpen, onClose, onConfirm, currentName, isLoading }: ModalRenameProps) {
    const [name, setName] = useState(currentName)
    const inputRef = useRef<HTMLInputElement>(null)

    useEffect(() => {
        if (isOpen) {
            setName(currentName)
            setTimeout(() => inputRef.current?.focus(), 100)
        }
    }, [isOpen, currentName])

    const handleSubmit = () => {
        if (name.trim() && name !== currentName) {
            onConfirm(name)
        }
    }

    return (
        <BaseModal
            isOpen={isOpen}
            onClose={onClose}
            onConfirm={handleSubmit}
            title="Rename Item"
            confirmText="Rename"
            isLoading={isLoading}
        >
            <div className="space-y-1.5">
                <label className="text-[11px] font-bold text-text-muted uppercase tracking-wider ml-1">New Name</label>
                <input
                    ref={inputRef}
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
                    className="w-full bg-background-light border border-border-light rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
            </div>
        </BaseModal>
    )
}
