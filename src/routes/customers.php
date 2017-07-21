<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

//Get all customers
$app ->get('/api/customers', function(Request $request, Response $response){
 $sql = "SELECT * FROM customers";
 try{
    $db = new db();
    $db = $db->connect();

    $stmt = $db->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($customers);
 } catch (PDOException $exception){
     echo '{"error": {"text": '.$exception->getMessage().'}}';
 }
});
 //Get single customer
    $app->get('/api/customer/{id}',function(Request $request, Response $response){
     $id = $request->getAttribute('id');
     $sql = "SELECT * FROM customers WHERE id = $id";
     try{
         $db = new db();
         $db = $db->connect();

         $stmt = $db->query($sql);
         $customer = $stmt->fetchAll(PDO::FETCH_OBJ);
         $db = null;
         echo json_encode($customer);

     } catch (PDOException $exception){
         echo '{"error": {"text": '.$exception->getMessage().'}}';
     }
    });
//Add customer
$app->post('/api/customer/add', function(Response $response, Request $request){
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

    $sql = "INSERT INTO customers (first_name, last_name, phone, email, address, city, state) 
            VALUES (:first_name,:last_name,:phone,:email,:address,:city,:state )";

    try{
        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state',$state);

        $stmt->execute();

        echo '{"notice" : {"text": "Customer Added"}';

    } catch (PDOException $exception){
        echo '{"error": {"text": '.$exception->getMessage().'}}';
    }
});


