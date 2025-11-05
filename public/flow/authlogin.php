<?php
require_once __DIR__ . '../../config/db.php'; 

 session_start();

$result = pg_query_params(
  $dbconn,   
  "SELECT email, senha_hash FROM usuario WHERE email = $1", 
  [$_POST['email']]
);


 //Verificação , "!empty" se não existir dados no BD
if(!empty($result) )
{
   //Salvando informações nas variáveis da sessão 
 if(password_verify($_POST["senha"],$result['senha']))
 {
    $_SESSION['usuario_nome']=$_POST["nome"];
    $_SESSION['usuario_email']=$_POST['email'];
    $_SESSION['logado']=true;
    header('Location: ../index.php');
    exit;
 } 

 else 
 {
    $_SESSION['erro'] = "Email ou senha incorreto!";
    header('Location: ../login.php');
 }


}

//Redirecionamento para outra página 
 $_SESSION['erro'] = "Email não cadastrado!";
 header('Location: ../login.php');
?>