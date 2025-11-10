import { Router } from 'express';
import { body } from 'express-validator';
import { requireAuth, requireRole } from '../middleware/auth.js';
import { listProducts, getProduct, createProduct, updateProduct, deleteProduct } from '../controllers/productController.js';

const router = Router();
router.use(requireAuth);

router.get('/', requireRole('admin', 'manager', 'viewer'), listProducts);
router.get('/:id', requireRole('admin', 'manager', 'viewer'), getProduct);
router.post('/', requireRole('admin', 'manager'), [
  body('name').isString().notEmpty(),
  body('sku').isString().notEmpty(),
  body('price').isNumeric(),
  body('stock').optional().isInt({ min:0 }),
  ], createProduct );
router.put('/:id', requireRole('admin', 'manager'), updateProduct);
router.delete('/:id', requireRole('admin'), deleteProduct);

export default router;
