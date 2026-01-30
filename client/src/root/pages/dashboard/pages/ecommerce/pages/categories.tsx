import { Plus, Search, MoreVertical, X, Package, Tag as TagIcon } from 'lucide-react'
import { useState, useEffect } from 'react'

interface Product {
    id: number
    name: string
    price: number
    stock_quantity: number
    featured_image: string | null
    sku?: string
    category?: string
    status?: string
    description?: string
    tags?: string[]
}

export default function EcommerceCategoriesPage() {
    const [categories] = useState<any[]>([])
    const [products] = useState<Product[]>([])
    const [loadingProducts] = useState(false)
    const [searchTerm, setSearchTerm] = useState('')
    const [selectedProduct, setSelectedProduct] = useState<Product | null>(null)

    // Close on ESC key
    useEffect(() => {
        const handleEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                setSelectedProduct(null)
            }
        }

        window.addEventListener('keydown', handleEscape)
        return () => window.removeEventListener('keydown', handleEscape)
    }, [])

    const filteredProducts = products.filter(p =>
        p.name.toLowerCase().includes(searchTerm.toLowerCase())
    )

    return (
        <div className="flex h-full">
            {/* Main Content */}
            <div className="flex-1 flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-xl overflow-hidden m-6 mr-0">
                <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-white dark:bg-slate-900">
                    <div className="flex items-center gap-4 flex-1 max-w-lg">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                            <input
                                type="text"
                                placeholder="Search products..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-all cursor-not-allowed"
                                disabled={categories.length === 0}
                            />
                        </div>
                    </div>

                    <button
                        disabled={categories.length === 0}
                        className={`px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg transition-all active:scale-95 ${categories.length === 0
                            ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed shadow-none'
                            : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-500/20'
                            }`}
                        title={categories.length === 0 ? "You must create a category first" : "Add Product"}
                    >
                        <Plus size={18} />
                        Add Product
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">
                    {loadingProducts ? (
                        <div className="flex h-full items-center justify-center">
                            <div className="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
                        </div>
                    ) : categories.length === 0 ? (
                        <div className="flex flex-col items-center justify-center h-full text-slate-400 py-20 text-center">
                            <div className="size-20 bg-slate-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mb-6 text-slate-300">
                                <Plus size={40} />
                            </div>
                            <h3 className="text-xl font-bold text-slate-900 dark:text-white mb-2">No Categories Yet</h3>
                            <p className="max-w-xs text-sm text-slate-500 dark:text-slate-400 mb-8">
                                You need at least one category to start adding products to your store.
                            </p>
                            <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                                Create Your First Category
                            </button>
                        </div>
                    ) : filteredProducts.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            {filteredProducts.map(product => (
                                <div
                                    key={product.id}
                                    onClick={() => setSelectedProduct(product)}
                                    className={`bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden group hover:shadow-xl transition-all duration-300 flex flex-col shadow-sm cursor-pointer ${selectedProduct?.id === product.id ? 'ring-2 ring-indigo-500' : ''
                                        }`}
                                >
                                    <div className="aspect-[4/3] bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
                                        {product.featured_image ? (
                                            <img src={product.featured_image} alt={product.name} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center text-slate-300">
                                                <Package size={48} />
                                            </div>
                                        )}
                                        <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button className="p-2 bg-white/90 dark:bg-slate-800/90 rounded-full shadow-lg text-slate-600 dark:text-slate-300 hover:text-indigo-600">
                                                <MoreVertical size={16} />
                                            </button>
                                        </div>
                                    </div>
                                    <div className="p-4 flex-1 flex flex-col">
                                        <h3 className="font-bold text-slate-800 dark:text-white mb-1 group-hover:text-indigo-600 transition-colors truncate">
                                            {product.name}
                                        </h3>
                                        <div className="flex items-center justify-between mt-auto">
                                            <span className="text-lg font-black text-indigo-600 dark:text-indigo-400">
                                                ${product.price}
                                            </span>
                                            <span className={`text-[11px] font-bold px-2 py-1 rounded-md ${product.stock_quantity > 0 ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400'}`}>
                                                {product.stock_quantity > 0 ? `${product.stock_quantity} in stock` : 'Out of stock'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="flex flex-col items-center justify-center h-full text-slate-400 py-20">
                            <Package size={64} className="mb-4 opacity-10" />
                            <p className="text-lg font-medium">No products found</p>
                            <p className="text-sm">Try selecting a different category or change your search.</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Right Sidebar - Product Details */}
            <aside className={`w-80 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 m-6 ml-0 rounded-xl shadow-sm overflow-hidden ${selectedProduct ? 'opacity-100' : 'opacity-50 pointer-events-none'
                }`}>
                {selectedProduct ? (
                    <>
                        <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-slate-50 dark:bg-slate-900/50">
                            <h3 className="font-bold text-slate-900 dark:text-white">Product Details</h3>
                            <button
                                onClick={() => setSelectedProduct(null)}
                                className="p-1.5 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors"
                            >
                                <X size={18} className="text-slate-400" />
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                            {/* Product Image */}
                            {selectedProduct.featured_image ? (
                                <div className="aspect-square bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden">
                                    <img src={selectedProduct.featured_image} alt={selectedProduct.name} className="w-full h-full object-cover" />
                                </div>
                            ) : (
                                <div className="aspect-square bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">
                                    <Package size={64} className="text-slate-300" />
                                </div>
                            )}

                            {/* Product Name */}
                            <div>
                                <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Product Name</label>
                                <h2 className="text-lg font-bold text-slate-900 dark:text-white">{selectedProduct.name}</h2>
                            </div>

                            {/* SKU */}
                            {selectedProduct.sku && (
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">SKU</label>
                                    <span className="text-sm font-mono text-slate-900 dark:text-white">{selectedProduct.sku}</span>
                                </div>
                            )}

                            {/* Price & Stock */}
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Price</label>
                                    <span className="text-2xl font-black text-indigo-600 dark:text-indigo-400">${selectedProduct.price}</span>
                                </div>
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Stock</label>
                                    <span className={`inline-flex text-sm font-bold px-3 py-1.5 rounded-lg ${selectedProduct.stock_quantity > 0
                                        ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                                        : 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400'
                                        }`}>
                                        {selectedProduct.stock_quantity}
                                    </span>
                                </div>
                            </div>

                            {/* Category */}
                            {selectedProduct.category && (
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Category</label>
                                    <span className="text-sm text-slate-900 dark:text-white">{selectedProduct.category}</span>
                                </div>
                            )}

                            {/* Tags */}
                            {selectedProduct.tags && selectedProduct.tags.length > 0 && (
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Tags</label>
                                    <div className="flex flex-wrap gap-2">
                                        {selectedProduct.tags.map((tag, idx) => (
                                            <span key={idx} className="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-md text-xs">
                                                <TagIcon size={12} />
                                                {tag}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Description */}
                            {selectedProduct.description && (
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Description</label>
                                    <p className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{selectedProduct.description}</p>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="pt-4 space-y-2">
                                <button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                                    Edit Product
                                </button>
                                <button className="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                                    View in Store
                                </button>
                            </div>
                        </div>
                    </>
                ) : (
                    <div className="flex-1 flex items-center justify-center p-6">
                        <p className="text-sm text-slate-400 text-center">Select a product to view details</p>
                    </div>
                )}
            </aside>
        </div>
    )
}
