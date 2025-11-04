<?php
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $senha = $_POST['senha'];
  $confirmar_senha = $_POST['confirmar_senha'];
  
  $msg = "";
  
  //Verificação se existe email no BD
  $result = pg_query_params(
    $dbconn, 
    "SELECT nome FROM usuarios WHERE email = $1", 
    [$email]
  );
  $dados = pg_fetch_all($result);
  
  if(!empty($dados))
  {
    $msg = "Esse email já está cadastrado! <br>";
  }
  
  //Exigência para no mínimo 3 caracteres no nome
  if (strlen($nome) < 3) {
    $msg = $msg . "O nome deve conter no mínimo 3 caracteres. <br>";
  }
  
  //Verificação dos campos de senha
  if ($senha !== $confirmar_senha) {
    $msg = $msg . "As senhas não coincidem! <br>";
  }
  
  //Exigência para no mínimo 6 caracteres na senha e um número (segurança)
  if (strlen($senha) < 6) {
    $msg = $msg . "A senha deve conter mínimo 6 caracteres!<br>";
  }
  
  if (!preg_match('/[0-9]/', $senha)) {
    $msg = $msg . "A senha deve conter mínimo um número";
  }
  
  //Emitir mensagens de erro
  if(!empty($msg))
  {
    session_start();
    $_SESSION['erro'] = $msg;
    header("Location: ../cadastro.php", true, 302);
    exit;
  }
  else
  {
    //Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    //Inserir dados no BD
    $result = pg_query_params($dbconn, "INSERT INTO usuarios (nome, email, senha_hash) VALUES ($1, $2, $3)", [$nome, $email, $senha_hash]);
    
    //Redirecionamento para outra página 
    header("Location: ../index.php");
    exit;
  }
}
?>
