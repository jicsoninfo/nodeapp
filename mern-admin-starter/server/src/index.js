import express from 'express';
import mongoose from 'mongoose';
import dotenv from 'dotenv';
import cors from 'cors';
import helmet from 'helmet';
import morgan from 'morgan';

import authRoutes from './routes/auth.js';
import userRoutes from './routes/users.js';
import productRoutes from './routes/products.js';

dotenv.config();
const app = express();

app.use(express.json());
app.use(helmet());
app.use(morgan('dev'));

const allowedOrigin = process.env.CLIENT_ORIGIN || '*';
app.use(cors({ origin: allowedOrigin, credentials: true }));

app.get('/', (req, res) => {
  res.json({ status: 'ok', message: 'MERN Admin API' });
});

app.use('/api/auth', authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/products', productRoutes);

const PORT = process.env.PORT || 5000;

async function start() {
  try {
    await mongoose.connect(process.env.MONGO_URI);
    app.listen(PORT, () => console.log(`Server listening on port ${PORT}`));
  } catch (err) {
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

//curl -X POST -H "Content-Type: application/json" -d '{"email":"testtwo@test.com","password":"admin@123"}' http://localhost:5050/api/auth/login

/*

//quicksheet

  public function get_part_details($token = null, $part_name = null)
  {
    $this->currentLang();
    // echo $part_name;
    // exit;
    $currentEstimate = $this->checkValidEstimate($token);
    $userId = $this->userData['user_id'];
    $dataHandler = array(
      'user_id' => $userId,
      'estimate_id' => $currentEstimate->estimate_id,
      'part_name' => urldecode($part_name)
    );

    // Make the API call (example)

    //  $test_data = array(
    //                   "email" => "testtwo@test.com",
    //                   "password" => "admin@123"
    //               );

    // $apiResponse = $this->call_api($test_data);
    // echo "<pre>"; print_r($apiResponse); die();


    $vehiclePartsDet = $this->Muser->get_part_details($dataHandler);

    if (isset($vehiclePartsDet) && !empty($vehiclePartsDet)) {
      $vehicleName = $this->Muser->getVehcileName($dataHandler);
      $data['companyData'] = $this->companyData;
      $data['sessionData'] = $this->sessionData;
      $data['userData'] = $this->userData;
      $data['pageData'] = $this->Common->get_page_data(8);
      $data['previous_page'] = base_url('estimate/preliminary-estimates/' . $token);
      $data['token'] = $token;
      $data['vehicle_name'] = $vehicleName['vehicle_identity_name'];
      $data1['data'] = $vehiclePartsDet;
      $data['part_name'] = urldecode($part_name);

      $getnew_data = [];
      foreach ($data1['data'] as $newdata) {
        $newdata['part_name'] = $this->lang_translate($newdata['part_name'], $userId);
        $newdata['list_price'] = $newdata['list_price']*$this->get_currency_data('currency_usd_value', $this->userData['user_id']);
        $newdata['default_currency'] = $this->get_currency_data('default_currency_symbol', $this->userData['user_id']);
        $data['data'][] = $newdata;
      }

      // $data['getnew_data'];

      // echo"<pre>";
      //   print_r($data['data']);
      //   die;
      $this->load->view('template/header', $data);
      $this->load->view('template/menu');
      $this->load->view('estimate/part_details');
      $this->load->view('template/footer');
    } else {
      $this->session->set_flashdata('error', $this->lang->line('oops_something_went_wrong'));
      redirect($_SERVER['HTTP_REFERER']);
    }
  }



 private function call_api($dataHandler)
    {
        $url = 'http://localhost:5050/api/auth/login'; 

        $ch = curl_init($url);

        $jsonData = json_encode($dataHandler);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            'Accept: application/json'        
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
         curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
    
       
        if (curl_errno($ch)) {
            log_message('error', 'cURL Error: ' . curl_error($ch));
            return false;
        }
       
        // $info = curl_getinfo($ch);
        // echo "<pre>"; 
        // print_r($info); 
        // echo "<br>Response:<br>";
        // print_r($response);
        // die(); 

        curl_close($ch);
        $decodedResponse = json_decode($response, true);
        return $decodedResponse;
    }
    
    
================================================================================

    // Define your functions
async function createUser(req, res) {
    // logic for creating a user
}

async function listUser(req, res) {
    // logic for listing users
}

async function getUser(req, res) {
    // logic for getting a single user
}

// Export all functions as named exports
export { createUser, listUser, getUser };

// Optional: export a single object as default for convenience
export default { createUser, listUser, getUser };
How to import:
1. Import individual functions (named import):

javascript
Copy code
import { createUser, listUser } from './userController.js';

createUser(req, res);
listUser(req, res);
2. Import the entire object (default import):

javascript
Copy code
import userController from './userController.js';

userController.createUser(req, res);
userController.listUser(req, res);
    ===================================================================

    localhost:5050/api/auth/register //post
    {
    "name": "testtwo",
    "email": "testtwo@test.com",
    "password": "admin@123"
}


localhost:5050/api/auth/login //post

{
    "email": "admin@example.com",
    "password": "Admin@123"
}

localhost:5050/api/users/ //get send bearer token get all users

localhost:5050/api/users/6908ac2384534cc83c53bb69 // get and get single user data with id


localhost:5050/api/users/6908ac2384534cc83c53bb69 // delete and delete the user data



localhost:5050/api/users // post method for create new user
{
    "name": "testtwo",
    "email": "testtwo@test.com",
    "password": "admin@123"
}



localhost:5050/api/users/690c68cef8be2b2ae9daa477 //put mehtod for update
{
    "name": "testtwoTWO",
    "email": "testtwo@test.com",
    "password": "admin@123"
}
  

//===================================================

localhost:5050/api/products //post method for creating product
{
    "name": "Test Two",
    "sku": "SDFSDF",
    "price": 800,
    "stock": 50,
    "description": "This is test data."
}



localhost:5050/api/products //Get method for get all product list

localhost:5050/api/products/690c85094880e9e5e13d0c58 // put method for update the product list


localhost:5050/api/products/690c85094880e9e5e13d0c58 // delete method for delete the product


"scripts": {
  "seed-admin": "node path/to/seedAdmin.js"
}

npm run seed-admin


    */