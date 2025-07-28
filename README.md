# Laravel Articles API

A secure, cleanly-structured Laravel REST API system for article management with authentication and rate limiting.

## Features

- ✅ Authenticated user login and token-based API access
- ✅ Article management (create, update, delete, soft delete)
- ✅ Category management
- ✅ Public listing of published articles with filters
- ✅ API request rate limiting (per minute and per day)
- ✅ Secure token-based authentication using Laravel Sanctum

## Requirements

- PHP 8.1 or higher
- Composer
- SQLite (default) or MySQL/PostgreSQL
- Laravel 10.x

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Laravel_Articles
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   # Create .env file manually (copy from .env.example if it exists)
   # For SQLite (default), you can use the basic Laravel .env structure
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | User login, returns token |
| GET | `/api/auth/me` | Get authenticated user info |
| POST | `/api/auth/logout` | Logout and invalidate token |

### Article Management (Authenticated Users Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/articles/mine` | List authenticated user's articles |
| POST | `/api/articles` | Create a new article |
| GET | `/api/articles/{id}` | View specific article (if owner) |
| PUT | `/api/articles/{id}` | Update article (if owner) |
| DELETE | `/api/articles/{id}` | Soft delete article (if owner) |

### Public Article Listing (No Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/articles` | List all published articles |
| GET | `/api/articles/public/{id}` | View a single published article |

### Category Management (Authenticated Users Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all categories |
| POST | `/api/categories` | Create new category |
| PUT | `/api/categories/{id}` | Update existing category |
| DELETE | `/api/categories/{id}` | Delete category |

## Authentication

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer <your-token>
```

## Rate Limiting

The API implements two levels of rate limiting:

1. **Per-minute limit**: 60 requests per minute per user/IP
2. **Daily limit**: 1000 requests per day per user/IP

When limits are exceeded:
```json
{
  "error": "API rate limit exceeded. Try again later."
}
```

## Example Usage

### 1. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```

### 2. Create Article (with token)
```bash
curl -X POST http://localhost:8000/api/articles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Article",
    "body": "This is the content of my article.",
    "status": "published",
    "category_id": 1
  }'
```

### 3. Get Public Articles
```bash
curl -X GET "http://localhost:8000/api/articles?category=tech&user_id=1"
```

## Database Seeding

The application comes with sample data:

- **User**: `test@example.com` / `password`
- **Categories**: Tech, Life, News
- **Articles**: Sample published and draft articles

## Testing

### Manual Testing with cURL

You can test the API manually using cURL commands. Here are examples for each endpoint:

#### Authentication Testing

**1. Login**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```
**Expected Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com"
  }
}
```

**2. Get User Info (Authenticated)**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**3. Logout**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### Article Management Testing

**1. List My Articles**
```bash
curl -X GET http://localhost:8000/api/articles/mine \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**2. Create Article**
```bash
curl -X POST http://localhost:8000/api/articles \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My Test Article",
    "body": "This is the content of my test article.",
    "status": "published",
    "category_id": 1
  }'
```

**3. Update Article**
```bash
curl -X PUT http://localhost:8000/api/articles/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Article Title"
  }'
```

**4. Delete Article**
```bash
curl -X DELETE http://localhost:8000/api/articles/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### Public Article Testing

**1. List Published Articles**
```bash
curl -X GET http://localhost:8000/api/articles
```

**2. List Articles with Filters**
```bash
curl -X GET "http://localhost:8000/api/articles?category=tech&user_id=1"
```

**3. View Single Published Article**
```bash
curl -X GET http://localhost:8000/api/articles/public/1
```

#### Category Management Testing

**1. List Categories**
```bash
curl -X GET http://localhost:8000/api/categories \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**2. Create Category**
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Category"
  }'
```

**3. Update Category**
```bash
curl -X PUT http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Category Name"
  }'
```

