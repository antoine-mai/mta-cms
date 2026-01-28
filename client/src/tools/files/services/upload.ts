export const uploadToFolder = async (targetPath: string, onSuccess?: () => void) => {
    const input = document.createElement('input')
    input.type = 'file'
    input.onchange = async (e) => {
        const file = (e.target as HTMLInputElement).files?.[0]
        if (!file) return

        const formData = new FormData()
        formData.append('file', file)
        formData.append('path', targetPath)

        try {
            const response = await fetch('/post/files/upload', {
                method: 'POST',
                body: formData
            })

            if (!response.ok) {
                throw new Error('Upload failed')
            }

            onSuccess?.()
        } catch (error) {
            console.error('Upload error:', error)
            alert('Failed to upload file')
        }
    }
    input.click()
}
