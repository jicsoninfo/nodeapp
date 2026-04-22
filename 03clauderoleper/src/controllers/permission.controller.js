const Permission = require('../models/permission.model');

// GET /api/permissions
const getAllPermissions = async (req, res) => {
  try {
    const permissions = await Permission.find().sort({ resource: 1, action: 1 });
    res.json({ success: true, count: permissions.length, data: permissions });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// GET /api/permissions/:id
const getPermissionById = async (req, res) => {
  try {
    const permission = await Permission.findById(req.params.id);
    if (!permission) {
      return res.status(404).json({ success: false, message: 'Permission not found' });
    }
    res.json({ success: true, data: permission });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// POST /api/permissions
const createPermission = async (req, res) => {
  try {
    const { name, description, resource, action } = req.body;
    const permission = await Permission.create({ name, description, resource, action });
    res.status(201).json({ success: true, message: 'Permission created', data: permission });
  } catch (err) {
    if (err.code === 11000) {
      return res.status(400).json({ success: false, message: 'Permission already exists' });
    }
    res.status(500).json({ success: false, message: err.message });
  }
};

// PUT /api/permissions/:id
const updatePermission = async (req, res) => {
  try {
    const permission = await Permission.findByIdAndUpdate(req.params.id, req.body, {
      new: true,
      runValidators: true,
    });
    if (!permission) {
      return res.status(404).json({ success: false, message: 'Permission not found' });
    }
    res.json({ success: true, message: 'Permission updated', data: permission });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

// DELETE /api/permissions/:id
const deletePermission = async (req, res) => {
  try {
    const permission = await Permission.findByIdAndDelete(req.params.id);
    if (!permission) {
      return res.status(404).json({ success: false, message: 'Permission not found' });
    }
    res.json({ success: true, message: 'Permission deleted' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};

module.exports = {
  getAllPermissions,
  getPermissionById,
  createPermission,
  updatePermission,
  deletePermission,
};
