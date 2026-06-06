function Comments({ postId, userName }) {
    const [comments, setComments] = React.useState([])
    const [newComment, setNewComment] = React.useState('')
    const [loading, setLoading] = React.useState(true)

    // Прямой URL к твоему контейнеру FastAPI на порту 8000
    const FASTAPI_URL = `http://localhost:8000/api/posts/${postId}/comments`

    // Загрузка комментариев
    React.useEffect(() => {
        fetch(FASTAPI_URL)
            .then(res => res.json())
            .then(data => {
                setComments(Array.isArray(data) ? data : [])
                setLoading(false)
            })
            .catch(err => {
                console.error("Ошибка загрузки комментариев:", err)
                setLoading(false)
            })
    }, [postId])

    async function handleSubmit(e) {
        e.preventDefault()
        
        // ИСПРАВЛЕНО: используем правильный trim() для JavaScript
        if (!newComment || !newComment.trim()) return

        try {
            const res = await fetch(FASTAPI_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    body: newComment.trim(), 
                    author_name: userName || 'Авторизованный пользователь' 
                })
            })
            
            if (res.ok) {
                const createdComment = await res.json()
                setComments([...comments, createdComment])
                setNewComment('')
            } else {
                const errorData = await res.text()
                alert(`FastAPI вернул статус ${res.status}: ${errorData}`)
            }
        } catch (error) {
            alert(`Не удалось достучаться до FastAPI на порту 8000. Проверь docker compose ps!`)
        }
    }

    return (
        <div style={{ marginTop: '24px', fontFamily: 'sans-serif' }}>
            <h3 style={{ borderBottom: '2px solid #eee', paddingBottom: '8px' }}>
                Комментарии (React + FastAPI)
            </h3>
            
            {loading ? (
                <p>Загрузка...</p>
            ) : comments.length === 0 ? (
                <p style={{ color: '#666', fontStyle: 'italic' }}>Комментариев пока нет.</p>
            ) : (
                <div style={{ marginBottom: '20px' }}>
                    {comments.map(c => (
                        <div key={c.id} style={{ marginBottom: '12px', padding: '12px', backgroundColor: '#f9f9f9', border: '1px solid #e3e3e3', borderRadius: '6px' }}>
                            <div style={{ fontSize: '13px', color: '#555', marginBottom: '4px' }}>
                                <strong>{c.author_name}</strong>
                            </div>
                            <div style={{ fontSize: '15px', color: '#111' }}>{c.body}</div>
                        </div>
                    ))}
                </div>
            )}

            <form onSubmit={handleSubmit} style={{ marginTop: '16px', backgroundColor: '#fff', padding: '15px', border: '1px solid #ddd', borderRadius: '6px' }}>
                <label htmlFor="react-body" style={{ display: 'block', marginBottom: '6px', fontSize: '14px' }}>
                    Оставить комментарий:
                </label>
                <textarea
                    id="react-body"
                    rows="3"
                    style={{ width: '100%', padding: '10px', boxSizing: 'border-box', borderRadius: '4px', border: '1px solid #ccc' }}
                    placeholder="Напишите текст комментария..."
                    value={newComment}
                    onChange={e => setNewComment(e.target.value)}
                    required
                />
                <button type="submit" style={{ marginTop: '10px', backgroundColor: '#28a745', color: 'white', border: 'none', padding: '8px 16px', borderRadius: '4px', cursor: 'pointer', fontWeight: 'bold' }}>
                    Отправить
                </button>
            </form>
        </div>
    )
}