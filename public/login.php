<!DOCTYPE html>
    <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
        </head>

        <body>
            <!-- Carrega a sessão, Mostra o cabeçalho e abre a tag main -->
            <?php require "/view/header.php"; ?>

            <h1>Login</h1>
                <form action="/flow/authlogin.php" method="POST">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required><br><br>

                    <input type="submit" value="Login">
                </form>
            </main>
        </body>

    </html>