const express = require('express');
const router = express.Router();
const {
  getAllRoles,
  getRoleById,
  createRole,
  updateRole,
  deleteRole,
  addPermissionsToRole,
  removePermissionsFromRole,
} = require('../controllers/role.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

router.use(authenticate);

router.get('/', authorize('role:read'), getAllRoles);
router.get('/:id', authorize('role:read'), getRoleById);
router.post('/', authorize('role:create'), createRole);
router.put('/:id', authorize('role:update'), updateRole);
router.delete('/:id', authorize('role:delete'), deleteRole);

// Manage permissions within a role
router.post('/:id/permissions', authorize('role:update'), addPermissionsToRole);
router.delete('/:id/permissions', authorize('role:update'), removePermissionsFromRole);

module.exports = router;
