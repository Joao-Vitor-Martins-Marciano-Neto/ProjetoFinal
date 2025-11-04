<?php
require_once __DIR__ . '/../../config/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
  // Validar se os campos foram enviados
  if(!isset($_POST['nome']) || !isset($_POST['email']) || !isset($_POST['senha']) || !isset($_POST['confirmar_senha'])) {
    $_SESSION['erro'] = "Todos os campos são obrigatórios!";
    header("Location: ../cadastro.php", true, 302);
    exit;
  }
  
  $nome = trim($_POST['nome']);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $senha = $_POST['senha'];
  $confirmar_senha = $_POST['confirmar_senha'];
  
  $msg = "";
  
  // Validar formato do email
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $msg = "Email inválido! <br>";
  }
  
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
    
    if($result) {
      //Buscar o ID do usuário recém criado
      $user_result = pg_query_params($dbconn, "SELECT id_usuario, nome, email FROM usuarios WHERE email = $1", [$email]);
      $user = pg_fetch_assoc($user_result);
      
      if($user) {
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_email'] = $user['email'];
        $_SESSION['logado'] = true;
      }
    }
    
    //Redirecionamento para outra página 
    header("Location: ../index.php");
    exit;
  }
}
?>
