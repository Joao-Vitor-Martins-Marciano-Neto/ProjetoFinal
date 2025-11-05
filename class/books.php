<?php
    require "./book.php";

    class Books {
        public $list = [];

        public function __construct($sql_result) {
            $sql_result = pg_fetch_all($sql_result);

            foreach ($sql_result as $book) {
                // $id, $title, $isbn, $authors, $description, $disponible, $img_path;
                $this->list[] = new Book($book["id"],
                                         $book["title"], 
                                         $book["isbn"], 
                                         $book["authors"], 
                                         $book["description"],
                                         $book["disponible"],
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