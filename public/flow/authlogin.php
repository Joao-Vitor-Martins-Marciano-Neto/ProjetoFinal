<?php
require_once __DIR__ . '/../../config/db.php'; 

session_start();

// Validar se os campos foram enviados
if(!isset($_POST['email']) || !isset($_POST['senha'])) {
  $_SESSION['erro'] = "Email e senha são obrigatórios!";
  header('Location: ../login.php');
  exit;
}

// Sanitizar entrada
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Validar formato do email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['erro'] = "Email inválido!";
  header('Location: ../login.php');
  exit;
}

$result = pg_query_params(
  $dbconn,   
  "SELECT id_usuario, nome, email, senha_hash FROM usuarios WHERE email = $1", 
  [$email]
);

$usuario = pg_fetch_assoc($result);

//Verificação , "!empty" se não existir dados no BD
if(!empty($usuario))
{
  //Salvando informações nas variáveis da sessão 
  if(password_verify($_POST["senha"], $usuario['senha_hash']))
  {
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['logado'] = true;
    header('Location: ../index.php');
    exit;
  } 
  else 
  {
    $_SESSION['erro'] = "Email ou senha incorreto!";
    header('Location: ../login.php');
    exit;
  }
}

//Redirecionamento para outra página 
$_SESSION['erro'] = "Email não cadastrado!";
header('Location: ../login.php');
?>