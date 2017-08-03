<?php

/**
 * Created by PhpStorm.
 * User: Marcelo
 * Date: 8/1/2017
 * Time: 11:26 PM
 */

namespace Controllers\Auth;

class AuthController
{
//Method used to render our view
    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'backup/views/auth/signin.twig');
    }
    //This method actually sign us in
    public function postSignIn($request, $response)
    {

    }
}