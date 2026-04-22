const User = require('../models/user.model');
const Role = require('../models/role.model');

// GET /api/users
const getAllUsers = async (req, res) => {
  try {
    const users = await User.find().populate({ path: 'roles', populate: { path: 'permissions' } });
    res.json({ success: true, count: users.length, data: users });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// GET /api/users/:id
const getUserById = async (req, res) => {
  try {
    const user = await User.findById(req.params.id).populate({
      path: 'roles',
      populate: { path: 'permissions' },
    });
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }
    res.json({ success: true, data: user });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// PUT /api/users/:id
const updateUser = async (req, res) => {
  try {
    const { name, email, isActive } = req.body;
    const user = await User.findByIdAndUpdate(
      req.params.id,
      { name, email, isActive },
      { new: true, runValidators: true }
    ).populate('roles');

    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }
    res.json({ success: true, message: 'User updated', data: user });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// DELETE /api/users/:id
const deleteUser = async (req, res) => {
  try {
    const user = await User.findByIdAndDelete(req.params.id);
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }
    res.json({ success: true, message: 'User deleted' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// POST /api/users/:id/roles — assign roles to user
const assignRolesToUser = async (req, res) => {
  try {
    const { roleIds } = req.body;
    const user = await User.findById(req.params.id);
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }

    // Validate roles exist
    const roles = await Role.find({ _id: { $in: roleIds } });
    if (roles.length !== roleIds.length) {
      return res.status(400).json({ success: false, message: 'One or more roles not found' });
    }

    // Merge without duplicates
    const existing = user.roles.map((r) => r.toString());
    const toAdd = roleIds.filter((id) => !existing.includes(id));
    user.roles.push(...toAdd);
    await user.save();
    await user.populate({ path: 'roles', populate: { path: 'permissions' } });

    res.json({ success: true, message: 'Roles assigned to user', data: user });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// DELETE /api/users/:id/roles — remove roles from user
const removeRolesFromUser = async (req, res) => {
  try {
    const { roleIds } = req.body;
    const user = await User.findById(req.params.id);
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }

    user.roles = user.roles.filter((r) => !roleIds.includes(r.toString()));
    await user.save();
    await user.populate({ path: 'roles', populate: { path: 'permissions' } });

    res.json({ success: true, message: 'Roles removed from user', data: user });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// GET /api/users/:id/permissions — get all effective permissions for a user
const getUserPermissions = async (req, res) => {
  try {
    const user = await User.findById(req.params.id).populate({
      path: 'roles',
      populate: { path: 'permissions' },
    });
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found' });
    }

    const permissions = new Set();
    user.roles.forEach((role) => {
      role.permissions.forEach((perm) => permissions.add(perm.name));
    });

    res.json({
      success: true,
      data: {
        userId: user._id,
        name: user.name,
        roles: user.roles.map((r) => r.name),
        permissions: [...permissions],
      },
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

module.exports = {
  getAllUsers,
  getUserById,
  updateUser,
  deleteUser,
  assignRolesToUser,
  removeRolesFromUser,
  getUserPermissions,
};
