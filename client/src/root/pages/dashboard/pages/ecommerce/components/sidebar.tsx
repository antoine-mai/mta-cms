import { useState, useEffect } from 'react'
import { useLocation, useNavigate, Link } from 'react-router-dom'
import { Folder, ChevronRight, ChevronDown, Package, Plus, List, Tags, User, BarChart3, Layers, ShoppingCart } from 'lucide-react'

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
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
            const res = await fetch('/root/post/categories/browse?type=product')
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
        const isSelected = selectedId === cat.id && currentPath === '/dashboard/ecommerce'

        return (
            <div className="select-none">
                <div
                    onClick={() => navigate(`/dashboard/ecommerce?category_id=${cat.id}`)}
                    className={`
                        flex items-center gap-2 px-3 py-1.5 cursor-pointer transition-all rounded-lg group text-sm
                        ${isSelected
                            ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'}
                    `}
                    style={{ paddingLeft: `${(level * 12) + 8}px` }}
                >
                    <span
                        onClick={(e) => toggleExpand(cat.id, e)}
                        className={`p-0.5 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition-colors ${!hasChildren ? 'invisible' : ''}`}
                    >
                        {isExpanded ? <ChevronDown size={14} /> : <ChevronRight size={14} />}
                    </span>
                    <Folder size={14} className={isSelected ? 'text-indigo-500' : 'text-slate-400'} />
                    <span className="truncate flex-1">{cat.name}</span>
                </div>

                {isExpanded && hasChildren && (
                    <div className="mt-0.5">
                        {children.map(child => (
                            <CategoryItem key={child.id} cat={child} level={level + 1} />
                        ))}
                    </div>
                )}
            </div>
        )
    }

    const ManagementItem = ({ path, icon: Icon, title }: { path: string, icon: any, title: string }) => {
        const isActive = currentPath === path
        return (
            <Link
                to={path}
                className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium ${isActive
                    ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400'
                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                    }`}
            >
                <Icon size={18} />
                <span>{title}</span>
            </Link>
        )
    }

    return (
        <aside className="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col h-full overflow-hidden">
            {/* Catalog Section */}
            <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 className="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Catalog</h3>
                <button
                    onClick={() => {/* Open Add Category Modal */ }}
                    className="p-1 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-md transition-colors"
                    title="Add Category"
                >
                    <Plus size={16} />
                </button>
            </div>

            <div className="flex-1 overflow-y-auto p-2 custom-scrollbar">
                <div
                    onClick={() => navigate('/dashboard/ecommerce')}
                    className={`
                        flex items-center gap-3 px-3 py-2 cursor-pointer transition-all rounded-lg mb-2 text-sm
                        ${(selectedId === null && currentPath === '/dashboard/ecommerce')
                            ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'}
                    `}
                >
                    <Package size={16} />
                    <span>All Products</span>
                </div>

                <div className="space-y-0.5 mb-6">
                    {categories.length === 0 ? (
                        <div className="px-3 py-4 text-center border border-dashed border-slate-200 dark:border-slate-800 rounded-xl mx-2">
                            <p className="text-[11px] text-slate-500 dark:text-slate-400 mb-2">No categories found</p>
                            <button className="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 mx-auto">
                                <Plus size={10} />
                                Create First Category
                            </button>
                        </div>
                    ) : (
                        categories.filter(c => !c.parent_id).map(rootCat => (
                            <CategoryItem key={rootCat.id} cat={rootCat} />
                        ))
                    )}
                </div>

                <div className="px-3 mb-2">
                    <h3 className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Management</h3>
                </div>
                <div className="space-y-1 mb-6">
                    <ManagementItem path="/dashboard/ecommerce/attribute-sets" icon={Layers} title="Attribute Sets" />
                    <ManagementItem path="/dashboard/ecommerce/attributes" icon={List} title="Attributes" />
                    <ManagementItem path="/dashboard/ecommerce/tags" icon={Tags} title="Product Tags" />
                </div>

                <div className="px-3 mb-2">
                    <h3 className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Sales</h3>
                </div>
                <div className="space-y-1">
                    <ManagementItem path="/dashboard/ecommerce/orders" icon={ShoppingCart} title="Orders" />
                    <ManagementItem path="/dashboard/ecommerce/customers" icon={User} title="Customers" />
                </div>
            </div>

            <div className="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                <ManagementItem path="/dashboard/ecommerce/analytics" icon={BarChart3} title="Analytics" />
            </div>
        </aside>
    )
}
