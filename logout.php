<?php
// logout.php - CORRIGIDO
session_start();
session_destroy();
header('Location: index.php');
exit();
?>