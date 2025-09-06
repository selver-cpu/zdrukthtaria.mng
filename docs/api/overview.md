# API Documentation

## Authentication

All API endpoints require authentication using Laravel Sanctum tokens.

### Headers

```
Accept: application/json
Authorization: Bearer {token}
```

## Rate Limiting

API requests are limited to 60 requests per minute per IP address.

## Endpoints

### Projects

- `GET /api/projects` - List all projects
- `POST /api/projects` - Create a new project
- `GET /api/projects/{id}` - Get project details
- `PUT /api/projects/{id}` - Update a project
- `DELETE /api/projects/{id}` - Delete a project

### Clients

- `GET /api/clients` - List all clients
- `POST /api/clients` - Create a new client
- `GET /api/clients/{id}` - Get client details

## Error Responses

| Status Code | Description |
|-------------|-------------|
| 401 | Unauthenticated |
| 403 | Unauthorized |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |

## Pagination

List endpoints support pagination using `page` and `per_page` query parameters.

Example: `GET /api/projects?page=2&per_page=15`

## Filtering

Most list endpoints support filtering. Example:
```
GET /api/projects?status=completed&client_id=1
```
