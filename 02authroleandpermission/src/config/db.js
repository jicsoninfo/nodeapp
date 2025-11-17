import mongoose from 'mongoose';

export const connectDB = async () =>{
  try{
    await mongoose.connect(process.env.MONGO_URI);
    console.log('MongoDB Connected');
  }catch(err){
    console.error(err.message);
    process.exit();
  }
};


//-----------------------------------
// async function connectDB() {
//     try {
//         await mongoose.connect(process.env.MONGO_URI);
//     } catch (err) {
//         console.error(err.message);
//         process.exit(1);
//     }
// }

// export { connectDB };
//-------------------------------
// const connectDB = async () => {
//     try {
//         await mongoose.connect(process.env.MONGO_URI);
//     } catch (err) {
//         console.error(err.message);
//         process.exit(1);
//     }
// };

// export default connectDB;
