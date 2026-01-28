export const fileUtils = {
    joinPath(base: string, name: string) {
        const separator = base.endsWith('/') ? '' : '/'
        return `${base}${separator}${name}`.replace('//', '/')
    },

    getParentPath(path: string) {
        return path.substring(0, path.lastIndexOf('/')) || ''
    },

    formatSize(bytes: number) {
        if (bytes === 0) return '0 B'
        if (bytes < 1024) return `${bytes} B`
        return `${(bytes / 1024).toFixed(1)} KB`
    },

    formatDate(date: string | Date) {
        return new Date(date).toLocaleString()
    },

    getLanguage(filename: string) {
        const ext = filename.split('.').pop()?.toLowerCase() || ''
        const map: Record<string, string> = {
            'js': 'javascript',
            'jsx': 'javascript',
            'ts': 'typescript',
            'tsx': 'typescript',
            'html': 'html',
            'css': 'css',
            'json': 'json',
            'php': 'php',
            'go': 'go',
            'py': 'python',
            'rb': 'ruby',
            'rs': 'rust',
            'sh': 'shell',
            'yml': 'yaml',
            'yaml': 'yaml',
            'md': 'markdown',
            'sql': 'sql',
            'xml': 'xml',
            'bash': 'shell'
        }
        return map[ext] || 'plaintext'
    }
}
