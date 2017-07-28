<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

//Get all programs
$app ->get('/api/allPrograms', function(Request $request, Response $response){
    $sql = "SELECT `program_name` FROM program";
    try{
        $db = new db();
        $db = $db->connect();

        $stmt = $db->query($sql);
        $programs = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(var_export($programs, true));
        return $response;

        //echo json_encode($programs);
    } catch (PDOException $exception){
        echo '{"error": {"text": '.$exception->getMessage().'}}';
    }
});