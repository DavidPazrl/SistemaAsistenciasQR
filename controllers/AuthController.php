<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    public function login($usuario, $password){
        $database = new Database();
        $db = $database->getConnection();

        $usuarioModel = new Usuario($db);
        $user = $usuarioModel->login($usuario, $password);

        if ($user) {
            $_SESSION['idPersonal'] = $user['idPersonal'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            if ($user['rol'] === 'Admin'){
                header("Location: ../views/admin/dashboard.php");
            } else {
                header("Location: ../views/encargado/dashboard.php");
            }
            exit();
        } else {
            header("Location: ../views/auth/login.php?error=1");
            exit();
        }
    }

    public function logout(){
        session_destroy();
        header("Location: ../views/auth/login.php");
        exit();
    }
}

// Manejo del formulario 

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $auth = new AuthController();
    $auth->login($usuario, $password);
}