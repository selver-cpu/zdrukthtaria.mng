# API Authentication

All API endpoints require authentication using Laravel Sanctum tokens. This document explains how to authenticate and make authenticated requests to the API.

## Authentication Flow

1. **Login** - Obtain an API token using your email and password
2. **Include Token** - Send the token with each subsequent request
3. **Logout** - Invalidate the token when done

## Obtaining an API Token

### Request
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "your_password",
    "device_name": "user_iphone"
}
```

### Successful Response (200 OK)
```json
{
    "token": "1|abcdef123456...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "menaxher"
    }
}
```

### Error Responses
- **401 Unauthorized** - Invalid credentials
- **422 Unprocessable Entity** - Validation errors

## Making Authenticated Requests

Include the token in the `Authorization` header of your requests:

```http
GET /api/projects
Authorization: Bearer 1|abcdef123456...
Accept: application/json
```

## Logging Out

To invalidate the current token:

```http
POST /api/logout
Authorization: Bearer 1|abcdef123456...
```

## Token Expiration

- Tokens expire after 1 year of inactivity
- You can check token expiration in the `expires_at` column of the `personal_access_tokens` table

## Rate Limiting

- 60 requests per minute per IP address
- 1000 requests per hour per authenticated user

## Error Responses

### Standard Error Format
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": ["The field name is required."]
    }
}
```

### Common Status Codes
- **200 OK** - Request successful
- **201 Created** - Resource created successfully
- **204 No Content** - Resource deleted successfully
- **400 Bad Request** - Invalid request syntax
- **401 Unauthorized** - Authentication required
- **403 Forbidden** - Insufficient permissions
- **404 Not Found** - Resource not found
- **422 Unprocessable Entity** - Validation error
- **429 Too Many Requests** - Rate limit exceeded
- **500 Internal Server Error** - Server error

## Testing API Endpoints

You can test the API endpoints using tools like:

1. **Postman**
2. **cURL**
3. **Insomnia**
4. **Swagger UI** (available at `/api/documentation`)

Example cURL request:

```bash
# Get all projects
curl -X GET \
  http://your-domain.com/api/projects \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer your_api_token_here'
```

## Security Best Practices

1. **Never expose your API token**
   - Don't commit tokens to version control
   - Use environment variables for token storage
   
2. **Use HTTPS**
   - Always make API requests over HTTPS
   - Enable HSTS for additional security
   
3. **Token Management**
   - Use different tokens for different devices
   - Revoke unused tokens
   - Rotate tokens periodically

4. **Input Validation**
   - Always validate input on both client and server
   - Use prepared statements to prevent SQL injection

5. **Error Handling**
   - Don't expose sensitive information in error messages
   - Log errors for debugging purposes

## Next Steps

- [View API Endpoints](/docs/api/endpoints.md)
- [API Rate Limiting](/docs/api/rate-limiting.md)
- [Error Handling](/docs/api/error-handling.md)
