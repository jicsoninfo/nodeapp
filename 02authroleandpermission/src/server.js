//const express = require('express'); //old pattern no need to add "type": "module", in json file
import express from 'express'; // new pattern need to add "type": "module", in json file
import dotenv from 'dotenv'; // for env file
import { connectDB } from './config/db.js';

import authRoutes from './routes/auth.routes.js';

dotenv.config();
//connectDB();



const app = express();
//app.listen(5000, () => console.log('server is running'));
//app.listen(process.env.PORT, () => console.log(`server is running on port ${process.env.PORT}`));

//app.use(express.json());

app.use('/api/auth', authRoutes);

async function start(){
    try{
      await connectDB();
      app.listen(process.env.PORT, () => console.log(`server is running on port ${process.env.PORT}`))
    }catch(err){
        console.log('Failed to start server', err);
        process.exit(1);
    }
}

start();



//npm install express mongoose bcrypt jsonwebtoken accesscontrol dotenv cors cookie-parser helmet express-rate-limit
