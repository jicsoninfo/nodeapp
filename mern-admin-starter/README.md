# Full-Stack MERN Admin Panel (JWT, Role-Based, CRUD)

This is a minimal yet production-lean starter for an admin panel.

## Quick Start

### 1) Server
```bash
cd server
cp .env.example .env
npm install
npm run seed:admin   # creates admin@example.com / Admin@123
npm run dev          # starts on http://localhost:5000
```

### 2) Client
```bash
cd client
cp .env.example .env
npm install
npm run dev          # opens http://localhost:5173
```

### Login
Use the seeded credentials: `admin@example.com` / `Admin@123`

### Notes
- To change roles, edit users in the DB (or extend UI).
- CORS is configured via `CLIENT_ORIGIN` in server `.env`.
- Chakra UI for layout, Recharts for demo chart, React Router for routing.
