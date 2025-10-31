<?php

if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - LAVELLE Perfumes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="header-left">
                <h1>LAVELLE Admin</h1>
                <span>Painel Administrativo</span>
            </div>
            <div class="header-right">
              
                
            </div>
        </header>