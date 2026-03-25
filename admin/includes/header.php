<?php
require_once 'auth.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Colegio Diego Portales</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a3a5c;
            --primary-light: #2c5282;
            --secondary: #e53e3e;
            --bg: #f7fafc;
            --surface: #ffffff;
            --text: #2d3748;
            --nav-width: 250px;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--nav-width);
            background-color: var(--primary);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
        }
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header img {
            height: 50px;
            margin-bottom: 10px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid var(--secondary);
        }
        .sidebar-menu li a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: var(--nav-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background-color: var(--surface);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .content-wrapper {
            padding: 2rem;
            flex: 1;
        }
        .card {
            background: var(--surface);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-family: inherit;
            transition: opacity 0.2s;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        .btn-danger {
            background-color: var(--secondary);
            color: white;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
            font-weight: 600;
            color: #4a5568;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-family: inherit;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .alert-success { background-color: #c6f6d5; color: #22543d; }
        .alert-danger { background-color: #fed7d7; color: #822727; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../images/logo-original.png" alt="Logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li>
                <a href="comunicados.php" class="<?= $current_page == 'comunicados.php' ? 'active' : '' ?>">
                    <i class="fas fa-bullhorn"></i> Comunicados
                </a>
            </li>
            <li>
                <a href="documentos.php" class="<?= $current_page == 'documentos.php' ? 'active' : '' ?>">
                    <i class="fas fa-file-pdf"></i> Reg. y Guías
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <h2>Gestor de Contenido</h2>
            <div class="user-info">
                <span><i class="fas fa-user-circle"></i> Hola, <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </header>
        <div class="content-wrapper">
