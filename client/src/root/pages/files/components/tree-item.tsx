import React, { useState, useEffect } from 'react'
import { ChevronDown, ChevronRight, Folder, FileText } from 'lucide-react'
import type { FileNode } from '../types'
import { OptionsMenu } from './options-menu'
import { useFiles } from '../context'

export function TreeItem({ node, onSelect, activePath, onEdit }: { node: FileNode, onSelect: (n: FileNode) => void, activePath: string, onEdit?: (n: FileNode) => void }) {
    const [children, setChildren] = useState<FileNode[]>([])
    const [loaded, setLoaded] = useState(false)
    const [expanded, setExpanded] = useState(false)
    const { refreshVersion } = useFiles()

    const fetchChildren = async () => {
        try {
            const res = await fetch(`/root/post/files/browse?path=${encodeURIComponent(node.path)}`)
            const data = await res.json()
            setChildren(data.items) // Load all items (files + dirs)
            setLoaded(true)
        } catch (err) {
            console.error(err)
        }
    }

    useEffect(() => {
        if (expanded) {
            fetchChildren()
        }
    }, [refreshVersion, node.path])

    const handleToggle = async (e: React.MouseEvent) => {
        e.stopPropagation()

        // Only dirs can expand
        if (node.type !== 'dir') return

        const newExpanded = !expanded
        setExpanded(newExpanded)
        if (newExpanded && !loaded) {
            fetchChildren()
        }
    }

    return (
        <TreeItemContent
            node={node}
            expanded={expanded}
            onSelect={onSelect}
            activePath={activePath}
            onToggle={handleToggle}
            onEdit={onEdit}
        >
            {children.map(child => (
                <TreeItem
                    key={child.path}
                    node={child}
                    onSelect={onSelect}
                    activePath={activePath}
                    onEdit={onEdit}
                />
            ))}
        </TreeItemContent>
    )
}

function TreeItemContent({ node, expanded, onSelect, activePath, children, onToggle, isRoot, onEdit }: any) {
    const isActive = activePath === node.path

    return (
        <div className="select-none">
            <div
                className={`
                    group flex items-center gap-1.5 px-2 py-1.5 cursor-pointer transition-colors text-sm
                    ${isActive
                        ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 font-medium'
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                    }
                `}
                onClick={() => onSelect(node)}
            >
                {/* Arrow for expanding */}
                <span
                    className={`
                        opacity-50 hover:opacity-100 p-0.5 hover:bg-slate-200 dark:hover:bg-slate-700 
                        ${(!onToggle && !isRoot) || node.type !== 'dir' ? 'invisible' : ''}
                    `}
                    onClick={onToggle}
                >
                    {expanded ? <ChevronDown size={14} /> : <ChevronRight size={14} />}
                </span>

                {node.type === 'dir' ? (
                    <Folder size={14} className={isActive ? 'text-indigo-500' : 'text-slate-400'} />
                ) : (
                    <FileText size={14} className={isActive ? 'text-indigo-500' : 'text-slate-400'} />
                )}

                <span className="truncate flex-1">{node.name}</span>

                <div onClick={(e) => e.stopPropagation()} className="opacity-0 group-hover:opacity-100 transition-opacity">
                    <OptionsMenu file={node} onEdit={onEdit} />
                </div>
            </div>

            {expanded && (
                <div className="pl-4 ml-1 border-l border-slate-200 dark:border-slate-800">
                    {children}
                </div>
            )}
        </div>
    )
}
