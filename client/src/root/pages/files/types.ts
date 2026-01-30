export interface FileNode {
    name: string;
    path: string;
    type: 'file' | 'dir';
    size?: string;
    modified?: string;
    children?: FileNode[];
}
