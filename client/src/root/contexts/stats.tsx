import { createContext, useContext, useState, useEffect, type ReactNode } from 'react'

interface SystemStats {
    cpu: { percent: number }
    network: { rx_kbs: number; tx_kbs: number }
    memory: { percent: number }
    storage: { percent: number }
}

interface StatsContextType {
    stats: SystemStats | null
    loading: boolean
}

const StatsContext = createContext<StatsContextType | undefined>(undefined)

export function StatsProvider({ children }: { children: ReactNode }) {
    const [stats, setStats] = useState<SystemStats | null>(null)
    const [loading, setLoading] = useState(true)

    const fetchStats = async () => {
        try {
            const res = await fetch('/root/post/system/stats')
            const data = await res.json()
            if (data.success === false) {
                setStats(null)
            } else {
                setStats(data)
            }
            setLoading(false)
        } catch (error) {
            console.error('Failed to fetch stats', error)
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchStats()
        const interval = setInterval(fetchStats, 2000)
        return () => clearInterval(interval)
    }, [])

    return (
        <StatsContext.Provider value={{ stats, loading }}>
            {children}
        </StatsContext.Provider>
    )
}

export function useStats() {
    const context = useContext(StatsContext)
    if (context === undefined) {
        throw new Error('useStats must be used within a StatsProvider')
    }
    return context
}
