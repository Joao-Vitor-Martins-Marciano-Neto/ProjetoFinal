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
                if (isset($_GET["search"]) && 1 == 0) {
                    require "../class/books.php";
                    require "../class/book.php";

                    $word = '%' . $_GET["search"] . '%';

                    // Aqui preciso usar STRING_AGG depois
                    $result = pg_query_params($dbconn, 'SELECT * FROM books where title ILIKE $1', [$word]);

                    $books = new Books($result);
                    $books->show_all_result();
                }

                // Criando livros de exemplo para testar
                require "../class/book.php";
                
                $book1 = new Book(2, "Harry Potter", "42084-424", "Autor I", "Livro para testes I;", TRUE, "HARRY_POTTER.png");
                
                $book1->show();

                $book2 = new Book(2, "Cristalografia: Cristais e estruturas cristalinas", "420131-322", "Autor II, Autor III", "Livro para testes;", FALSE, "CRISTALOGRAFIA.png");
                
                $book2->show();
            ?>


            </main>
        </body>
    </html>