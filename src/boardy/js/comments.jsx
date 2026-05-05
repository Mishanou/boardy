const { useState, useEffect } = React;

const API = '/api';
const POST_ID = 2;

function ItemList() {
    const [items, setItems] = useState([]);
    const [text, setText] = useState('');
    const [editId, setEditId] = useState(null);
    const [editText, setEditText] = useState('');
    const [jwt, setJwt] = useState(null);

    const load = async () => {
        const res = await fetch(`${API}/posts/${POST_ID}/comments`);
        const data = await res.json();
        setItems(data.items);
    };

    useEffect(() => {
        load();

        fetch('/api/me.php', { credentials: 'include' })
            .then(r => {
                if (!r.ok) return null;
                return r.json();
            })
            .then(data => {
                if (data && data.token)
                {
                    setJwt(data.token);
                    console.log(data.token);
                }
            })
            .catch(() => setJwt(null));
    }, []);

    const add = async () => {
        if (!text.trim()) return;
        if (!jwt) return;

        const headers = {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + jwt
        };

        await fetch(`${API}/posts/${POST_ID}/comments`, {
            method: 'POST',
            headers,
            body: JSON.stringify({ body: text })
        });

        setText('');
        load();
    };

    const save = async (id) => {
        if (!editText.trim()) return;
        if (!jwt) return;

        const headers = {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + jwt
        };

        await fetch(`${API}/comments/${id}`, {
            method: 'PUT',
            headers,
            body: JSON.stringify({ body: editText })
        });

        setEditId(null);
        load();
    };

    const del = async (id) => {
        if (!confirm('Удалить?')) return;
        if (!jwt) return;

        const headers = {
            'Authorization': 'Bearer ' + jwt
        };

        await fetch(`${API}/comments/${id}`, {
            method: 'DELETE',
            headers
        });

        load();
    };

    return (
        <div>
            {items.map(item => (
                <div key={item.id} className="card mb-2">
                    <div className="card-body">
                        <strong>{item.author_name}</strong>

                        {editId === item.id ? (
                            <div className="input-group mt-2">
                                <input
                                    className="form-control"
                                    value={editText}
                                    onChange={e => setEditText(e.target.value)}
                                />
                                <button className="btn btn-success" onClick={() => save(item.id)}>
                                    Сохранить
                                </button>
                                <button className="btn btn-secondary" onClick={() => setEditId(null)}>
                                    Отмена
                                </button>
                            </div>
                        ) : (
                            <div>
                                <p>{item.body}</p>
                                <button
                                    className="btn btn-sm btn-outline-secondary"
                                    onClick={() => {
                                        setEditId(item.id);
                                        setEditText(item.body);
                                    }}
                                >
                                    ✏️
                                </button>
                                <button
                                    className="btn btn-sm btn-outline-danger ms-1"
                                    onClick={() => del(item.id)}
                                >
                                    🗑️
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            ))}

            {jwt && (
                <div className="input-group mt-3">
                    <input
                        className="form-control"
                        placeholder="Комментарий"
                        value={text}
                        onChange={e => setText(e.target.value)}
                    />
                    <button className="btn btn-primary" onClick={add}>
                        Отправить
                    </button>
                </div>
            )}
        </div>
    );
}

ReactDOM.createRoot(document.getElementById('app')).render(<ItemList />);
