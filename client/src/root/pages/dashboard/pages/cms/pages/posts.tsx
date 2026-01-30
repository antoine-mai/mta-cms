import { Plus, Search, MoreVertical, Calendar, User, Eye, X, Tag, Folder } from 'lucide-react'
import { useState, useEffect } from 'react'

interface Post {
    id: number
    title: string
    excerpt: string
    author: string
    category: string
    status: string
    views: number
    date: string
    content?: string
    tags?: string[]
    featuredImage?: string
}

export default function CmsPostsPage() {
    const [selectedPost, setSelectedPost] = useState<Post | null>(null)

    // Close on ESC key
    useEffect(() => {
        const handleEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                setSelectedPost(null)
            }
        }

        window.addEventListener('keydown', handleEscape)
        return () => window.removeEventListener('keydown', handleEscape)
    }, [])

    const posts: Post[] = [
        {
            id: 1,
            title: 'Getting Started with React',
            excerpt: 'Learn the basics of React and build your first component...',
            author: 'John Doe',
            category: 'Technology',
            status: 'published',
            views: 1234,
            date: '2024-01-15',
            content: 'React is a JavaScript library for building user interfaces. It lets you compose complex UIs from small and isolated pieces of code called components.',
            tags: ['React', 'JavaScript', 'Tutorial']
        },
        {
            id: 2,
            title: '10 Tips for Better Productivity',
            excerpt: 'Boost your daily productivity with these proven strategies...',
            author: 'Jane Smith',
            category: 'Lifestyle',
            status: 'published',
            views: 892,
            date: '2024-01-14',
            content: 'Productivity is about working smarter, not harder. Here are 10 actionable tips to help you get more done.',
            tags: ['Productivity', 'Lifestyle', 'Tips']
        },
        {
            id: 3,
            title: 'The Future of AI in Business',
            excerpt: 'Exploring how artificial intelligence is transforming...',
            author: 'Mike Johnson',
            category: 'Business',
            status: 'draft',
            views: 0,
            date: '2024-01-13',
            content: 'Artificial Intelligence is revolutionizing the way businesses operate, from automation to predictive analytics.',
            tags: ['AI', 'Business', 'Technology']
        },
    ]

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
                                placeholder="Search posts..."
                                className="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                            />
                        </div>
                    </div>

                    <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                        <Plus size={18} />
                        New Post
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-6 bg-slate-50/30 dark:bg-slate-900/20 custom-scrollbar">
                    <div className="space-y-3">
                        {posts.map(post => (
                            <div
                                key={post.id}
                                onClick={() => setSelectedPost(post)}
                                className={`bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:shadow-lg transition-all duration-300 group cursor-pointer ${selectedPost?.id === post.id ? 'ring-2 ring-indigo-500' : ''
                                    }`}
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <div className="flex-1">
                                        <div className="flex items-center gap-3 mb-2">
                                            <h3 className="font-bold text-lg text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {post.title}
                                            </h3>
                                            <span className={`text-xs font-bold px-2 py-1 rounded-md ${post.status === 'published'
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
                                                }`}>
                                                {post.status}
                                            </span>
                                        </div>
                                        <p className="text-sm text-slate-600 dark:text-slate-300 mb-4">{post.excerpt}</p>
                                        <div className="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                            <div className="flex items-center gap-1">
                                                <User size={14} />
                                                <span>{post.author}</span>
                                            </div>
                                            <div className="flex items-center gap-1">
                                                <Calendar size={14} />
                                                <span>{post.date}</span>
                                            </div>
                                            <div className="flex items-center gap-1">
                                                <Eye size={14} />
                                                <span>{post.views} views</span>
                                            </div>
                                            <span className="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 rounded-md">
                                                {post.category}
                                            </span>
                                        </div>
                                    </div>
                                    <button className="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                        <MoreVertical size={16} className="text-slate-400" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Right Sidebar - Details Panel */}
            <aside className={`w-80 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 m-6 ml-0 rounded-xl shadow-sm overflow-hidden ${selectedPost ? 'opacity-100' : 'opacity-50 pointer-events-none'
                }`}>
                {selectedPost ? (
                    <>
                        <div className="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 bg-slate-50 dark:bg-slate-900/50">
                            <h3 className="font-bold text-slate-900 dark:text-white">Post Details</h3>
                            <button
                                onClick={() => setSelectedPost(null)}
                                className="p-1.5 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors"
                            >
                                <X size={18} className="text-slate-400" />
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                            {/* Title */}
                            <div>
                                <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Title</label>
                                <h2 className="text-lg font-bold text-slate-900 dark:text-white">{selectedPost.title}</h2>
                            </div>

                            {/* Status */}
                            <div>
                                <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Status</label>
                                <span className={`inline-flex text-xs font-bold px-3 py-1.5 rounded-lg ${selectedPost.status === 'published'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
                                    }`}>
                                    {selectedPost.status}
                                </span>
                            </div>

                            {/* Author */}
                            <div>
                                <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Author</label>
                                <div className="flex items-center gap-2">
                                    <div className="size-8 bg-indigo-100 dark:bg-indigo-500/10 rounded-full flex items-center justify-center">
                                        <User size={16} className="text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <span className="text-sm font-medium text-slate-900 dark:text-white">{selectedPost.author}</span>
                                </div>
                            </div>

                            {/* Category */}
                            <div>
                                <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Category</label>
                                <div className="flex items-center gap-2">
                                    <Folder size={16} className="text-slate-400" />
                                    <span className="text-sm text-slate-900 dark:text-white">{selectedPost.category}</span>
                                </div>
                            </div>

                            {/* Tags */}
                            {selectedPost.tags && selectedPost.tags.length > 0 && (
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Tags</label>
                                    <div className="flex flex-wrap gap-2">
                                        {selectedPost.tags.map((tag, idx) => (
                                            <span key={idx} className="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-md text-xs">
                                                <Tag size={12} />
                                                {tag}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Metadata */}
                            <div className="pt-4 border-t border-slate-200 dark:border-slate-700 space-y-3">
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-slate-500 dark:text-slate-400">Published</span>
                                    <span className="font-medium text-slate-900 dark:text-white">{selectedPost.date}</span>
                                </div>
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-slate-500 dark:text-slate-400">Views</span>
                                    <span className="font-medium text-slate-900 dark:text-white">{selectedPost.views.toLocaleString()}</span>
                                </div>
                            </div>

                            {/* Content Preview */}
                            {selectedPost.content && (
                                <div>
                                    <label className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">Content Preview</label>
                                    <p className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{selectedPost.content}</p>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="pt-4 space-y-2">
                                <button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                                    Edit Post
                                </button>
                                <button className="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                                    View Live
                                </button>
                            </div>
                        </div>
                    </>
                ) : (
                    <div className="flex-1 flex items-center justify-center p-6">
                        <p className="text-sm text-slate-400 text-center">Select a post to view details</p>
                    </div>
                )}
            </aside>
        </div>
    )
}
