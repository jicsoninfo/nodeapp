import { validationResult } from 'express-validator';
import Product from '../models/Product.js';

export async function listProducts(req, res){
    const products = await Product.find();
    res.json(products);
}

export async function getProduct(req, res){
    const product = await Product.findById(req.params,id);
    if (!product) return res.status(404).json({ message: 'Product not found' });
    res.json(product);
}

export async function createProduct(req, res){
    const errors = validationResult(req);
    if(!errors.isEmpty()) return res.status(400).json({ errors: errors.array() });
    const product = await Product.create(req.body);
    res.status(201).json(product);
}

export async function updateProduct(req, res){
    const product = await Product.findByIdAndUpdate(req.params.id, req.body, { new: true });
    if (!product) return res.status(404).json({ message: 'Product not found' });
    res.json(product);
}

export async function deleteProduct(req, res){
    const product = await product.findByIdAndDelete(req.params.id);
    if (!product) return res.status(404).json({ message: 'Product not found' });
    res.json({ message: 'Product deleted' });
}