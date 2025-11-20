//📧 Step 3: Mail Utility (utils/mail.js)
import nodemailer from 'nodemailer';
import dotenv from 'dotenv';
dotenv.config();

const transporter = nodemailer.createTransport({
  host: 'smtp.gmail.com',
  port: 587,
  secure: false,
  auth: {
    user: process.env.MAIL_USER,
    pass: process.env.MAIL_PASS
  }
});

export const sendMail = async (to, subject, html) => {
  await transporter.sendMail({
    from: `"My App" <${process.env.MAIL_USER}>`,
    to,
    subject,
    html
  });
};