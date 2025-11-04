<?php
require_once __DIR__ . '/../../config/db.php';

session_start();

// Verificar se o usuário está logado
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
  $_SESSION['erro'] = "Você precisa estar logado para realizar um empréstimo!";
  header('Location: ../login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id_livro = $_POST['id_livro'];
  $id_usuario = $_SESSION['usuario_id'];
  
  // Verificar disponibilidade do livro (estoque > 0)
  $result = pg_query_params(
    $dbconn,
    "SELECT id_livro, titulo, estoque FROM Livro WHERE id_livro = $1",
    [$id_livro]
  );
  
  $livro = pg_fetch_assoc($result);
  
  if(!$livro) {
    $_SESSION['erro'] = "Livro não encontrado!";
    header('Location: ../emprestimos.php');
    exit;
  }
  
  if($livro['estoque'] <= 0) {
    $_SESSION['erro'] = "Livro indisponível no momento!";
    header('Location: ../emprestimos.php');
    exit;
  }
  
  // Calcular data prevista de devolução (15 dias)
  $data_emprestimo = date('Y-m-d');
  $data_prevista_devolucao = date('Y-m-d', strtotime('+15 days'));
  
  // Iniciar transação
  pg_query($dbconn, "BEGIN");
  
  try {
    // Inserir empréstimo
    $result = pg_query_params(
      $dbconn,
      "INSERT INTO Emprestimo (id_usuario, id_livro, data_emprestimo, data_prevista_devolucao, status_emprestimo) VALUES ($1, $2, $3, $4, $5)",
      [$id_usuario, $id_livro, $data_emprestimo, $data_prevista_devolucao, 'Ativo']
    );
    
    if(!$result) {
      throw new Exception("Erro ao registrar empréstimo");
    }
    
    // Decrementar estoque
    $result = pg_query_params(
      $dbconn,
      "UPDATE Livro SET estoque = estoque - 1 WHERE id_livro = $1",
      [$id_livro]
    );
    
    if(!$result) {
      throw new Exception("Erro ao atualizar estoque");
    }
    
    // Confirmar transação
    pg_query($dbconn, "COMMIT");
    
    $_SESSION['sucesso'] = "Empréstimo realizado com sucesso! Data de devolução: " . date('d/m/Y', strtotime($data_prevista_devolucao));
    header('Location: ../index.php');
    exit;
    
  } catch(Exception $e) {
    // Reverter transação em caso de erro
    pg_query($dbconn, "ROLLBACK");
    $_SESSION['erro'] = "Erro ao realizar empréstimo: " . $e->getMessage();
    header('Location: ../emprestimos.php');
    exit;
  }
}
?>
