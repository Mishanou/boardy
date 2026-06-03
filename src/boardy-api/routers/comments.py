from fastapi import APIRouter, HTTPException, Depends
from pydantic import BaseModel, Field
from database import get_db
from auth import get_current_user
from routers.ws import manager
import aiomysql

router = APIRouter(prefix='/api')


class CommentCreate(BaseModel):
    body: str = Field(..., min_length=1, max_length=2000)
    author_name: str = Field(..., min_length=1, max_length=255)

class CommentUpdate(BaseModel):
    body: str = Field(..., min_length=1, max_length=2000) 


@router.get('/posts/{post_id}/comments')
async def get_comments(post_id: int):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'SELECT * '
                'FROM comments '
                'WHERE post_id=%s '
                'ORDER BY created_at',
                (post_id)
            )
            items = await cur.fetchall()
    finally:
        conn.close()

    for item in items:
        item['created_at'] = str(item['created_at'])

    return items


@router.post('/posts/{post_id}/comments', status_code=201)
async def create_comment(post_id: int, data: CommentCreate, user = Depends(get_current_user)):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')

    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                '''
                INSERT INTO comments (post_id, author_id, author_name, body)
                VALUES (%s, %s, %s, %s)
                ''',
                (post_id, user['sub'], user['name'], data.body)
            )
            await conn.commit()

            comment_id = cur.lastrowid

            await cur.execute(
                'SELECT * FROM comments WHERE id = %s',
                (comment_id)
            )
            comment = await cur.fetchone()
    finally:
        conn.close()

    comment['created_at'] = str(comment['created_at'])

    await manager.broadcast({
        'type': 'new_comment',
        'comment': comment
    })

    return comment


@router.put('/comments/{comment_id}')
async def update_comment(comment_id: int, data: CommentUpdate, user = Depends(get_current_user)):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')

    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'SELECT * FROM comments WHERE id = %s',
                (comment_id)
            )

            comment = await cur.fetchone()

            if not comment:
                raise HTTPException(status_code=404, detail='Комментарий не найден')

            if comment['author_id'] != user['sub']:
                raise HTTPException(status_code=403, detail='Это не ваш комментарий')

            await cur.execute(
                'UPDATE comments SET body = %s WHERE id = %s',
                (data.body, comment_id)
            )

            await conn.commit()

            comment['body'] = data.body
    finally:
        conn.close()

    comment['created_at'] = str(comment['created_at'])

    await manager.broadcast({
        'type': 'update_comment',
        'comment': comment
    })

    return comment

@router.delete('/comments/{comment_id}')
async def delete_comment(comment_id: int, user = Depends(get_current_user)):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'SELECT * FROM comments WHERE id = %s',
                (comment_id)
            )

            comment = await cur.fetchone()

            if not comment:
                raise HTTPException(status_code=404, detail='Комментарий не найден')

            if comment['author_id'] != user['sub']:
                raise HTTPException(status_code=403, detail='Это не ваш комментарий')

            await cur.execute(
                'DELETE FROM commentsb WHERE id = %s',
                (comment_id)
            )

            await conn.commit()
    finally:
        conn.close()

    await manager.broadcast({
        'type': 'delete_comment',
        'comment_id': comment_id
    })

    return {'ok': True}
