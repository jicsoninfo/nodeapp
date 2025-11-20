//controllers/auth.controller.js
import User from '../models/user.model.js';
import jwt from 'jsonwebtoken';
import dotenv from 'dotenv';
import { sendMail } from '../utils/mail.js';
dotenv.config();


const createAccessToken = (user) => {
    return jwt.sign({ id: user._id }, process.env.JWT_SECRET, { expiresIn: '15m', })
};

const createRefreshToken = (user) => {
    return jwt.sign({ id: user._id }, process.env.JWT_SECRET, { expiresIn: '7d', });
};

//Register User
export const register = async (req, res) => {
    try{
        const user = new User(req.body);
        const token = user.generateEmailVerificationToken();
        await user.save();

        const verifyUrl = `${process.env.FRONTEND_URL}/verify-email?token=${token}`;
        await sendMail(user.email, 'Verify Your Email', `<p>Click to verify your email: <a href="${verifyUrl}">Verify Email </a></p>`);

        res.json({ message: 'Registration successful, Please check you email to verify' })
    }catch(err){
      res.status(400).json({ error: err.message })
    }
}


//Varify Email

export const verifyEmail = async (req, res) =>{
    try{
      const { token } = req.query;
      const user = await User.findOne({ emailVerificationToken: token  });
      if(!user) return res.status(400).json({ message: 'Invalid token' });

      user.isVerified = true;
      user.emailVerificationToken = null;
      await user.save();
      res.json({ message: 'Email verified successfully!' });
    
    }catch(err){

      res.status(500).json({ error: err.message });
    }
}


//login 
export const login = async (req, res) => {
    try{
     const { email, password } = req.body;
     const user = await User.findOne({ email });

     if(!user || !(await user.comparePassword(password))){
      return res.status(400).json({ message: 'Invalid credentials' })
     }

     
     
     if(!user.isVerified){
       return res.status(400).json({ message: 'Please verify your email before logging in. '});
     }

     const accessToken = createAccessToken(user);
     const refreshToken = createRefreshToken(user);
     user.refreshTokens.push(refreshToken);
     await user.save();
     res.json({ accessToken, refreshToken });
    }catch(err){
        res.status(500).josn({ error: err.message})
    };
}

// //Refresh Token
// export const refresh = async (req, res) => {
//  const { token } = req.body;
//  if(!token) return res.status(401).json({ message: 'No token provided' });
//  try{
//    const decoded = jwt.verify(token, process.env.JWT_SECRET);
//    const user = await User.findyById(decoded.id);
//    if(!user || !user.refreshTokens.includes(token))
//         return res.status(403).json({ message: 'Invalid refresh token' });
//    const newAccessToken = createAccessToken(user);
//    res.json({ accessToken: newAccessToken });

//  }catch(err){
//     res.status(403).json({ error: 'Invalid token' });
//  }
// }


// Refresh Token
export const refresh = async (req, res) => {
  const { token } = req.body;
  if (!token) return res.status(401).json({ message: 'No token provided' });

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    const user = await User.findById(decoded.id);
    if (!user || !user.refreshTokens.includes(token))
      return res.status(403).json({ message: 'Invalid refresh token' });

    const newAccessToken = createAccessToken(user);
    res.json({ accessToken: newAccessToken });
  } catch (err) {
    res.status(403).json({ error: 'Invalid token' });
  }
};

//Forgot Password
export const forgotPassword = async (req, res) => {
  const { email } = req.body;
  const user = await User.findOne({ email });
  if (!user) return res.status(404).json({ message: 'User not found' });

  const token = user.generatePasswordResetToken();
  await user.save();

  const resetUrl = `${process.env.FRONTEND_URL}/reset-password?token=${token}`;
  await sendMail(email, 'Password Reset Request', `<p>Click to reset your password: <a href="${resetUrl}">Reset Password</a></p>`);
  res.json({ message: 'Password reset link sent to your email.' });
};

//Reset Password
export const resetPassword = async (req, res) => {
 const { token, password} = req.body
 const user = await User.findOne({ passwordResetToken: token });
 if (!user) return res.status(400).json({ message: 'Invalid token' });

 user.password = password;
 user.passwordResetToken = null;
 await user.save();

 res.json({ message: 'Password has been reset successfully!' });
};