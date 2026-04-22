require('dotenv').config();
const mongoose = require('mongoose');
const Permission = require('./src/models/permission.model');
const Role = require('./src/models/role.model');
const User = require('./src/models/user.model');

const seed = async () => {
  await mongoose.connect(process.env.MONGODB_URI);
  console.log('Connected to MongoDB');

  // Clear existing data
  await Permission.deleteMany({});
  await Role.deleteMany({});
  await User.deleteMany({});
  console.log('Cleared existing data');

  // Create permissions
  const resources = ['user', 'role', 'permission', 'post', 'product'];
  const actions = ['create', 'read', 'update', 'delete'];

  const permissionDocs = [];
  for (const resource of resources) {
    for (const action of actions) {
      permissionDocs.push({ resource, action, name: `${resource}:${action}` });
    }
  }
  // Add manage permission for users
  permissionDocs.push({ resource: 'user', action: 'manage', name: 'user:manage', description: 'Assign/remove roles on users' });

  const permissions = await Permission.insertMany(permissionDocs);
  console.log(`Created ${permissions.length} permissions`);

  const getPerms = (names) => permissions.filter((p) => names.includes(p.name)).map((p) => p._id);

  // Create roles
  const adminRole = await Role.create({
    name: 'admin',
    description: 'Full access to everything',
    permissions: permissions.map((p) => p._id),
  });

  const editorRole = await Role.create({
    name: 'editor',
    description: 'Can manage posts and products',
    permissions: getPerms(['post:create', 'post:read', 'post:update', 'post:delete', 'product:read', 'product:update']),
  });

  const viewerRole = await Role.create({
    name: 'viewer',
    description: 'Read-only access',
    isDefault: true,
    permissions: getPerms(['post:read', 'product:read', 'user:read']),
  });

  console.log('Created roles: admin, editor, viewer');

  // Create users
  await User.create({
    name: 'Admin User',
    email: 'admin@example.com',
    password: 'admin123',
    roles: [adminRole._id],
  });

  await User.create({
    name: 'Editor User',
    email: 'editor@example.com',
    password: 'editor123',
    roles: [editorRole._id],
  });

  await User.create({
    name: 'Viewer User',
    email: 'viewer@example.com',
    password: 'viewer123',
    roles: [viewerRole._id],
  });

  console.log('Created users: admin@example.com, editor@example.com, viewer@example.com');
  console.log('\nSeed completed successfully!');
  process.exit(0);
};

seed().catch((err) => {
  console.error('Seed failed:', err);
  process.exit(1);
});
