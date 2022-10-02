<?php

require 'users_lib.php';

function response($status, $message, $more = null, $http = null) 
{
    if ($http !== null) {
        http_response_code($http);
    }

    exit(json_encode([
        "status" => $status,
        "message" => $message,
        "more" => $more
    ]));
}

function loginCheck() 
{
    if (!isset($_SESSION['user'])) {
        response(0, "Faça o login primeiro", null, 403);
    }
}

if (isset($_POST['request'])) {
    switch ($_POST['request']) {
        case "save": 
            loginCheck();
            $pass = $usr->saveUser(
                $_POST['email'], $_POST['pass'],
                isset($_POST['id']) ? $_POST['id'] : null
            );
            response($pass, $pass ? "OK" : $usr->error);
            break;      
        case "delete": 
            loginCheck();
            $pass = $usr->deleteUser($_POST['id']);
            response($pass, $pass ? "OK" : $usr->error);
            break;
        case "get": 
            loginCheck();
            response(true, "OK", $usr->getUser($_POST['id']));
            break;
        case "in":
            if (isset($_SESSION['user'])) {
                response(true, "OK");
            }
            $pass = $usr->verifyUser($_POST['email'], $_POST['pass']);
            response($pass, $pass ? "OK" : "OPS! Email ou senha inválidos");
            break;
        case "out":
            unset($_SESSION['user']);
            response(true, "OK");
            break;  
        default:
            response(false, "Requisição inválida", null, null, 400);
            break;
    }
}