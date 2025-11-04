<?php
require_once __DIR__ . '/../../config/db.php'; 

session_start();

$result = pg_query_params(
  $dbconn,   
  "SELECT email, senha_hash FROM usuarios WHERE email = $1", 
  [$_POST['email']]
);

$usuario = pg_fetch_assoc($result);

//Verificação , "!empty" se não existir dados no BD
if(!empty($usuario))
{
  //Salvando informações nas variáveis da sessão 
  if(password_verify($_POST["senha"], $usuario['senha_hash']))
  {
    $_SESSION['usuario_nome'] = $_POST["nome"];
    $_SESSION['usuario_email'] = $_POST['email'];
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