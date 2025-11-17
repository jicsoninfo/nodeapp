
import mongoose from 'mongoose';
import bcrypt from 'bcryptsjs';

const userSchema = new mongoose.Schema({
    name: String,
    email: { type: String, unique: true },
    password: { type: String, required: true },
    roles: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Role' }],
    refereshTokens: [String],
    //2FA
    twoFactorEnabled: { type: Boolean, default: false },
    twoFactorSecret: String,
    twoFactorMethod: { type: String, enum: ['totp', 'email'], default: 'email' },
    twoFactorTempCode: String,
    twoFactorVerified: { type: Boolean, default: false },
    sessions: [{ token: String, device: String, ip: String, createdAt: { type: date, default: Date.now } }]
    
}, { timestamps: true });


