from fastapi import APIRouter, HTTPException
from pydantic import BaseModel
from database import get_db
import aiomysql

router = APIRouter()


class ChildCreate(BaseModel):
    body: str


class ChildUpdate(BaseModel):
    body: str


@router.get('/posts/{post_id}/comments')
async def get_comments(post_id: int):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'SELECT c.id, c.body, c.created_at, '
                'u.name AS author_name '  # ← имя из связанной таблицы
                'FROM comments c '
                'JOIN users u ON c.author_id = u.id '
                'WHERE c.post_id = %s '  # ← фильтр по родителю
                'ORDER BY c.created_at',
                (post_id,)
            )
            items = await cur.fetchall()
    finally:
        conn.close()

    # datetime → строка (JSON не умеет datetime)
    for item in items:
        item['created_at'] = str(item['created_at'])

    return {'items': items, 'count': len(items)}


@router.post('/posts/{post_id}/comments', status_code=201)
async def create_child(post_id: int, data: ChildCreate):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')
    
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute('SELECT id FROM posts WHERE id = %s', (post_id,))
            if not await cur.fetchone():
                raise HTTPException(status_code=404, detail='Пост не найден')
            await cur.execute(
                'INSERT INTO comments (body, post_id, author_id) VALUES (%s, %s, %s)',
                (data.body, post_id, 2)
            )
            await conn.commit()
            new_id = cur.lastrowid
    finally:
        conn.close()
    
    return {'id': new_id, 'body': data.body, 'status': 'created'}


@router.put('/comments/{comment_id}')
async def update_child(comment_id: int, data: ChildUpdate):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')
    
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(
                'UPDATE comments SET body = %s WHERE id = %s',
                (data.body, comment_id)
            )
            if cur.rowcount == 0:
                raise HTTPException(status_code=404, detail='Запись не найдена')
            await conn.commit()
    finally:
        conn.close()
    
    return {'id': comment_id, 'body': data.body, 'status': 'updated'}


@router.delete('/comments/{comment_id}', status_code=204)
async def delete_child(comment_id: int):
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute('DELETE FROM comments WHERE id = %s', (comment_id,))
            if cur.rowcount == 0:
                raise HTTPException(status_code=404, detail='Запись не найдена')
            await conn.commit()
    finally:
        conn.close()
