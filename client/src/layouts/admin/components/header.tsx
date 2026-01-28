import { type ReactNode } from 'react'

interface HeaderProps {
    children?: ReactNode
}

export default function Header({ children }: HeaderProps) {

    return <header className="h-12 flex items-center justify-between px-6 bg-background-main border-b border-border-light shrink-0 z-30 sticky top-0">
        <div className="flex items-center gap-2">
            {/* Left side can have title or breadcrumbs if needed */}
        </div>
        <div className="flex items-center gap-4">
            {children}
            {/* Right side placeholder or minimal info */}
        </div>
    </header>
}