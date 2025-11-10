<!DOCTYPE html>
    <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cadastro</title>
            <link rel="stylesheet" href="../assets/css/public.css">
            <link rel="stylesheet" href="../assets/css/cadastro.css">
        </head>

        <body>
            <!-- Carrega a sessão, Mostra o cabeçalho e abre a tag main -->
            <?php require "../view/header.php"; ?>

                <h1>Cadastro de Usuário</h1>
                
                <?php if(isset($_SESSION['erro'])): ?>
                    <div class="error-message">
                        <?php 
                            echo $_SESSION['erro'];
                            unset($_SESSION['erro']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <form action="flow/authcadastro.php" method="POST">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required><br><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required><br><br>

                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required><br><br>

                    <input type="submit" value="Cadastrar">
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