import asyncio
import json
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from fastapi.middleware.cors import CORSMiddleware
import redis.asyncio as aioredis

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


class ConnectionManager:
    def __init__(self):
        self.active_connections: list[WebSocket] = []

    async def connect(self, websocket: WebSocket):
        await websocket.accept()
        self.active_connections.append(websocket)

    def disconnect(self, websocket: WebSocket):
        self.active_connections.remove(websocket)

    async def broadcast(self, message: str):
        for connection in self.active_connections:
            try:
                await connection.send_text(message)
            except Exception:
                pass


manager = ConnectionManager()


@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket):
    await manager.connect(websocket)
    try:
        while True:
            await websocket.receive_text()
    except WebSocketDisconnect:
        manager.disconnect(websocket)


async def redis_listener():
    while True:
        try:
            r = aioredis.from_url("redis://redis:6379", decode_responses=True)
            pubsub = r.pubsub()
            
            await pubsub.psubscribe("*new_post", "*new_comment")
            
            while True:
                message = await pubsub.get_message(ignore_subscribe_messages=True, timeout=1.0)
                if message:
                    channel = message.get("channel", "")
                    data = json.loads(message["data"])

                    if "new_post" in channel:
                        payload = {"type": "new_post", "post": data}
                    elif "new_comment" in channel:
                        payload = {"type": "new_comment", "comment": data}
                    else:
                        continue
                    
                    await manager.broadcast(json.dumps(payload))
                
                await asyncio.sleep(0.05)
                
        except Exception as e:
            await asyncio.sleep(5)

@app.on_event("startup")
async def startup_event():
    asyncio.create_task(redis_listener())