<?php

    class Book {
        public $id, $title, $isbn, $authors = '', $description, $disponible, $img_path;
        
        public function __construct(int $ID, string $TITLE, string $ISBN, string $AUTHORS, string $DESCRIPTION, bool $DISPONIBLE, string $IMG_PATH) {
            $this->id = $ID;
            $this->title = $TITLE;
            $this->isbn = $ISBN;
            $this->authors = $AUTHORS;
            $this->description = $DESCRIPTION;
            $this->disponible = $DISPONIBLE;
            $this->img_path = $IMG_PATH;
        }

        public function show() {
            $disponible_str = $this->disponible ? 'Disponível' : 'Emprestado';

            echo "<div class='book'>";
                echo "<div class='book-principal'>";
                    echo "<img class='book-img' src='../assets/book_img/" . htmlspecialchars($this->img_path) . "'>";
                    echo "<div class='book-info'>";
                        echo "<p class='book-desc'>" . htmlspecialchars($this->title) ."<br>" . htmlspecialchars($this->description) . "</p>";
                        echo "<div class='book-extra-info'>";
                            echo "<p class='book-isbn'>ISBN: " . htmlspecialchars($this->isbn) . "</p>";
                            echo "<p class='book-authors'> Autores: " . htmlspecialchars($this->authors) . "</p>";
                        echo "</div>";
                    echo "</div>"; 
                echo "</div>";
                if ($disponible_str == 'Disponível'){
                    $extra_class = "green";
                    $opt = 1;
                } else {
                    $extra_class = "red";
                }
                echo "<div class='book-disponible-div $extra_class'>";
                    if (isset($opt)) {
                        echo "<form method='GET' action='flow/authemprestimo.php' class='book-loan'>";
                            echo "<input type='text' name='emprestimo' value='" . $this->isbn . "' hidden>";
                            echo "<button type='submit'>Empréstimo</button>";
                        echo "</form>";
                    } else {
                        echo "<p class='book-disponible'>" . $disponible_str . "</p>";
                    }
                echo "</div>";
            echo "</div>";
        }
    }
?>