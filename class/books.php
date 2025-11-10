<?php
    require "./book.php";
    require_once __DIR__ . '/../config/db.php'; // Fixed: Added missing '/' for correct path concatenation
    class Books {
        public $list = [];

        public function __construct($sql_result) {
            global $dbconn;
            $sql_result = pg_fetch_all($sql_result);

            foreach ($sql_result as $book) {
                // Buscar id_livro baseado no ISBN
                $livro_result = pg_query_params(
                    $dbconn,
                    "SELECT id_livro FROM livro WHERE isbn = $1",
                    [$book["isbn"]]
                );
                $livro = pg_fetch_assoc($livro_result);
                
                // Verificar disponibilidade do livro
                $disponivel = empty(pg_fetch_all(
                    pg_query_params(
                        $dbconn,
                        "SELECT id_emprestimo FROM emprestimo WHERE id_livro = $1 AND (status_emprestimo = 'Ativo' OR status_emprestimo IS NULL OR status_emprestimo = '')",
                        [$livro["id_livro"]]
                    )
                ));
                // $id, $title, $isbn, $authors, $description, $disponible, $img_path;
                $this->list[] = new Book($book["id_livro"],
                                         $book["titulo"], 
                                         $book["isbn"], 
                                         $book["autor"], 
                                         $book["titulo"],
                                         $disponivel,
                                         $book["isbn"] . ".jpg");
            }
        }

        public function show_all_result() {
            echo "<div class='books'>";
                foreach ($this->list as $book) {
                    $book->show();
                }
            echo "</div>";
        }
    }