import { useRef } from 'react'
import { useFileExplorer } from '../context'

const SearchForm = () => {
    const { searchQuery, setSearchQuery } = useFileExplorer()
    return <div className="flex items-center bg-background-light rounded-lg px-3 h-8 border border-border-light w-72 transition-colors focus-within:ring-0 focus-within:border-primary">
        <span className="material-symbols-outlined text-text-secondary text-[20px]">search</span>
        <input
            className="bg-transparent border-none text-[13px] text-text-main focus:ring-0 w-full placeholder-text-secondary p-0 ml-2 font-normal focus:outline-none"
            placeholder="Search files..."
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
        />
    </div>
}

const UploadButton = () => {
    const fileInputRef = useRef<HTMLInputElement>(null)
    const { currentPath } = useFileExplorer()

    const handleUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0]
        if (!file) return

        const formData = new FormData()
        formData.append('file', file)
        formData.append('path', currentPath)

        try {
            const response = await fetch('/post/files/upload', {
                method: 'POST',
                body: formData
            })

            if (!response.ok) {
                throw new Error('Upload failed')
            }

            // Refresh file list
            window.dispatchEvent(new CustomEvent('files-changed'))

            // Reset input
            if (fileInputRef.current) {
                fileInputRef.current.value = ''
            }
        } catch (error) {
            console.error('Upload error:', error)
            alert('Failed to upload file')
        }
    }

    return <>
        <input
            ref={fileInputRef}
            type="file"
            className="hidden"
            onChange={handleUpload}
        />
        <button
            onClick={() => fileInputRef.current?.click()}
            className="flex items-center gap-2 h-8 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg transition-all shadow-md shadow-blue-500/20 shrink-0"
        >
            <span className="material-symbols-outlined text-[20px]">upload</span>
            Upload
        </button>
    </>
}

export {
    UploadButton,
    SearchForm
}