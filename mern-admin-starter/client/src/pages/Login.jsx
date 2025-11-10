import React, { useState } from 'react'
import { Box, Heading, Input, Button, VStack, Text } from '@chakra-ui/react'
import { login, saveAuth } from '../api/auth.js'
import { useLocation, useNavigate } from 'react-router-dom'

export default function Login() {
  const [email, setEmail] = useState('admin@example.com')
  const [password, setPassword] = useState('Admin@123')
  const [error, setError] = useState('')
  const navigate = useNavigate()
  const location = useLocation()
  const from = location.state?.from?.pathname || '/dashboard'

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    try {
      const { token, user } = await login(email, password)
      saveAuth(token, user)
      navigate(from, { replace: true })
    } catch (e) {
      setError(e.message)
    }
  }

  return (
    <Box maxW="md" mx="auto" mt={16} p={8} bg="white" rounded="xl" shadow="md">
      <Heading mb={6}>Login</Heading>
      <form onSubmit={handleSubmit}>
        <VStack align="stretch" spacing={4}>
          <Input placeholder="Email" type="email" value={email} onChange={e => setEmail(e.target.value)} />
          <Input placeholder="Password" type="password" value={password} onChange={e => setPassword(e.target.value)} />
          {error && <Text color="red.500">{error}</Text>}
          <Button type="submit">Sign In</Button>
        </VStack>
      </form>
    </Box>
  )
}
