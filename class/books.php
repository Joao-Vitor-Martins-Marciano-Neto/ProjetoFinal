<?php
    require "./book.php";
    require_once __DIR__ . '../config/db.php';
    class Books {
        public $list = [];

        public function __construct($sql_result) {
            global $dbconn;
            $sql_result = pg_fetch_all($sql_result);

            foreach ($sql_result as $book) {
                $disponivel = empty(pg_fetch_all(
                    pg_query_params(
                        $dbconn,
                        "SELECT FROM emprestimo as E WHERE E.isbn = $1",
                        [$book["isbn"]]
                    )
                ));
                // $id, $title, $isbn, $authors, $description, $disponible, $img_path;
                $this->list[] = new Book($book["id"],
                                         $book["title"], 
                                         $book["isbn"], 
                                         $book["authors"], 
                                         $book["description"],
                                         $disponivel,
                                         $book["img_path"]);
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