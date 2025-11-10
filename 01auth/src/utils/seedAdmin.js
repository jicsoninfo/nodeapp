import dotenv from 'dotenv';
import mongoose from 'mongoose';
import bcrypt from 'bcryptjs';
import User from '../models/User.js';

dotenv.config();

async function run(){
    try{
      await mongoose.connect(process.env.MONGO_URI);
      const email = process.env.ADMIN_EMAIL;
      const password = process.env.ADMIN_PASSWORD;

      if(!email || !password){
        console.error('ADMIN_EMAIL and ADMIN_PASSWORD must be set in .env');
        process.exit(1);
      }

      let user = await User.findOne({ email });

      if(user){
        console.log('Admin already exists:', email);
      }else{
          const passwordHash = await bcrypt.hash(password, 10);
          user = await User.create({ name: 'Admin', email, passwordHash, role: 'admin' });
          console.log('Admin created:', email);
        }
    
    }catch (e){
      console.error(e);
    }finally {
      await mongoose.disconnect();
      process.exit(0);
    }
}

run();