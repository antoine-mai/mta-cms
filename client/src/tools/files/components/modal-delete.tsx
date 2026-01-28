import { BaseModal } from './base-modal'

interface ModalDeleteProps {
    isOpen: boolean
    onClose: () => void
    onConfirm: () => void
    itemName: string
    isLoading?: boolean
}

export default function ModalDelete({ isOpen, onClose, onConfirm, itemName, isLoading }: ModalDeleteProps) {
    return (
        <BaseModal
            isOpen={isOpen}
            onClose={onClose}
            onConfirm={onConfirm}
            title="Delete Item"
            confirmText="Delete"
            confirmColor="red"
            isLoading={isLoading}
        >
            <div className="flex flex-col items-center text-center py-2">
                <div className="size-16 rounded-full bg-red-500/10 flex items-center justify-center mb-4">
                    <span className="material-symbols-outlined text-red-600 text-[32px]">delete_forever</span>
                </div>
                <p className="text-sm text-text-main">
                    Are you sure you want to delete <span className="font-bold">{itemName}</span>?
                </p>
                <p className="text-[12px] text-text-muted mt-2">
                    This action cannot be undone and all data will be lost.
                </p>
            </div>
        </BaseModal>
    )
}
