from fastapi import FastAPI
from datetime import datetime
import aiomysql
from routers import comments
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI(title='Boardy API', version='0.2.0')

app.include_router(comments.router, prefix="/api")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get('/status')
async def status():
    return {'status': 'ok', 'time': str(datetime.now())}

@app.get('/messages')
async def get_messages():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT posts.body AS message, users.name, '
            'posts.created_at FROM posts '
            'JOIN users ON posts.author_id = users.id '
            'ORDER BY posts.created_at DESC'
        )
        messages = await cur.fetchall()
    conn.close()

    for m in messages:
        m['created_at'] = str(m['created_at'])

    return {'messages': messages, 'count': len(messages)}

@app.get('/users')
async def get_users():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT id, name, email, created_at FROM users'
        )
        users = await cur.fetchall()
    conn.close()

    for u in users:
        u['created_at'] = str(u['created_at'])

    return {'users': users, 'count': len(users)}
