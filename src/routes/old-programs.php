<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

//Get all programs
$app ->get('/api/programs', function(Request $request, Response $response){
 $sql = "SELECT * FROM programs";
 try{
    $db = new db();
    $db = $db->connect();

    $stmt = $db->query($sql);
    $programs = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($programs);
 } catch (PDOException $exception){
     echo '{"error": {"text": '.$exception->getMessage().'}}';
 }
});
 //Get single program
    $app->get('/api/program/{id}',function(Request $request, Response $response){
     $id = $request->getAttribute('id');
     $sql = "SELECT * FROM programs WHERE id = $id";
     try{
         $db = new db();
         $db = $db->connect();

         $stmt = $db->query($sql);
         $program = $stmt->fetchAll(PDO::FETCH_OBJ);
         $db = null;
         echo json_encode($program);

     } catch (PDOException $exception){
         echo '{"error": {"text": '.$exception->getMessage().'}}';
     }
    });

//Add program
$app->post('/api/program/add', function(Request $request, Response $response){
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

    $sql = "INSERT INTO programs (first_name, last_name, phone, email, address, city, state) VALUES
    (:first_name,:last_name,:phone,:email,:address,:city,:state )";

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

        echo '{"notice" : {"text": "Program Added"}';

    } catch (PDOException $exception){
        echo '{"error": {"text": '.$exception->getMessage().'}}';
    }
});

//Update program
$app->put('/api/program/update/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

    $sql = "UPDATE programs SET 
                   first_name = :first_name,
                   last_name  = :last_name,
                   phone      = :phone,
                   email      = :email,
                   address    = :address,
                   city       = :city,
                   state      = :state
            WHERE id = $id";

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

        echo '{"notice" : {"text": "Program Updated"}';

    } catch (PDOException $exception){
        echo '{"error": {"text": '.$exception->getMessage().'}}';
    }
});
//Delete single program
$app->delete('/api/program/delete/{id}',function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "DELETE FROM programs WHERE id = $id";

    try{
        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;

        echo '{"notice" : {"text" : "Program Delete"}}';

    } catch (PDOException $exception){
        echo '{"error": {"text": '.$exception->getMessage().'}}';
    }
});

