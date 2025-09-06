# API Endpoints Documentation

This document provides detailed information about all available API endpoints in the Carpentry Project Management System.

## Base URL
All API endpoints are relative to the base URL: `https://your-domain.com/api`

## Authentication
All endpoints require authentication unless otherwise noted. See [Authentication Documentation](/docs/api/authentication.md) for details.

## Rate Limiting
- 60 requests per minute per IP address
- 1000 requests per hour per authenticated user

---

## Projects

### List All Projects
```http
GET /projects
```

#### Query Parameters
- `status` (string) - Filter by status (e.g., 'active', 'completed')
- `client_id` (integer) - Filter by client ID
- `sort` (string) - Sort field (e.g., 'created_at', 'deadline')
- `order` (string) - Sort order ('asc' or 'desc')
- `per_page` (integer) - Items per page (default: 15)
- `page` (integer) - Page number (default: 1)

#### Response (200 OK)
```json
{
    "data": [
        {
            "id": 1,
            "name": "Project Name",
            "description": "Project description",
            "status": "in_progress",
            "client_id": 5,
            "start_date": "2023-01-15",
            "deadline": "2023-06-30",
            "created_at": "2023-01-10T12:00:00Z",
            "updated_at": "2023-01-10T12:00:00Z"
        }
    ],
    "links": {
        "first": "https://api.example.com/projects?page=1",
        "last": "https://api.example.com/projects?page=5",
        "prev": null,
        "next": "https://api.example.com/projects?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "path": "https://api.example.com/projects",
        "per_page": 15,
        "to": 15,
        "total": 75
    }
}
```

### Get Single Project
```http
GET /projects/{id}
```

#### Response (200 OK)
```json
{
    "data": {
        "id": 1,
        "name": "Project Name",
        "description": "Project description",
        "status": "in_progress",
        "client": {
            "id": 5,
            "name": "Client Name",
            "email": "client@example.com"
        },
        "materials": [
            {
                "id": 1,
                "name": "Wood Plank",
                "quantity": 10,
                "unit": "pcs"
            }
        ],
        "tasks": [
            {
                "id": 1,
                "name": "Initial Design",
                "status": "completed",
                "due_date": "2023-02-01"
            }
        ],
        "created_at": "2023-01-10T12:00:00Z",
        "updated_at": "2023-01-10T12:00:00Z"
    }
}
```

### Create Project
```http
POST /projects
Content-Type: application/json

{
    "name": "New Project",
    "description": "Project description",
    "client_id": 5,
    "start_date": "2023-03-01",
    "deadline": "2023-06-30",
    "status": "planning"
}
```

#### Response (201 Created)
```json
{
    "data": {
        "id": 2,
        "name": "New Project",
        "description": "Project description",
        "status": "planning",
        "client_id": 5,
        "start_date": "2023-03-01",
        "deadline": "2023-06-30",
        "created_at": "2023-02-15T10:30:00Z",
        "updated_at": "2023-02-15T10:30:00Z"
    },
    "message": "Project created successfully"
}
```

### Update Project
```http
PUT /projects/{id}
Content-Type: application/json

{
    "name": "Updated Project Name",
    "status": "in_progress"
}
```

#### Response (200 OK)
```json
{
    "data": {
        "id": 1,
        "name": "Updated Project Name",
        "status": "in_progress",
        "updated_at": "2023-02-16T09:15:00Z"
    },
    "message": "Project updated successfully"
}
```

### Delete Project
```http
DELETE /projects/{id}
```

#### Response (204 No Content)
```
// No content in response
```

---

## Tasks

### List Project Tasks
```http
GET /projects/{projectId}/tasks
```

### Create Task
```http
POST /projects/{projectId}/tasks
Content-Type: application/json

{
    "name": "Task Name",
    "description": "Task description",
    "assigned_to": 3,
    "due_date": "2023-03-15",
    "status": "todo"
}
```

---

## Materials

### List Materials
```http
GET /materials
```

### Create Material
```http
POST /materials
Content-Type: application/json

{
    "name": "Plywood",
    "description": "High-quality plywood",
    "unit": "sqm",
    "price_per_unit": 25.50,
    "in_stock": 100
}
```

### Update Material Stock
```http
PATCH /materials/{id}/stock
Content-Type: application/json

{
    "quantity": 5,
    "action": "add" // or "remove"
}
```

---

## Reports

### Generate Project Report
```http
GET /reports/projects
```

### Export Project Report
```http
POST /reports/export/projects
Content-Type: application/json

{
    "format": "pdf", // or "excel", "csv"
    "filters": {
        "date_from": "2023-01-01",
        "date_to": "2023-12-31",
        "status": ["completed", "in_progress"]
    }
}
```

---

## File Uploads

### Upload Project Document
```http
POST /projects/{id}/documents
Content-Type: multipart/form-data

{
    "file": "[binary data]",
    "name": "Project Plan",
    "description": "Initial project plan document"
}
```

### List Project Documents
```http
GET /projects/{id}/documents
```

### Download Document
```http
GET /documents/{id}/download
```

---

## Error Handling

### Error Response Example (400 Bad Request)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Error Response Example (404 Not Found)
```json
{
    "message": "The requested resource was not found.",
    "status": 404
}
```

### Error Response Example (429 Too Many Requests)
```json
{
    "message": "Too Many Attempts. Please try again in 60 seconds.",
    "status": 429
}
```

---

## Webhooks

### Available Webhooks
- `project.created`
- `project.updated`
- `project.completed`
- `task.assigned`
- `task.completed`

### Webhook Payload Example
```json
{
    "event": "project.updated",
    "data": {
        "id": 123,
        "name": "Project Name",
        "status": "in_progress",
        "updated_at": "2023-02-16T09:15:00Z"
    },
    "timestamp": 1676538900
}
```

### Webhook Security
- Webhook endpoints must be registered in the admin panel
- Each webhook has a secret key for signature verification
- Payloads are signed with HMAC-SHA256

---

## Changelog

### v1.0.0 (2023-02-15)
- Initial API release
- Project management endpoints
- Task management
- Material inventory
- Reporting system
