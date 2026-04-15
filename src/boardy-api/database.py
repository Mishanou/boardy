import aiomysql

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',        # ← ваш пользователь БД
    'password': '0000',      # ← ваш пароль
    'db': 'boardy',          # ← ваша база данных
    'charset': 'utf8mb4',    # ← полный Unicode, включая эмодзи
}


async def get_db():
    """Возвращает соединение с базой данных"""
    return await aiomysql.connect(**DB_CONFIG)
