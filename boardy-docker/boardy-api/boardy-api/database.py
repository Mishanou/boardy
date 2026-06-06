import aiomysql

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',
    'password': '0000',
    'db': 'boardy_api',
    'charset': 'utf8mb4',
}


async def get_db():
    return await aiomysql.connect(**DB_CONFIG)


async def db_execute(query, *params):
    conn = await get_db()

    try:
        async with conn.cursor() as cur:
            await cur.execute(query, params)
            await conn.commit()

    finally:
        conn.close()