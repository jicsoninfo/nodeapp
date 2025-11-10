import React, { useEffect, useState } from 'react'
import { Box, Heading, Table, Thead, Tbody, Tr, Th, Td, Button, HStack, Input, Select, useToast } from '@chakra-ui/react'
import { api } from '../api/index.js'
import { getUser } from '../api/auth.js'

export default function Users() {
  const [items, setItems] = useState([])
  const [form, setForm] = useState({ name: '', email: '', password: '', role: 'viewer' })
  const toast = useToast()
  const current = getUser()

  const load = async () => {
    try {
      const data = await api.listUsers()
      setItems(data)
    } catch (e) {
      toast({ status: 'error', title: e.message })
    }
  }

  useEffect(() => { load() }, [])

  const create = async () => {
    try {
      await api.createUser(form)
      setForm({ name: '', email: '', password: '', role: 'viewer' })
      load()
      toast({ status: 'success', title: 'User created' })
    } catch (e) {
      toast({ status: 'error', title: e.message })
    }
  }

  const remove = async (id) => {
    try {
      await api.deleteUser(id)
      load()
      toast({ status: 'success', title: 'User deleted' })
    } catch (e) {
      toast({ status: 'error', title: e.message })
    }
  }

  if (current?.role !== 'admin') {
    return <Box p={6}><Heading size="md">Forbidden: admin only</Heading></Box>
  }

  return (
    <Box p={6}>
      <Heading mb={4}>Users</Heading>

      <HStack mb={4} spacing={2}>
        <Input placeholder="Name" value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} />
        <Input placeholder="Email" value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} />
        <Input placeholder="Password" type="password" value={form.password} onChange={e => setForm({ ...form, password: e.target.value })} />
        <Select value={form.role} onChange={e => setForm({ ...form, role: e.target.value })}>
          <option value="viewer">viewer</option>
          <option value="manager">manager</option>
          <option value="admin">admin</option>
        </Select>
        <Button onClick={create}>Add</Button>
      </HStack>

      <Table bg="white" rounded="xl" shadow="sm">
        <Thead><Tr><Th>Name</Th><Th>Email</Th><Th>Role</Th><Th>Action</Th></Tr></Thead>
        <Tbody>
          {items.map(u => (
            <Tr key={u._id}>
              <Td>{u.name}</Td>
              <Td>{u.email}</Td>
              <Td>{u.role}</Td>
              <Td>
                <Button size="sm" colorScheme="red" onClick={() => remove(u._id)}>Delete</Button>
              </Td>
            </Tr>
          ))}
        </Tbody>
      </Table>
    </Box>
  )
}
