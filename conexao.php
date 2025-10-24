<?php
// conexao.php

$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'lavelle_perfumes'; // ou o nome do seu banco

try {
    $con = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>