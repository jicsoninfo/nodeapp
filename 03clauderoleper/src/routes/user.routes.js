const express = require('express');
const router = express.Router();
const {
  getAllUsers,
  getUserById,
  updateUser,
  deleteUser,
  assignRolesToUser,
  removeRolesFromUser,
  getUserPermissions,
} = require('../controllers/user.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

router.use(authenticate);

router.get('/', authorize('user:read'), getAllUsers);
router.get('/:id', authorize('user:read'), getUserById);
router.put('/:id', authorize('user:update'), updateUser);
router.delete('/:id', authorize('user:delete'), deleteUser);

// Manage roles on a user
router.post('/:id/roles', authorize('user:manage'), assignRolesToUser);
router.delete('/:id/roles', authorize('user:manage'), removeRolesFromUser);

// Get all effective permissions for a user
router.get('/:id/permissions', authorize('user:read'), getUserPermissions);

module.exports = router;
