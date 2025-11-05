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
        "SELECT disponibilidade FROM livros as l WHERE l.isbn = $1",
        [$isbn]
  );

  if($resultado[0]['disponibilidade'] == TRUE) 
  {
    // Alterar disponibilidade
    pg_exec($dbconn,"ALTER TABLE livros as l SET disponibilidade=FALSE WHERE l.isbn = $1",
    [$isbn]);
 
    //Código abaixo deve ser corrigido
    pg_exec($dbconn,"ALTER TABLE emprestimo as e SET id_usuario=$1 WHERE e.isbn=$2", []);
  } else {
     echo "Livro indisponível";
  }

} 
?>