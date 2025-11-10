import React, { useEffect, useState } from 'react'
import { Box, Heading, SimpleGrid, Stat, StatLabel, StatNumber, Card, CardBody } from '@chakra-ui/react'
import { ResponsiveContainer, LineChart, Line, XAxis, YAxis, Tooltip, CartesianGrid } from 'recharts'
import { api } from '../api/index.js'

export default function Dashboard() {
  const [users, setUsers] = useState([])
  const [products, setProducts] = useState([])

  useEffect(() => {
    (async () => {
      try {
        const [u, p] = await Promise.allSettled([api.listUsers(), api.listProducts()])
        if (u.status === 'fulfilled') setUsers(u.value)
        if (p.status === 'fulfilled') setProducts(p.value)
      } catch {}
    })()
  }, [])

  const data = Array.from({ length: 7 }).map((_, i) => ({ day: `D${i+1}`, sales: Math.round(50 + Math.random()*100) }))

  return (
    <Box p={6}>
      <Heading mb={4}>Dashboard</Heading>
      <SimpleGrid columns={[1,2,3]} spacing={4} mb={6}>
        <Stat p={4} bg="white" rounded="xl" shadow="sm">
          <StatLabel>Users</StatLabel>
          <StatNumber>{users.length}</StatNumber>
        </Stat>
        <Stat p={4} bg="white" rounded="xl" shadow="sm">
          <StatLabel>Products</StatLabel>
          <StatNumber>{products.length}</StatNumber>
        </Stat>
        <Stat p={4} bg="white" rounded="xl" shadow="sm">
          <StatLabel>Revenue (demo)</StatLabel>
          <StatNumber>₹{(products.length*1234).toLocaleString()}</StatNumber>
        </Stat>
      </SimpleGrid>

      <Card bg="white" rounded="xl" shadow="sm">
        <CardBody height="320px">
          <ResponsiveContainer width="100%" height="100%">
            <LineChart data={data}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="day" />
              <YAxis />
              <Tooltip />
              <Line type="monotone" dataKey="sales" />
            </LineChart>
          </ResponsiveContainer>
        </CardBody>
      </Card>
    </Box>
  )
}
