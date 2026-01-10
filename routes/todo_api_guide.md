# Todo API Guide

Base URL:
http://bw8wcw4gok440cco44c8kksk.72.62.198.249.sslip.io/api

## Authentication
Currently this API is **public** (no auth middleware attached). Anyone can call it.

## Data Model
Todo fields:
- id (integer)
- title (string)
- completed (boolean, default false)
- created_at
- updated_at

## Endpoints

### List all todos
GET /todos

```bash
curl {BASE_URL}/todos
```

### Create a todo
POST /todos

```bash
curl -X POST {BASE_URL}/todos \
 -H "Content-Type: application/json" \
 -d '{ "title": "Buy milk", "completed": false }'
```

### Get a single todo
GET /todos/{id}

```bash
curl {BASE_URL}/todos/1
```

### Update a todo
PUT /todos/{id}

```bash
curl -X PUT {BASE_URL}/todos/1 \
 -H "Content-Type: application/json" \
 -d '{ "completed": true }'
```

### Delete a todo
DELETE /todos/{id}

```bash
curl -X DELETE {BASE_URL}/todos/1
```

## Validation Rules
- title: required when creating, max 255 chars
- completed: boolean

## HTTP Status Codes
- 200 OK
- 201 Created
- 204 No Content
- 422 Validation Error

## Notes
Laravel route uses apiResource:
Route::apiResource('todos', TodoController::class);

This provides full CRUD automatically.
