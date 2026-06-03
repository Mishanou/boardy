import { startLogin, handleCallback, refreshToken } from './auth.js'

function Comments({ postId, userName }) {
    const [token, setToken] = React.useState(null)
    const [comments, setComments] = React.useState([])

    React.useEffect(() => {
        handleCallback().then(t => { if (t) setToken(t) })
    }, [])

    async function authedFetch(url, options = {}) {
        let response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Authorization': 'Bearer ' + token,
            }
        })

        if (response.status === 401) {
            const newToken = await refreshToken()
            if (!newToken) return null
            setToken(newToken)

            return fetch(url, {
                ...options,
                headers: {
                    ...options.headers,
                    'Authorization': 'Bearer ' + newToken,
                }
            })
        }
        return response
    }

    async function addComment(body) {
        const res = await authedFetch(
            `http://boardy.localhost/api/posts/${postId}/comments`,
            {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ body, author_name: userName })
            })
            
        return res.json()
    }
    // ... render ...
}