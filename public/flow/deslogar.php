<?php
    require "../../config/check_session.php";
    session_unset();
    session_destroy();

    header("Location: ../index.php", TRUE, 302);
    exit;
?>