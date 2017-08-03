<?php
/**
 * Created by PhpStorm.
 * User: Marcelo
 * Date: 8/2/2017
 * Time: 12:39 AM
 */

$app->get('/home', function($request, $response){
    return $this->view->render($response, 'home.twig');
});