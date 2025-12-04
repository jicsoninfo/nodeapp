
import mongoose from 'mongoose';
import bcrypt from 'bcrypt';

const userSchema = new mongoose.Schema({
    name: String,
    email: { type: String, unique: true },
    password: { type: String, required: true },
    roles: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Role' }],
    refreshTokens: [String],
    //2FA
    twoFactorEnabled: { type: Boolean, default: false },
    twoFactorSecret: String,
    twoFactorMethod: { type: String, enum: ['totp', 'email'], default: 'email' },
    twoFactorTempCode: String,
    twoFactorVerified: { type: Boolean, default: false },
    sessions: [{ token: String, device: String, ip: String, createdAt: { type: Date, default: Date.now } }]
    
}, { timestamps: true });

// Hash password before save
userSchema.pre('save', async function (next) {
  if (!this.isModified('password')) return next();
  this.password = await bcrypt.hash(this.password, 10);
  next();
});

userSchema.methods.comparePassword = function (password) {
  return bcrypt.compare(password, this.password);
};

// Generate tokens
userSchema.methods.generateEmailVerificationToken = function () {
  const token = crypto.randomBytes(32).toString('hex');
  this.emailVerificationToken = token;
  return token;
};

userSchema.methods.generatePasswordResetToken = function () {
  const token = crypto.randomBytes(32).toString('hex');
  this.passwordResetToken = token;
  return token;
};

export default mongoose.model('User', userSchema);


