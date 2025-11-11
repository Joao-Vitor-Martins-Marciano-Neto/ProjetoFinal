<?php
require_once __DIR__ . '/../../config/db.php'; // Fixed: Added missing '/' for correct path concatenation 

 session_start();

$result = pg_query_params(
  $dbconn,   
  "SELECT id_usuario, nome, email, senha_hash FROM usuario WHERE email = $1", 
  [$_POST['email']]
);

$usuario = pg_fetch_assoc($result);

 //Verificação , "!empty" se não existir dados no BD
if(!empty($usuario) )
{
   //Salvando informações nas variáveis da sessão 
 if(password_verify($_POST["senha"],$usuario['senha_hash']))
 {
    $_SESSION['usuario_id']=$usuario['id_usuario'];
    $_SESSION['usuario_nome']=$usuario['nome'];
    $_SESSION['usuario_email']=$usuario['email'];
    $_SESSION['logado']=true;
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
 exit; // Fixed: Added missing exit after header redirect
?>