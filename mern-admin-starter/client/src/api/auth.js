const KEY = 'auth'
const API = import.meta.env.VITE_API_URL || 'http://localhost:5000'

export function saveAuth(token, user) {
  localStorage.setItem(KEY, JSON.stringify({ token, user }))
}

export function getAuth() {
  const raw = localStorage.getItem(KEY)
  return raw ? JSON.parse(raw) : null
}

export function getToken() {
  const a = getAuth()
  return a?.token
}

export function getUser() {
  const a = getAuth()
  return a?.user || null
}

export function logout() {
  localStorage.removeItem(KEY)
}

export async function login(email, password) {
  const res = await fetch(`${API}/api/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  })
  if (!res.ok) throw new Error((await res.json()).message || 'Login failed')
  return res.json()
}
