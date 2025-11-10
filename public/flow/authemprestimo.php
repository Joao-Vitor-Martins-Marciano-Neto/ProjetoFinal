<?php
require_once __DIR__ . '../../config/db.php';

//Login...verificar
//Pegar ISBN

session_start();

if(isset($_SESSION['logado'])) 
{
  $isbn = $_GET['emprestimo'];

  // Verificar disponibilidade - buscar id_livro baseado no ISBN
  $resultado_livro = pg_query_params(
        $dbconn,
        "SELECT id_livro FROM livro WHERE isbn = $1",
        [$isbn]
  );
  
  $livro = pg_fetch_assoc($resultado_livro);
  
  if(!empty($livro)) 
  {
    // Verificar se já existe empréstimo ativo para este livro
    $resultado_emprestimo = pg_query_params(
          $dbconn,
          "SELECT id_emprestimo FROM emprestimo WHERE id_livro = $1 AND (status_emprestimo = 'Ativo' OR status_emprestimo IS NULL OR status_emprestimo = '')",
          [$livro['id_livro']]
    );

    if(empty(pg_fetch_all($resultado_emprestimo))) 
    {
      // Adicionar livro na tabela de empréstimo
      $resultado_insert = pg_query_params(
        $dbconn,
        "INSERT INTO emprestimo (id_usuario, id_livro, data_emprestimo, data_prevista_devolucao, status_emprestimo) VALUES ($1, $2, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days', 'Ativo')",
        [$_SESSION['usuario_id'], $livro['id_livro']]
      );
      
      if($resultado_insert) {
        echo "Empréstimo realizado com sucesso!";
      } else {
        echo "Erro ao realizar empréstimo";
      }
   
    } else {
       echo "Livro indisponível";
    }
  } else {
    echo "Livro não encontrado";
  }

} else {
  header('Location: ../login.php');
}
?>