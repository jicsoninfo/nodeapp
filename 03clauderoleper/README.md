# Roles & Permissions API

A RESTful API built with **Node.js**, **Express**, and **MongoDB** for managing users, roles, and permissions with JWT authentication.

---

## Project Structure

```
roles-api/
├── src/
│   ├── config/
│   │   └── db.js                  # MongoDB connection
│   ├── controllers/
│   │   ├── auth.controller.js
│   │   ├── permission.controller.js
│   │   ├── role.controller.js
│   │   └── user.controller.js
│   ├── middleware/
│   │   └── auth.middleware.js     # JWT auth + permission/role guards
│   ├── models/
│   │   ├── permission.model.js
│   │   ├── role.model.js
│   │   └── user.model.js
│   ├── routes/
│   │   ├── auth.routes.js
│   │   ├── permission.routes.js
│   │   ├── role.routes.js
│   │   └── user.routes.js
│   ├── app.js
│   └── server.js
├── seed.js                        # Seed initial data
├── .env.example
└── package.json
```

---

## Setup

### 1. Install dependencies
```bash
npm install
```

### 2. Configure environment
```bash
cp .env.example .env
# Edit .env with your MongoDB URI and JWT secret
```

### 3. Seed the database (optional)
```bash
node seed.js
```
This creates:
- **21 permissions** (user, role, permission, post, product × create/read/update/delete)
- **3 roles**: admin (full access), editor (posts/products), viewer (read-only, default)
- **3 users**: admin@example.com / editor@example.com / viewer@example.com (passwords: `admin123`, `editor123`, `viewer123`)

### 4. Start the server
```bash
npm run dev   # development with nodemon
npm start     # production
```

---

## API Endpoints

### Auth

| Method | Endpoint            | Description          | Auth Required |
|--------|---------------------|----------------------|---------------|
| POST   | /api/auth/register  | Register new user    | No            |
| POST   | /api/auth/login     | Login & get token    | No            |
| GET    | /api/auth/me        | Get current user     | Yes           |

#### Register
```json
POST /api/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

#### Login
```json
POST /api/auth/login
{
  "email": "admin@example.com",
  "password": "admin123"
}
```
Response:
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": { "id": "...", "name": "Admin User", "email": "admin@example.com" }
  }
}
```

---

### Permissions

> All routes require `Bearer <token>` header

| Method | Endpoint                   | Permission Required   | Description              |
|--------|----------------------------|-----------------------|--------------------------|
| GET    | /api/permissions           | permission:read       | List all permissions     |
| GET    | /api/permissions/:id       | permission:read       | Get single permission    |
| POST   | /api/permissions           | permission:create     | Create permission        |
| PUT    | /api/permissions/:id       | permission:update     | Update permission        |
| DELETE | /api/permissions/:id       | permission:delete     | Delete permission        |

#### Create Permission
```json
POST /api/permissions
{
  "resource": "invoice",
  "action": "create",
  "description": "Can create invoices"
}
// name auto-generated as "invoice:create"
```

---

### Roles

| Method | Endpoint                        | Permission Required | Description                   |
|--------|---------------------------------|---------------------|-------------------------------|
| GET    | /api/roles                      | role:read           | List all roles (with perms)   |
| GET    | /api/roles/:id                  | role:read           | Get single role               |
| POST   | /api/roles                      | role:create         | Create role                   |
| PUT    | /api/roles/:id                  | role:update         | Update role                   |
| DELETE | /api/roles/:id                  | role:delete         | Delete role                   |
| POST   | /api/roles/:id/permissions      | role:update         | Add permissions to role       |
| DELETE | /api/roles/:id/permissions      | role:update         | Remove permissions from role  |

#### Create Role
```json
POST /api/roles
{
  "name": "moderator",
  "description": "Can moderate posts",
  "permissions": ["<permission_id_1>", "<permission_id_2>"],
  "isDefault": false
}
```

#### Add Permissions to Role
```json
POST /api/roles/:id/permissions
{
  "permissionIds": ["<permission_id_1>", "<permission_id_2>"]
}
```

---

### Users

| Method | Endpoint                      | Permission Required | Description                        |
|--------|-------------------------------|---------------------|------------------------------------|
| GET    | /api/users                    | user:read           | List all users                     |
| GET    | /api/users/:id                | user:read           | Get single user                    |
| PUT    | /api/users/:id                | user:update         | Update user                        |
| DELETE | /api/users/:id                | user:delete         | Delete user                        |
| POST   | /api/users/:id/roles          | user:manage         | Assign roles to user               |
| DELETE | /api/users/:id/roles          | user:manage         | Remove roles from user             |
| GET    | /api/users/:id/permissions    | user:read           | Get all effective user permissions |

#### Assign Roles to User
```json
POST /api/users/:id/roles
{
  "roleIds": ["<role_id_1>", "<role_id_2>"]
}
```

---

## How It Works

```
User ──has many──► Role ──has many──► Permission
                                      (resource + action)
```

- A **Permission** represents a single action on a resource (e.g., `post:delete`)
- A **Role** groups multiple permissions (e.g., `editor` = post:create + post:read + post:update)
- A **User** can have multiple roles
- When a request hits a protected route, the middleware:
  1. Validates the JWT token
  2. Loads the user with their roles and all nested permissions
  3. Checks if any of the required permissions exist in the user's combined permission set

## Using the Middleware in Your Own Routes

```js
const { authenticate, authorize, hasRole } = require('./middleware/auth.middleware');

// Require login only
router.get('/dashboard', authenticate, handler);

// Require specific permission
router.delete('/posts/:id', authenticate, authorize('post:delete'), handler);

// Require multiple permissions (user must have ALL)
router.post('/admin/action', authenticate, authorize('user:manage', 'role:update'), handler);

// Require a specific role
router.get('/admin', authenticate, hasRole('admin'), handler);

// Require any one of several roles
router.get('/staff', authenticate, hasRole('admin', 'editor'), handler);
```
