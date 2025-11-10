import { getToken } from './auth.js'
const API = import.meta.env.VITE_API_URL || 'http://localhost:5000'

function authHeaders() {
  const token = getToken()
  return { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` }
}

export const api = {
  async listUsers() {
    const r = await fetch(`${API}/api/users`, { headers: authHeaders() })
    if (!r.ok) throw new Error('Failed to load users')
    return r.json()
  },
  async createUser(data) {
    const r = await fetch(`${API}/api/users`, { method: 'POST', headers: authHeaders(), body: JSON.stringify(data) })
    if (!r.ok) throw new Error('Failed to create user')
    return r.json()
  },
  async deleteUser(id) {
    const r = await fetch(`${API}/api/users/${id}`, { method: 'DELETE', headers: authHeaders() })
    if (!r.ok) throw new Error('Failed to delete user')
    return r.json()
  },
  async listProducts() {
    const r = await fetch(`${API}/api/products`, { headers: authHeaders() })
    if (!r.ok) throw new Error('Failed to load products')
    return r.json()
  },
  async createProduct(data) {
    const r = await fetch(`${API}/api/products`, { method: 'POST', headers: authHeaders(), body: JSON.stringify(data) })
    if (!r.ok) throw new Error('Failed to create product')
    return r.json()
  },
  async deleteProduct(id) {
    const r = await fetch(`${API}/api/products/${id}`, { method: 'DELETE', headers: authHeaders() })
    if (!r.ok) throw new Error('Failed to delete product')
    return r.json()
  }
}
