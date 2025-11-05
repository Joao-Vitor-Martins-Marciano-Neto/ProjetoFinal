<?php
require_once __DIR__ . '../../config/db.php';

//Login...verificar
//Pegar ISBN

session_start();

if(isset($_SESSION['logado'])) 
{
  $isbn = $_GET['ISBN'];

  // Verificar disponibilidade
  $resultado=pg_query_params(
        $dbconn,
        "SELECT FROM emprestimo as E WHERE E.isbn = $1",
        [$isbn]
  );

  if(empty(pg_fetch_all($resultado))) 
  {
    // Alterar disponibilidade
    //Adicionar livro na tabela de empréstimo com base no código SQL
    pg_exec($dbconn,"INSERT INTO emprestimo (id_usuario, isbn, data_emprestimo, data_prevista_devolucao) VALUES ($1, $2, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days')",
    [$_SESSION['usuario_id'], $isbn]);
 
  } else {
     echo "Livro indisponível";
  }

} 
?>