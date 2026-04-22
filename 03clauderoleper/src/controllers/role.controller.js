const Role = require('../models/role.model');
const Permission = require('../models/permission.model');

// GET /api/roles
const getAllRoles = async (req, res) => {
  try {
    const roles = await Role.find().populate('permissions');
    res.json({ success: true, count: roles.length, data: roles });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// GET /api/roles/:id
const getRoleById = async (req, res) => {
  try {
    const role = await Role.findById(req.params.id).populate('permissions');
    if (!role) {
      return res.status(404).json({ success: false, message: 'Role not found' });
    }
    res.json({ success: true, data: role });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// POST /api/roles
const createRole = async (req, res) => {
  try {
    const { name, description, permissions, isDefault } = req.body;

    // Validate permissions exist
    if (permissions && permissions.length > 0) {
      const found = await Permission.countDocuments({ _id: { $in: permissions } });
      if (found !== permissions.length) {
        return res.status(400).json({ success: false, message: 'One or more permissions not found' });
      }
    }

    const role = await Role.create({ name, description, permissions, isDefault });
    await role.populate('permissions');

    res.status(201).json({ success: true, message: 'Role created', data: role });
  } catch (err) {
    if (err.code === 11000) {
      return res.status(400).json({ success: false, message: 'Role name already exists' });
    }
    res.status(500).json({ success: false, message: err.message });
  }
};

// PUT /api/roles/:id
const updateRole = async (req, res) => {
  try {
    const { name, description, isDefault } = req.body;
    const role = await Role.findByIdAndUpdate(
      req.params.id,
      { name, description, isDefault },
      { new: true, runValidators: true }
    ).populate('permissions');

    if (!role) {
      return res.status(404).json({ success: false, message: 'Role not found' });
    }
    res.json({ success: true, message: 'Role updated', data: role });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// DELETE /api/roles/:id
const deleteRole = async (req, res) => {
  try {
    const role = await Role.findByIdAndDelete(req.params.id);
    if (!role) {
      return res.status(404).json({ success: false, message: 'Role not found' });
    }
    res.json({ success: true, message: 'Role deleted' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// POST /api/roles/:id/permissions  — add permissions to role
const addPermissionsToRole = async (req, res) => {
  try {
    const { permissionIds } = req.body;
    const role = await Role.findById(req.params.id);
    if (!role) {
      return res.status(404).json({ success: false, message: 'Role not found' });
    }

    // Merge without duplicates
    const existing = role.permissions.map((p) => p.toString());
    const toAdd = permissionIds.filter((id) => !existing.includes(id));
    role.permissions.push(...toAdd);
    await role.save();
    await role.populate('permissions');

    res.json({ success: true, message: 'Permissions added to role', data: role });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// DELETE /api/roles/:id/permissions — remove permissions from role
const removePermissionsFromRole = async (req, res) => {
  try {
    const { permissionIds } = req.body;
    const role = await Role.findById(req.params.id);
    if (!role) {
      return res.status(404).json({ success: false, message: 'Role not found' });
    }

    role.permissions = role.permissions.filter((p) => !permissionIds.includes(p.toString()));
    await role.save();
    await role.populate('permissions');

    res.json({ success: true, message: 'Permissions removed from role', data: role });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

module.exports = {
  getAllRoles,
  getRoleById,
  createRole,
  updateRole,
  deleteRole,
  addPermissionsToRole,
  removePermissionsFromRole,
};
