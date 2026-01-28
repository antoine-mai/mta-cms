import { useRef } from 'react'
import Editor from '@monaco-editor/react'
import { useFileExplorer } from '../context'
import { useTheme } from '@/contexts/theme'
import { fileUtils } from '../services/utils'

export const EditorView = () => {
    const {
        selectedFile,
        fileContent,
        setFileContent,
        isFileLoading,
        isImage,
        handleSaveFile
    } = useFileExplorer()

    const editorRef = useRef<any>(null)
    const { theme } = useTheme()
    const actualTheme = theme === 'system'
        ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
        : theme
    const monacoTheme = actualTheme === 'dark' ? 'vs-dark' : 'vs'

    const handleSave = () => {
        if (editorRef.current) {
            const content = editorRef.current.getValue()
            handleSaveFile(content)
        }
    }

    return (
        <div className="flex-1 overflow-y-auto bg-background-main flex flex-col">
            <div className="flex-1 flex flex-col p-6 lg:p-8 w-full max-w-7xl mx-auto">
                <div className="flex items-center justify-between mb-4 border-b border-border-light pb-2">
                    <div className="flex items-center gap-3">
                        <span className={`material-symbols-outlined text-[20px] pt-1 ${isImage ? 'text-purple-500' : 'text-blue-500'}`}>
                            {isImage ? 'image' : (selectedFile?.icon || 'description')}
                        </span>
                        <span className="font-bold text-base text-text-main pt-0.5">{selectedFile?.name}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => setFileContent(null)}
                            disabled={isFileLoading}
                            className="h-8 px-3 text-[11px] font-bold text-text-secondary hover:bg-background-light rounded transition-all border border-border-light flex items-center gap-1.5 whitespace-nowrap disabled:opacity-50"
                        >
                            <span className="material-symbols-outlined text-[16px]">close</span>
                            CLOSE
                        </button>
                        {!isImage && (
                            <button
                                onClick={handleSave}
                                disabled={isFileLoading}
                                className="h-8 px-3 bg-primary text-white rounded text-[11px] font-bold shadow-sm hover:bg-primary-hover transition-all flex items-center gap-1.5 border border-primary whitespace-nowrap disabled:opacity-50"
                            >
                                <span className="material-symbols-outlined text-[16px]">
                                    {isFileLoading ? 'progress_activity' : 'save'}
                                </span>
                                {isFileLoading ? 'SAVING...' : 'SAVE'}
                            </button>
                        )}
                    </div>
                </div>
                <div className="flex-1 overflow-hidden border border-border-light rounded bg-card-bg flex items-center justify-center">
                    {isFileLoading && isImage ? (
                        <span className="material-symbols-outlined animate-spin text-primary text-4xl">progress_activity</span>
                    ) : (
                        isImage ? (
                            <img src={fileContent!} alt="Preview" className="max-w-full max-h-full object-contain" />
                        ) : (
                            <Editor
                                height="100%"
                                language={fileUtils.getLanguage(selectedFile?.name || '')}
                                theme={monacoTheme}
                                path={selectedFile?.name}
                                value={fileContent || ''}
                                onMount={(editor) => {
                                    editorRef.current = editor
                                }}
                                options={{
                                    minimap: { enabled: false },
                                    fontSize: 14,
                                    readOnly: false,
                                    automaticLayout: true,
                                    padding: { top: 16 },
                                    scrollbar: {
                                        vertical: 'visible',
                                        horizontal: 'visible',
                                        useShadows: false,
                                        verticalSliderSize: 10,
                                        horizontalSliderSize: 10
                                    }
                                }}
                            />
                        )
                    )}
                </div>
            </div>
        </div>
    )
}
