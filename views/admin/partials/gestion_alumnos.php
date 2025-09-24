<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        include __DIR__ . '/../alumnos/index.php';
        break;
    case 'index':
}
    