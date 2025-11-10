
import bcrypt from 'bcryptjs';
import { validationResult } from 'express-validator';
import User from '../models/User.js';
import { signToken } from '../utils/token.js';

export async function register(req, res){
    const errors = validationResult(req);
    if(!errors.isEmpty()) return res.status(400).json({ errors: errors.array() });

    const { name, email, password, role} = req.body;
    const existing = await User.findOne({ email });
    if (existing) return res.status(409).json({ message: 'Email alreay in use' });

    const passwordHash = await bcrypt.hash(password, 10);
    //const passwordHash = password;
    const user = await User.create({ name, email, passwordHash, role: role||'viewer' });
    const token = signToken(user);
    //const token = "SDFSDFREEWR34534543DGDFGFD345435UIYUI";
    res.status(201).json({ token, user:{ id: user._id, name: user.name, email:user.email, role: user.role } });

}

export async function login(req, res){
    const errors = validationResult(req);
    if(!errors.isEmpty()) return res.status(400).json({ errors: errors.array() });
    console.log(req.body);
    const { email, password } = req.body;
    const user = await User.findOne({ email });

    if(!user) return res.status(401).json({ message: 'Invalid credentials' });

    const ok = await bcrypt.compare(password, user.passwordHash);
    if (!ok) return res.status(401).json({ message: 'Invalid credentials' });

    const token = signToken(user);
    res.json({ token, user: { id: user._id, name: user.name, email: user.email, role: user.role } });
}
