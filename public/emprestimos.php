<!DOCTYPE html>
    <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Empréstimos</title>
             <link rel="stylesheet" href="../assets/css/public.css">
        </head>

        <body>
            <!-- Carrega a sessão, Mostra o cabeçalho e abre a tag main -->
            <?php 
                require "../view/header.php"; 
                require "../config/check_auth.php";
                require_once "../config/db.php";

                // Primeiro, remover empréstimos expirados (mais de 7 dias) da base de dados
                // Isso garante que livros com empréstimos expirados fiquem disponíveis para outros usuários
                $delete_expired = pg_query_params(
                    $dbconn,
                    "DELETE FROM emprestimo WHERE data_emprestimo < CURRENT_DATE - INTERVAL '7 days'",
                    []
                );

                // Buscar empréstimos do usuário logado
                $result = pg_query_params(
                    $dbconn,
                    "SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista_devolucao, 
                            l.titulo, l.autor, l.isbn
                     FROM emprestimo e
                     INNER JOIN livro l ON e.id_livro = l.id_livro
                     WHERE e.id_usuario = $1
                     ORDER BY e.data_emprestimo ASC",
                    [$_SESSION['usuario_id']]
                );

                $emprestimos = pg_fetch_all($result);
                
                // Inverter a ordem para mostrar os mais recentes primeiro
                if($emprestimos) {
                    $emprestimos = array_reverse($emprestimos);
                }
            ?>

            <div class="emprestimos-container">
                <h1>Meus Empréstimos</h1>
                
                <?php if(empty($emprestimos)): ?>
                    <p class="no-loans">Você não possui livros emprestados no momento.</p>
                <?php else: ?>
                    <div class="loans-list">
                        <?php foreach($emprestimos as $emprestimo): 
                            // Calcular se o empréstimo expirou (mais de 7 dias)
                            $data_emprestimo = new DateTime($emprestimo['data_emprestimo']);
                            $hoje = new DateTime();
                            $dias_emprestado = $hoje->diff($data_emprestimo)->days;
                            $expirado = $dias_emprestado >= 7;
                            $status = $expirado ? 'Devolvido' : 'Ativo';
                        ?>
                            <div class="loan-item">
                                <div class="loan-info">
                                    <h3><?php echo htmlspecialchars($emprestimo['titulo']); ?></h3>
                                    <p><strong>Autor:</strong> <?php echo htmlspecialchars($emprestimo['autor']); ?></p>
                                    <p><strong>ISBN:</strong> <?php echo htmlspecialchars($emprestimo['isbn']); ?></p>
                                    <p><strong>Data do Empréstimo:</strong> <?php echo date('d/m/Y', strtotime($emprestimo['data_emprestimo'])); ?></p>
                                    <p><strong>Data Prevista de Devolução:</strong> <?php echo date('d/m/Y', strtotime($emprestimo['data_prevista_devolucao'])); ?></p>
                                    <p><strong>Dias Emprestado:</strong> <?php echo $dias_emprestado; ?> dias</p>
                                    <p class="loan-status <?php echo strtolower($status); ?>">
                                        <strong>Status:</strong> <?php echo $status; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <style>
                .emprestimos-container {
                    max-width: 1200px;
                    margin: 20px auto;
                    padding: 20px;
                }

                .emprestimos-container h1 {
                    text-align: center;
                    margin-bottom: 30px;
                    color: #333;
                }

                .no-loans {
                    text-align: center;
                    font-size: 18px;
                    color: #666;
                    padding: 40px;
                }

                .loans-list {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }

                .loan-item {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    transition: box-shadow 0.3s ease;
                }

                .loan-item:hover {
                    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                }

                .loan-info h3 {
                    margin-top: 0;
                    color: #2c3e50;
                    font-size: 22px;
                }

                .loan-info p {
                    margin: 8px 0;
                    color: #555;
                }

                .loan-status {
                    font-size: 16px;
                    padding: 5px 10px;
                    border-radius: 4px;
                    display: inline-block;
                    margin-top: 10px;
                }

                .loan-status.ativo {
                    background-color: #d4edda;
                    color: #155724;
                }

                .loan-status.devolvido {
                    background-color: #f8d7da;
                    color: #721c24;
                }
            </style>

            </main>
        </body>

    </html>