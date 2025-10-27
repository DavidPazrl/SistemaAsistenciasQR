<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/proyectos/SistemaAsistenciasQR/config.php';

session_start();
session_unset();
session_destroy();

header("Location: " . BASE_URL . "views/auth/login.php");
exit();
