import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    const config = {
        root: path.resolve(__dirname, 'public'),
        base: '/',
        plugins: [react()],
        publicDir: '../static',
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './src'),
            },
        },
        build: {
            outDir: path.resolve(__dirname, '../public'),
            emptyOutDir: false,
            rollupOptions: {
                input: {},
                output: {
                    manualChunks: (id: string) => {
                        if (id.includes('node_modules')) {
                            if (id.includes('react') || id.includes('react-dom') || id.includes('react-router-dom')) {
                                return 'react-vendor'
                            }
                            if (id.includes('@monaco-editor') || id.includes('monaco-editor')) {
                                return 'monaco-vendor'
                            }
                        }
                    },
                },
            },
        },
    }

    if (mode === 'development') {
        const target = env.VITE_API_TARGET || 'http://localhost:8100'
        return {
            ...config,
            server: {
                proxy: {
                    '/post': {
                        target: target,
                        changeOrigin: true,
                        secure: false,
                    },
                },
            },
            build: {
                outDir: path.resolve(__dirname, '../public/dist'),
                rollupOptions: {
                    input: {
                        main: path.resolve(__dirname, 'public/index.html'),
                        root: path.resolve(__dirname, 'public/root.html')
                    }
                }
            }
        }
    }

    const appType = process.env.APP_TYPE || 'main'

    if (appType === 'admin') {
        config.base = '/root/dist/'
        config.build.outDir = path.resolve(__dirname, '../public/root/dist')
        config.build.emptyOutDir = true
        config.build.rollupOptions.input = {
            root: path.resolve(__dirname, 'public/root.html')
        }
    } else {
        config.base = '/dist/'
        config.build.outDir = path.resolve(__dirname, '../public/dist')
        config.build.emptyOutDir = true
        config.build.rollupOptions.input = {
            main: path.resolve(__dirname, 'public/index.html')
        }
    }

    return config
})
