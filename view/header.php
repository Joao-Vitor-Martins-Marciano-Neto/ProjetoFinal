<?php
    require "../config/check_session.php";

    $project_name = "Biblioteca";

    
    echo "<header>";
        // Mostra a logo e o título da página
        echo "<div id='header-info'>";
            echo "<a href='index.php'>";
                echo "<img src='../assets/img/header_logo.png' id='header-logo' alt='Logo da biblioteca'>";
                echo "<h1 id='header-title'>" . $project_name . "</h1>";
            echo "</a>";
        echo "</div>";

        echo "<div id='header-links'>";
            echo "<ul id='links'>";

                // Se o usuário não está logado, mostra aba de cadastro e login
                if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== TRUE) {
                    echo  "<li class='link'>
                            <a href='cadastro.php'> Cadastro </a>
                        </li>";

                        
                    echo "<li class='link'>
                            <a href='login.php'> Login </a>
                        </li>";
                
                // Se o usuário está logado, mostra a pesquisa, empréstimo e permite deslogar
                } else {
                    echo "<li class='link'>
                        <a href='pesquisa.php' class='search-link'> Pesquisar </a>
                    </li>";

                    echo "<li class='link'>
                        <a href='emprestimos.php' class='loan-link'> Empréstimos </a>
                    </li>";

                    echo "<li class='link white'>
                        <a href='flow/deslogar.php'> Deslogar </a>
                    </li>";
                }
            echo "</ul>";
        echo "</div>";
    echo "</header>";


                
    echo "<main>";