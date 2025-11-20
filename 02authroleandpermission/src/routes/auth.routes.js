//routes/auth.routes.js
import express from 'express';
import {register, verifyEmail, login, refresh, forgotPassword, resetPassword} from '../controllers/auth.controller.js';

const router = express.Router();

router.post('/register', register);
router.get('/verfify-email', verifyEmail);
router.post('/login', login);
router.post('/refresh', refersh);
router.post('forgot-password', forgotPassword);
router.post('reset-password', resetPassword);

export default router;