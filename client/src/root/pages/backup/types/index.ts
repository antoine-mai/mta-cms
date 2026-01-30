export interface Backup {
    id: string
    name: string
    size: string
    date: string
    time: string
    type: 'full' | 'incremental'
    status: 'completed' | 'failed' | 'in-progress'
}

export interface CloudProvider {
    id: string
    name: string
    enabled: boolean
    config: Record<string, string>
}
