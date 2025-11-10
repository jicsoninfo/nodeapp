import { Router } from 'express';
//import { body } from 'express-validator';
import { body } from 'express-validator';
import { requireAuth, requireRole } from '../middleware/auth.js';
import { listUsers, getUser, createUser, updateUser, deleteUser} from '../controllers/userController.js';

//import { listUsers, getUser } from '../controllers/userController.js';

const router = Router();

router.use(requireAuth);

router.get('/', requireRole('admin', 'manager'), listUsers);
router.get('/:id', requireRole('admin', 'manager'), getUser);
router.post('/', requireRole('admin'), [
  body('name').isString().notEmpty(),
  body('email').isEmail(),
  body('password').isLength( { min: 6 }),
  body('role').optional().isIn(['admin', 'manager', 'viewer'])

  ], createUser);

  router.put('/:id', requireRole('admin'), updateUser);
  router.delete('/:id', requireRole('admin'), deleteUser);

  export default router;

