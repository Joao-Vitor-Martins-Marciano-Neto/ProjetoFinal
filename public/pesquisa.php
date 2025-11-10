<?php
    // Conexão com o db
    require "../config/db.php";
?>


<!DOCTYPE html>
    <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pesquisa</title>
            <link rel="stylesheet" href="../assets/css/public.css">
            <link rel="stylesheet" href="../assets/css/book.css">
        </head>

        <body>
            <!-- Carrega a sessão, Mostra o cabeçalho e abre a tag main -->
            <?php require "../view/header.php"; 
                  require "../config/check_auth.php";?>

            <form method="GET" id="book-search">
                <div id='search-area'>
                    <label for="search">
                        Pesquisar:
                        <input type="text" name="search"  placeholder="Pesquisar" 
                                                          maxlength="50" 
                                                          minlength="2">
                    </label>

                    <button type="submit">
                        <img src="../assets/img/search_icon.png" alt="Buscar">
                    </button>
                </div>
            </form>

            <?php
                // Verifica se há uma pesquisa e exibe os resultados
                if (isset($_GET["search"])) {
                    require "../class/books.php";
                    require "../class/book.php";

                    $search = trim($_GET['search']);
                    if (strlen($search) < 2) {
                        // Termo muito curto — não faz a consulta
                        $result = false;
                    } else {
                        $word = '%' . $search . '%';

                        // Consulta simples por título e autor (case-insensitive)
                        $sql = 'SELECT * FROM livro WHERE titulo ILIKE $1 OR autor ILIKE $1 ORDER BY titulo';
                        $result = pg_query_params($dbconn, $sql, [$word]);

                    }

                    $books = new Books($result);
                    $books->show_all_result();
                }
                
            ?>


            </main>
        </body>
    </html>