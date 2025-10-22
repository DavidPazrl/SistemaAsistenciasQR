<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';
require_once ROOT . 'models/Usuario.php';
require_once ROOT . 'config/database.php';

class AuthController {
    public function login($usuario, $password){
        $database = new Database();
        $db = $database->getConnection();

        $usuarioModel = new Usuario($db);
        $user = $usuarioModel->login($usuario, $password);

        if ($user) {
            $_SESSION['idPersonal'] = $user['idPersonal'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = strtolower($user['rol']);

            if ($user['rol'] === 'admin'){
                header("Location: " . BASE_URL . "views/admin/dashboard.php");
            } else {
                header("Location: " . BASE_URL . "views/encargado/dashboard.php");
            }
            exit();
        } else {
            header("Location: " . BASE_URL . "views/auth/login.php?error=1");
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