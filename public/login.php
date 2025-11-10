<!DOCTYPE html>
    <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
            <link rel="stylesheet" href="../assets/css/public.css">
            <link rel="stylesheet" href="../assets/css/login.css">
        </head>

        <body>
            <!-- Carrega a sessão, Mostra o cabeçalho e abre a tag main -->
            <?php require "../view/header.php"; ?>

            <h1>Login</h1>
            
                <?php if(isset($_SESSION['erro'])): ?>
                    <div class="error-message">
                        <?php 
                            echo $_SESSION['erro'];
                            unset($_SESSION['erro']);
                        ?>
                    </div>
                <?php endif; ?>
            
                <form action="flow/authlogin.php" method="POST">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required><br><br>

                    <input type="submit" value="Login">
                </form>
                
                <style>
                    .error-message {
                        background-color: #f8d7da;
                        color: #721c24;
                        padding: 12px;
                        border: 1px solid #f5c6cb;
                        border-radius: 4px;
                        margin: 15px 0;
                    }
                </style>
            </main>
        </body>

    </html>