export function isEditable(name: string): boolean {
    const lower = name.toLowerCase();

    // Always editable conditions
    if (name.startsWith('.')) return true; // Dotfiles (.env, .gitignore, etc.)
    if (!name.includes('.')) return true; // No extension (README, LICENSE, etc.)

    const ext = lower.split('.').pop();

    // Whitelist approach for files with extensions
    const editableExts = [
        'txt', 'md', 'js', 'jsx', 'ts', 'tsx', 'css', 'html', 'json', 'php',
        'env', 'sql', 'xml', 'yaml', 'yml', 'ini', 'conf', 'sh', 'bat', 'rb',
        'py', 'pl', 'go', 'java', 'c', 'cpp', 'h', 'hpp', 'cs', 'properties',
        'lock', 'log', 'csv', 'tsv', 'svg', 'vue', 'scss', 'less', 'sass'
    ];

    if (ext && editableExts.includes(ext)) return true;

    return false;
}
