<?php
require_once __DIR__ . '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
  {
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $senha = $_POST['senha'];
  $confirmar_senha = $_POST['confirmar_senha'];
}

//Verificação se existe email no BD
$result = pg_query_params(
  $dbconn, 
  "SELECT email FROM usuario WHERE email = $1", 
  [$_POST['email']]
);
$dados = pg_fetch_all($result);

if(!empty($dados))
{
   $msg = "Esse email já está cadastrado! <br>";
}
//Exigência para no mínimo 3 caracteres no nome
if (strlen(($nome)) < 3) {
  $msg = $msg . "O nome deve conter no mínimo 3 caracteres. <br>";
}
//Verificação dos campos de senha
if ($senha !== $confirmar_senha) {
  $msg = $msg . "As senhas não coincidem! <br>";
}
//Exigência para no mínimo 6 caracteres na senha e um número (segurança)
if (strlen($senha) < 6) {
  $msg=$msg . "A senha deve conter mínimo 6 caracteres!<br>";
}
if (!preg_match('/[0-9]/', $senha)) {
  $msg = $msg . "A senha deve conter mínimo um número";
}
//Emitir mensagens de erro
if(isset($msg))
{
  header("Location: ../cadastro.php",true,302);
} else
//Inserir dados no BD
 {
  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
  $result = pg_query_params($dbconn,"INSERT INTO usuario (nome, email , senha_hash, data_cadastro, tipo_usuario ) VALUES ($1, $2, $3, CURRENT_DATE, 'Cliente')",[ $nome , $email ,$senha_hash]);
}
//Redirecionamento para outra página 
header("Location: ../index.php",true,302);

?>
