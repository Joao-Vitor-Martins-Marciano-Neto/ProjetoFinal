<?php
// Arquivo para verificar se o usuário está logado
session_start();

// Verificar se a sessão 'logado' existe e é verdadeira
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
  // Se não estiver logado, redirecionar para a página de login
  header('Location: /public/login.php');
  exit;
}
?>
