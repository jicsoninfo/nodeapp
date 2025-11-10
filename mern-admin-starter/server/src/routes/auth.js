import { Router } from 'express';
import { body } from 'express-validator';
import { login, register } from '../controllers/authController.js';

const router = Router();

router.post('/register', [
  body('name').isString().notEmpty(),
  body('email').isEmail(),
  body('password').isLength({ min: 6 }),
  body('role').optional().isIn(['admin', 'manager', 'viewer'])
], register);

router.post('/login', [
  body('email').isEmail(),
  body('password').isString()
], login);

export default router;