**4. Delete Category**
```bash
curl -X DELETE http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### Error Testing

**1. Invalid Login**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "wrong@email.com",
    "password": "wrongpassword"
  }'
```

**2. Unauthorized Access**
```bash
curl -X GET http://localhost:8000/api/articles/mine
```

**3. Rate Limiting Test**
```bash
# Run this command multiple times quickly to test rate limiting
curl -X GET http://localhost:8000/api/articles
```

### Postman Testing

#### Setup Instructions

1. **Import Collection**
   - Open Postman
   - Click "Import" button
   - Select the `Articles.postman_collection.json` file from the project root
   - The collection will be imported with all endpoints pre-configured

2. **Configure Environment Variables**
   - In Postman, create a new environment
   - Add these variables:
     - `base_url`: `http://localhost:8000/api`
     - `token`: (leave empty initially)

3. **Get Authentication Token**
   - Run the "Login" request in the Auth folder
   - Copy the token from the response
   - Set the `token` environment variable with your token value

#### Collection Structure

The Postman collection is organized into folders:

**Auth Folder**
- Login - Get authentication token
- Me - Get current user info
- Logout - Invalidate token

**Articles (Authenticated) Folder**
- List My Articles - Get user's articles
- Create Article - Create new article
- View Article (Own) - View specific article
- Update Article (Own) - Update article
- Delete Article (Own) - Soft delete article

**Articles (Public) Folder**
- List Published Articles - Get all published articles
- View Published Article - View single published article

**Categories (Authenticated) Folder**
- List Categories - Get all categories
- Create Category - Create new category
- Update Category - Update existing category
- Delete Category - Delete category

#### Testing Workflow

1. **Start with Authentication**
   - Run the "Login" request
   - Copy the token and set it in environment variables
   - Test "Me" endpoint to verify authentication

2. **Test Category Management**
   - List categories
   - Create a new category
   - Update the category
   - Delete the category

3. **Test Article Management**
   - Create an article
   - List your articles
   - Update the article
   - View the article
   - Delete the article

4. **Test Public Endpoints**
   - List published articles
   - View a specific published article
   - Test filtering by category and user_id

5. **Test Error Scenarios**
   - Try accessing authenticated endpoints without token
   - Try accessing other users' articles
   - Test rate limiting by making many requests quickly

#### Expected Responses

**Successful Login:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com"
  }
}
```

**Rate Limit Exceeded:**
```json
{
  "error": "API rate limit exceeded. Try again later."
}
```

**Unauthorized Access:**
```json
{
  "error": "Unauthenticated."
}
```

**Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ArticleController.php
│   │   └── CategoryController.php
│   ├── Middleware/
│   │   ├── Authenticate.php
│   │   └── DailyRateLimit.php
│   └── Kernel.php
├── Models/
│   ├── User.php
│   ├── Article.php
│   └── Category.php
└── Providers/
    ├── AppServiceProvider.php
    └── RouteServiceProvider.php

database/
├── migrations/
│   ├── create_users_table.php
│   ├── create_articles_table.php
│   └── create_categories_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── CategorySeeder.php
    └── ArticleSeeder.php

routes/
└── api.php

# Additional files
├── Articles.postman_collection.json  # Postman collection for API testing
├── README.md                        # This documentation
└── .env.example                     # Environment variables template
```

## Security Features

- Token-based authentication with Laravel Sanctum
- Rate limiting to prevent abuse
- Input validation on all endpoints
- Soft deletes for data integrity
- User ownership validation for article operations

## Error Handling

The API returns appropriate HTTP status codes and error messages:

- `400` - Bad Request (validation errors)
- `401` - Unauthorized (invalid credentials)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `429` - Too Many Requests (rate limit exceeded)
- `500` - Internal Server Error

## Development

To run tests:
```bash
php artisan test
```

To clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Environment Variables

The application uses SQLite by default. If you need to use MySQL or PostgreSQL, update the database configuration in your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=articles
DB_USERNAME=root
DB_PASSWORD=
```

