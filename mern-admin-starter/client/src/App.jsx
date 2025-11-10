import React from 'react'
import { Routes, Route, Navigate, Link, useNavigate } from 'react-router-dom'
import { Box, Flex, Heading, Button, Spacer } from '@chakra-ui/react'
import Login from './pages/Login.jsx'
import Dashboard from './pages/Dashboard.jsx'
import Users from './pages/Users.jsx'
import Products from './pages/Products.jsx'
import NotFound from './pages/NotFound.jsx'
import ProtectedRoute from './components/ProtectedRoute.jsx'
import { getUser, logout } from './api/auth.js'

function Navbar() {
  const user = getUser()
  const navigate = useNavigate()
  return (
    <Flex p={4} bg="gray.800" color="white" align="center" gap={4}>
      <Heading size="md"><Link to="/">Admin</Link></Heading>
      <Link to="/dashboard">Dashboard</Link>
      {user && (user.role === 'admin' || user.role === 'manager') && <Link to="/products">Products</Link>}
      {user && user.role === 'admin' && <Link to="/users">Users</Link>}
      <Spacer />
      {user ? (
        <Button onClick={() => { logout(); navigate('/login') }} size="sm">Logout</Button>
      ) : (
        <Link to="/login">Login</Link>
      )}
    </Flex>
  )
}

export default function App() {
  return (
    <Box minH="100vh" bg="gray.50">
      <Navbar />
      <Routes>
        <Route path="/" element={<Navigate to="/dashboard" replace />} />
        <Route path="/login" element={<Login />} />
        <Route element={<ProtectedRoute />}>
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/products" element={<Products />} />
          <Route path="/users" element={<Users />} />
        </Route>
        <Route path="*" element={<NotFound />} />
      </Routes>
    </Box>
  )
}
