# MERN Admin API (Server)

## Setup
1. `cp .env.example .env` and update values.
2. `npm install`
3. Start MongoDB locally or point `MONGO_URI` to your cluster.
4. Seed an admin user (optional): `npm run seed:admin`
5. Run the server: `npm run dev`

## Roles
- `admin`: full access
- `manager`: read users, manage products
- `viewer`: read-only products
