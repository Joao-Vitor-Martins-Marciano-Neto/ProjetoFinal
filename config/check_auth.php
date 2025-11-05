<?php
    if (!isset($_SESSION['logado'])) {
        echo "<script>
                alert('Você deve estar logado para acessar esse recurso');
                window.location.href = 'index.php';
            </script>";
            exit;
    }
