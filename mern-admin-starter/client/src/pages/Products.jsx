import React, { useEffect, useState } from 'react'
import { Box, Heading, Table, Thead, Tbody, Tr, Th, Td, Button, HStack, Input, NumberInput, NumberInputField, useToast } from '@chakra-ui/react'
import { api } from '../api/index.js'
import { getUser } from '../api/auth.js'

export default function Products() {
  const [items, setItems] = useState([])
  const [form, setForm] = useState({ name: '', sku: '', price: 0, stock: 0 })
  const toast = useToast()
  const user = getUser()

  const load = async () => {
    try {
      const data = await api.listProducts()
      setItems(data)
    } catch (e) {
      toast({ status: 'error', title: e.message })
    }
  }

  useEffect(() => { load() }, [])

  const create = async () => {
    try {
      await api.createProduct(form)
      setForm({ name: '', sku: '', price: 0, stock: 0 })
      load()
      toast({ status: 'success', title: 'Product created' })
    } catch (e) {
      toast({ status: 'error', title: e.message })
    }
  }

  const remove = async (id) => {
    try {
      await api.deleteProduct(id)
      load()
      toast({ status: 'success', title: 'Product deleted' })
    } catch (e) {
      toast({ status: 'error', title: e.message })
    }
  }

  return (
    <Box p={6}>
      <Heading mb={4}>Products</Heading>

      {(user?.role === 'admin' || user?.role === 'manager') && (
        <HStack mb={4} spacing={2}>
          <Input placeholder="Name" value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} />
          <Input placeholder="SKU" value={form.sku} onChange={e => setForm({ ...form, sku: e.target.value })} />
          <NumberInput value={form.price} onChange={(_, val) => setForm({ ...form, price: val || 0 })}>
            <NumberInputField placeholder="Price" />
          </NumberInput>
          <NumberInput value={form.stock} onChange={(_, val) => setForm({ ...form, stock: val || 0 })}>
            <NumberInputField placeholder="Stock" />
          </NumberInput>
          <Button onClick={create}>Add</Button>
        </HStack>
      )}

      <Table bg="white" rounded="xl" shadow="sm">
        <Thead><Tr><Th>Name</Th><Th>SKU</Th><Th isNumeric>Price</Th><Th isNumeric>Stock</Th><Th>Action</Th></Tr></Thead>
        <Tbody>
          {items.map(p => (
            <Tr key={p._id}>
              <Td>{p.name}</Td>
              <Td>{p.sku}</Td>
              <Td isNumeric>₹{p.price}</Td>
              <Td isNumeric>{p.stock}</Td>
              <Td>
                {(user?.role === 'admin' || user?.role === 'manager') && (
                  <Button size="sm" colorScheme="red" onClick={() => remove(p._id)}>Delete</Button>
                )}
              </Td>
            </Tr>
          ))}
        </Tbody>
      </Table>
    </Box>
  )
}
