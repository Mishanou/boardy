from fastapi import APIRouter, HTTPException, Depends
from pydantic import BaseModel
from database import get_db
from auth import get_current_user
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

    for item in items:
        item['created_at'] = str(item['created_at'])

    return {'items': items, 'count': len(items)}


@router.post('/posts/{post_id}/comments', status_code=201)
async def create_comment(post_id: int, data: ChildCreate, user = Depends(get_current_user)):
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
                (data.body, post_id, user['user_id'])
            )
            await conn.commit()
            new_id = cur.lastrowid
    finally:
        conn.close()
    
    return {'id': new_id, 'body': data.body, 'status': 'created'}


@router.put('/comments/{comment_id}')
async def update_comment(comment_id: int, data: ChildUpdate, user = Depends(get_current_user)):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')
    
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(
                'UPDATE comments SET body = %s WHERE id = %s AND author_id = %s',
                (data.body, comment_id, user['user_id'])
            )
            if cur.rowcount == 0:
                raise HTTPException(status_code=404, detail='Запись не найдена')
            await conn.commit()
    finally:
        conn.close()
    
    return {'id': comment_id, 'body': data.body, 'status': 'updated'}


@router.delete('/comments/{comment_id}', status_code=204)
async def delete_comment(comment_id: int, user = Depends(get_current_user)):
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(
                'DELETE FROM comments WHERE id = %s AND author_id = %s',
                (comment_id, user['user_id'])
            )
            if cur.rowcount == 0:
                raise HTTPException(status_code=404, detail='Запись не найдена')
            await conn.commit()
    finally:
        conn.close()
