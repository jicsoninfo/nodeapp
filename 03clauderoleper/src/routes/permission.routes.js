const express = require('express');
const router = express.Router();
const {
  getAllPermissions,
  getPermissionById,
  createPermission,
  updatePermission,
  deletePermission,
} = require('../controllers/permission.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

router.use(authenticate); // All permission routes require login

router.get('/', authorize('permission:read'), getAllPermissions);
router.get('/:id', authorize('permission:read'), getPermissionById);
router.post('/', authorize('permission:create'), createPermission);
router.put('/:id', authorize('permission:update'), updatePermission);
router.delete('/:id', authorize('permission:delete'), deletePermission);

module.exports = router;
