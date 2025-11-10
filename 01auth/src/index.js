import express from "express";
import dotenv from "dotenv";
import mongoose from "mongoose";

import authRoutes from './routes/auth.js';
import userRoutes from './routes/users.js';
import productRoutes from './routes/products.js';


dotenv.config();
const app = express();
app.use(express.json());



const PORT = process.env.PORT || 5000;

app.get('/', (req, res) => {
    res.json({ "status": "OK", "message": "Welcome to API"});
});

app.use('/api/auth', authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/products', productRoutes);

async function start(){
    try{
        await mongoose.connect(process.env.MONGO_URI);
        //app.listen(PORT);
        app.listen(PORT, () => console.log(`Server listening on port ${PORT}`));
        

    }catch(err){
        console.error('Failed to start server', err);
        process.exit(1);
    }
}
start();



//But wrapping it in an async function start() is useful when you want to do asynchronous setup before starting your server — for example:


//npm start
//npm run dev
//mongod --version
//sudo systemctl start mongod
//sudo systemctl status mongod
//sudo systemctl enable mongod