<?php
// conexao.php
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'lavelle_perfumes';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit();
}
?>