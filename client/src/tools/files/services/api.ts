export const fileService = {
    async fetchFolder(path: string) {
        const res = await fetch('/post/tools/files', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path })
        })
        return await res.json()
    },

    async rename(oldPath: string, newPath: string) {
        const res = await fetch('/post/tools/files/rename', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ old_path: oldPath, new_path: newPath })
        })
        return await res.json()
    },

    async delete(path: string) {
        const res = await fetch('/post/tools/files/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path })
        })
        return await res.json()
    },

    async mkdir(parentPath: string, name: string) {
        const res = await fetch('/post/tools/files/mkdir', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ parent_path: parentPath, name })
        })
        return await res.json()
    },

    async create(parentPath: string, name: string) {
        const res = await fetch('/post/tools/files/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ parent_path: parentPath, name })
        })
        return await res.json()
    },

    async readFile(path: string) {
        const res = await fetch('/post/tools/files/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path })
        })
        return await res.json()
    },

    async saveFile(path: string, content: string) {
        const res = await fetch('/post/tools/files/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path, content })
        })
        return await res.json()
    },

    async search(path: string, query: string) {
        const res = await fetch('/post/tools/files/search', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path, query })
        })
        return await res.json()
    },

    async copy(sourcePath: string, destPath: string) {
        const res = await fetch('/post/tools/files/copy', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ source_path: sourcePath, dest_path: destPath })
        })
        return await res.json()
    },

    async compress(parentDir: string, items: string[], destName: string, format: string) {
        const res = await fetch('/post/tools/files/compress', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ parent_dir: parentDir, items, dest_name: destName, format })
        })
        return await res.json()
    },

    async extract(archivePath: string, destDir: string) {
        const res = await fetch('/post/tools/files/extract', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ archive_path: archivePath, dest_dir: destDir })
        })
        return await res.json()
    }
}
