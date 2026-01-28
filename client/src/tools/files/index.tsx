import { useEffect } from 'react'
import Header from '@/layouts/admin/components/header'
import Sidebar from './components/sidebar'
import { SearchForm } from './components/header'
import ModalRename from './components/modal-rename'
import ModalDelete from './components/modal-delete'
import ModalNewFolder from './components/modal-new-folder'
import ModalNewFile from './components/modal-new-file'
import { FileExplorerProvider, useFileExplorer } from './context'

// Sub-components
import { EditorView } from './components/editor'
import { BrowserView } from './components/browser'
import { DetailsView } from './components/details'

function FileExplorerContent({ hideDetails, initialPath }: { hideDetails?: boolean, initialPath?: string }) {
    const {
        actionModal,
        setActionModal,
        fileContent,
        setOpenMenuFile,
        handleActionConfirm,
    } = useFileExplorer()

    useEffect(() => {
        const handleClickOutside = () => setOpenMenuFile(null)
        document.addEventListener('click', handleClickOutside)
        return () => document.removeEventListener('click', handleClickOutside)
    }, [setOpenMenuFile])

    return (
        <div className="flex flex-1 h-full overflow-hidden">
            <Sidebar hideHeader={hideDetails} initialPath={initialPath} />
            <main className="flex-1 flex flex-col min-w-0 bg-background-main relative overflow-hidden">
                <Header>
                    <div className="flex items-center gap-4">
                        <SearchForm />
                    </div>
                </Header>

                <div className="flex-1 flex min-h-0">
                    <div className="flex-1 flex flex-col min-w-0 bg-background-main overflow-hidden">
                        {fileContent !== null ? <EditorView /> : <BrowserView />}
                    </div>
                    {!hideDetails && <DetailsView />}
                </div>

                <ModalRename
                    isOpen={actionModal?.type === 'rename'}
                    currentName={actionModal?.target?.name || ''}
                    onClose={() => setActionModal(null)}
                    onConfirm={(name) => handleActionConfirm({ action: 'rename', name })}
                />

                <ModalDelete
                    isOpen={actionModal?.type === 'delete'}
                    itemName={actionModal?.target?.name || ''}
                    onClose={() => setActionModal(null)}
                    onConfirm={() => handleActionConfirm({ action: 'delete', name: '' })}
                />

                <ModalNewFolder
                    isOpen={actionModal?.type === 'new-folder'}
                    onClose={() => setActionModal(null)}
                    onConfirm={(name) => handleActionConfirm({ action: 'new-folder', name })}
                />

                <ModalNewFile
                    isOpen={actionModal?.type === 'new-file'}
                    onClose={() => setActionModal(null)}
                    onConfirm={(name) => handleActionConfirm({ action: 'new-file', name })}
                />
            </main>
        </div>
    )
}

interface FilesProps {
    initialPath?: string
    hideDetails?: boolean
}

export default function Files({ initialPath, hideDetails }: FilesProps) {
    return (
        <FileExplorerProvider initialPath={initialPath}>
            <FileExplorerContent hideDetails={hideDetails} initialPath={initialPath} />
        </FileExplorerProvider>
    )
}
