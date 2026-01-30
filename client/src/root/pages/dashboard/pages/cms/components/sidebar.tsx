import { useState, useEffect } from 'react'
import { useLocation, useNavigate, Link } from 'react-router-dom'
import { Folder, ChevronRight, ChevronDown, FileText, Plus, Tags } from 'lucide-react'

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
}

interface ManagementItemProps {
    path: string
    icon: React.ComponentType<{ size?: number; className?: string }>
    title: string
}

function ManagementItem({ path, icon: Icon, title }: ManagementItemProps) {
    const location = useLocation()
    const isActive = location.pathname === path || location.pathname.startsWith(path + '/')

    return (
        <Link
            to={path}
            className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium ${isActive
                ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
        >
            <Icon size={18} className={isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'} />
            <span>{title}</span>
        </Link>
    )
}

export default function Sidebar() {
    const [categories, setCategories] = useState<Category[]>([])
    const [expandedIds, setExpandedIds] = useState<number[]>([])
    const location = useLocation()
    const navigate = useNavigate()

    const query = new URLSearchParams(location.search)
    const selectedId = query.get('category_id') ? parseInt(query.get('category_id')!) : null
    const currentPath = location.pathname

    useEffect(() => {
        fetchCategories()
    }, [])

    const fetchCategories = async () => {
        try {
            const res = await fetch('/root/post/categories/browse?type=post')
            const data = await res.json()
            if (data.success) {
                setCategories(data.items)
            }
        } catch (error) {
            console.error(error)
        }
    }

    const toggleExpand = (id: number, e: React.MouseEvent) => {
        e.stopPropagation()
        setExpandedIds(prev =>
            prev.includes(id) ? prev.filter(i => i !== id) : [...prev, id]
        )
    }

    const CategoryItem = ({ cat, level = 0 }: { cat: Category, level?: number }) => {
        const children = categories.filter(c => c.parent_id === cat.id)
        const hasChildren = children.length > 0
        const isExpanded = expandedIds.includes(cat.id)
        const isSelected = selectedId === cat.id && currentPath === '/dashboard/cms/categories'

        return (
            <div>
                <div
                    onClick={() => navigate(`/dashboard/cms/categories?category_id=${cat.id}`)}
                    className={`flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-colors group ${isSelected
                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                        : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300'
                        }`}
                    style={{ paddingLeft: `${12 + level * 16}px` }}
                >
                    {hasChildren && (
                        <button
                            onClick={(e) => toggleExpand(cat.id, e)}
                            className="p-0.5 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition-colors"
                        >
                            {isExpanded ? (
                                <ChevronDown size={14} className="text-slate-400" />
                            ) : (
                                <ChevronRight size={14} className="text-slate-400" />
                            )}
                        </button>
                    )}
                    {!hasChildren && <div className="w-[18px]" />}
                    <Folder size={16} className={isSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'} />
                    <span className="flex-1 text-sm font-medium truncate">{cat.name}</span>
                </div>
                {hasChildren && isExpanded && (
                    <div>
                        {children.map(child => (
                            <CategoryItem key={child.id} cat={child} level={level + 1} />
                        ))}
                    </div>
                )}
            </div>
        )
    }

    const rootCategories = categories.filter(c => !c.parent_id)

    return (
        <aside className="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col h-full">
            <div className="flex-1 overflow-y-auto custom-scrollbar">
                {/* Categories Section */}
                <div className="p-4 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center justify-between mb-3">
                        <h3 className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Categories</h3>
                        <button className="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors">
                            <Plus size={14} className="text-slate-400" />
                        </button>
                    </div>

                    {rootCategories.length > 0 ? (
                        <div className="space-y-0.5">
                            {rootCategories.map(cat => (
                                <CategoryItem key={cat.id} cat={cat} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-8">
                            <div className="size-12 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <Folder size={24} className="text-slate-300" />
                            </div>
                            <p className="text-xs text-slate-400 mb-3">No categories yet</p>
                            <button className="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-bold transition-all active:scale-95">
                                Create First Category
                            </button>
                        </div>
                    )}
                </div>

                {/* Content Section */}
                <div className="p-4">
                    <div className="px-3 mb-2">
                        <h3 className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Content</h3>
                    </div>
                    <div className="space-y-1">
                        <ManagementItem path="/dashboard/cms/posts" icon={FileText} title="Posts" />
                        <ManagementItem path="/dashboard/cms/tags" icon={Tags} title="Tags" />
                    </div>
                </div>
            </div>
        </aside>
    )
}
