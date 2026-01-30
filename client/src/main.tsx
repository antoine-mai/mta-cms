import { createRoot } from 'react-dom/client'
import { StrictMode } from 'react'

function Main() {
    return <StrictMode>
        <div>Home</div>
    </StrictMode>
}

const root = document.getElementById('root')!
createRoot(root).render(<Main />)
