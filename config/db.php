<?php

//DESATIVAR APÓS CRIAR O DB

  if (FALSE) {
  // ========== Config PostgreSQL ============
  $_DB['host'] = 'localhost';    // Servidor PostgreSQL
  $_DB['port'] = '5432';       // Porta padrão do PostgreSQL
  $_DB['user'] = 'postgres';     // Usuário PostgreSQL
  $_DB['password'] = 'postgres';    // Senha PostgreSQL
  $_DB['database'] = 'biblioteca'; // Banco de dados PostgreSQL (com espaços)
  // ========================================

  try {
    // String de conexão para PostgreSQL - versão correta para nomes com espaços
    $conn_string = "host='{$_DB['host']}' port='{$_DB['port']}' dbname='{$_DB['database']}' user='{$_DB['user']}' password='{$_DB['password']}'";
    
    // Estabelece a conexão
    $dbconn = pg_connect($conn_string);
    
    // Verifica se a conexão foi bem sucedida
    if (!$dbconn) {
      throw new Exception("Falha na conexão com o PostgreSQL. Verifique: ".pg_last_error());
    }
    
  // echo "Conexão com PostgreSQL estabelecida com sucesso!";
    
  } catch (Exception $e) {
    error_log($e->getMessage());
    exit('Erro na conexão: <br>' . $e->getMessage());
  }
}
?>
