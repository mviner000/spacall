# Todo API Guide with Real-Time WebSocket Support

## 📡 Base URLs

**REST API:**
```
http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api
```

**WebSocket (Reverb):**
```
ws://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io:8081
```

---

## 🔐 Authentication
Currently this API is **public** (no auth middleware attached). Anyone can call it.

---

## 📊 Data Model
Todo fields:
- `id` (integer)
- `title` (string)
- `completed` (boolean, default false)
- `created_at` (timestamp)
- `updated_at` (timestamp)

---

## 🔌 REST API Endpoints

### List all todos
**GET** `/todos`
```bash
curl http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api/todos
```

### Create a todo
**POST** `/todos`
```bash
curl -X POST http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api/todos \
 -H "Content-Type: application/json" \
 -d '{ "title": "Buy milk", "completed": false }'
```

### Get a single todo
**GET** `/todos/{id}`
```bash
curl http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api/todos/1
```

### Update a todo
**PUT** `/todos/{id}`
```bash
curl -X PUT http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api/todos/1 \
 -H "Content-Type: application/json" \
 -d '{ "completed": true }'
```

### Delete a todo
**DELETE** `/todos/{id}`
```bash
curl -X DELETE http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api/todos/1
```

---

## ⚡ Real-Time WebSocket Events

The API broadcasts real-time events via Laravel Reverb (WebSocket server) on the `todos` channel:

### Events
- `todo.created`
- `todo.updated`
- `todo.deleted`

### Event Payload Example
```json
{
  "id": 1,
  "title": "Buy milk",
  "completed": false,
  "created_at": "2026-01-10T03:40:00.000000Z",
  "updated_at": "2026-01-10T03:40:00.000000Z"
}
```

---

## ✅ Validation Rules
- `title`: required, max 255 chars
- `completed`: boolean

## 📡 HTTP Status Codes
- `200` OK
- `201` Created
- `204` No Content
- `422` Validation Error


---

## 🔧 Node Easy: Using wscat (Node.js based)

### Install wscat
```bash
# Install Node.js first if you don't have it
sudo apt update
sudo apt install nodejs npm -y

# Install wscat globally
sudo npm install -g wscat
```

### Test Your WebSocket Connection
```bash
# Connect to Laravel Reverb
wscat -c ws://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io:8081/app/twnelshlx0eg32hojkqy
```

Once connected, subscribe to the `todos` channel by typing:
```json
{"event":"pusher:subscribe","data":{"channel":"todos"}}
```

You should start receiving real-time events such as:
- `todo.created`
- `todo.updated`
- `todo.deleted`
