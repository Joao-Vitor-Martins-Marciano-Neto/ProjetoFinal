# Análise Detalhada das Alterações

## Contexto
O repositório apresentava erros lógicos e não lógicos devido a inconsistências entre o esquema SQL do banco de dados e o código PHP que o utiliza. As principais alterações foram realizadas para corrigir essas inconsistências e garantir o funcionamento correto do sistema de autenticação e empréstimos.

---

## 1. Arquivo: `SQL` (Esquema do Banco de Dados)

### Alteração 1: Linha 22
**Problema:** A coluna estava nomeada como `isbn INT NOT NULL`, mas deveria ser `id_livro` para referenciar corretamente a chave estrangeira.

**Antes:**
```sql
isbn INT NOT NULL,
```

**Depois:**
```sql
id_livro INT NOT NULL,
```

**Motivo:** O banco de dados deve usar `id_livro` como chave estrangeira para referenciar a tabela `livro`, não `isbn` que é uma string (VARCHAR).

---

### Alteração 2: Linhas 28-29
**Problema:** As referências de chave estrangeira usavam capitalização incorreta (`Usuario` e `Livro` em vez de `usuario` e `livro`).

**Antes:**
```sql
FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
FOREIGN KEY (id_livro) REFERENCES Livro(id_livro)
```

**Depois:**
```sql
FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
FOREIGN KEY (id_livro) REFERENCES livro(id_livro)
```

**Motivo:** PostgreSQL é case-sensitive em nomes de tabelas quando não estão entre aspas. As tabelas foram criadas com nomes em minúsculas, então as referências também devem usar minúsculas.

---

## 2. Arquivo: `public/flow/authlogin.php`

### Alteração 1: Linha 8
**Problema:** A query SELECT não estava buscando os campos necessários (`id_usuario` e `nome`).

**Antes:**
```php
"SELECT email, senha_hash FROM usuario WHERE email = $1"
```

**Depois:**
```php
"SELECT id_usuario, nome, email, senha_hash FROM usuario WHERE email = $1"
```

**Motivo:** É necessário buscar o `id_usuario` para salvar na sessão e o `nome` para exibir no sistema.

---

### Alteração 2: Linha 11
**Problema:** Não estava convertendo o resultado da query em um array associativo.

**Antes:**
```php
// Sem linha para converter resultado
```

**Depois:**
```php
$usuario = pg_fetch_assoc($result);
```

**Motivo:** `pg_query_params()` retorna um resource, não um array. É necessário usar `pg_fetch_assoc()` para obter os dados.

---

### Alteração 3: Linha 14
**Problema:** Verificação incorreta - estava verificando se `$result` estava vazio, mas deveria verificar `$usuario`.

**Antes:**
```php
if(!empty($result) )
```

**Depois:**
```php
if(!empty($usuario) )
```

**Motivo:** `$result` é sempre um resource válido, mesmo quando não há resultados. A verificação deve ser feita no array `$usuario`.

---

### Alteração 4: Linha 17
**Problema:** Estava acessando `$result['senha']` que não existe, deveria ser `$usuario['senha_hash']`.

**Antes:**
```php
if(password_verify($_POST["senha"],$result['senha']))
```

**Depois:**
```php
if(password_verify($_POST["senha"],$usuario['senha_hash']))
```

**Motivo:** O campo no banco de dados é `senha_hash` e deve ser acessado do array `$usuario`.

---

### Alteração 5: Linhas 19-21
**Problema:** Estava tentando acessar `$_POST["nome"]` que não existe no formulário de login.

**Antes:**
```php
$_SESSION['usuario_nome']=$_POST["nome"];
$_SESSION['usuario_email']=$_POST['email'];
$_SESSION['logado']=true;
```

**Depois:**
```php
$_SESSION['usuario_id']=$usuario['id_usuario'];
$_SESSION['usuario_nome']=$usuario['nome'];
$_SESSION['usuario_email']=$usuario['email'];
$_SESSION['logado']=true;
```

**Motivo:** Os dados do usuário devem vir do banco de dados (`$usuario`), não do formulário. Também é necessário salvar o `id_usuario` para uso posterior.

---

### Alteração 6: Linhas 28 e 38
**Problema:** Faltava `exit` após o redirecionamento em caso de erro.

**Antes:**
```php
header('Location: ../login.php');
// continua executando
```

**Depois:**
```php
header('Location: ../login.php');
exit;
```

**Motivo:** Após um `header()` de redirecionamento, deve-se usar `exit` para parar a execução do script.

---

## 3. Arquivo: `public/flow/authcadastro.php`

### Alteração 1: Linhas 28-36
**Problema:** A validação de senha estava sendo feita APÓS o hash, o que tornava as validações inúteis.

**Antes:**
```php
if ($senha !== $confirmar_senha) {
  $msg = $msg . "As senhas não coincidem! <br>";
} else {
  $senha = password_hash($senha, PASSWORD_DEFAULT);
}
//Exigência para no mínimo 6 caracteres na senha e um número (segurança)
if (strlen($senha) < 6) {
  $msg=$msg . "A senha deve conter mínimo 6 caracteres!<br>";
}
if (!preg_match('/[0-9]/', $senha)) {
  $msg = $msg . "A senha deve conter mínimo um número";
}
```

**Depois:**
```php
if ($senha !== $confirmar_senha) {
  $msg = $msg . "As senhas não coincidem! <br>";
}
//Exigência para no mínimo 6 caracteres na senha e um número (segurança)
if (strlen($senha) < 6) {
  $msg=$msg . "A senha deve conter mínimo 6 caracteres!<br>";
}
if (!preg_match('/[0-9]/', $senha)) {
  $msg = $msg . "A senha deve conter mínimo um número";
}
```

**Motivo:** As validações de tamanho e conteúdo devem ser feitas ANTES do hash. O hash sempre resulta em uma string longa, invalidando as verificações de tamanho.

---

### Alteração 2: Linhas 45-46
**Problema:** A inserção no banco não incluía os campos obrigatórios `data_cadastro` e `tipo_usuario`, e usava a senha sem hash.

**Antes:**
```php
$result = pg_query_params($dbconn,"INSERT INTO usuario (nome, email , senha_hash ) VALUES ($1, $2, $3)",[ $nome , $email ,$senha]);
```

**Depois:**
```php
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
$result = pg_query_params($dbconn,"INSERT INTO usuario (nome, email , senha_hash, data_cadastro, tipo_usuario ) VALUES ($1, $2, $3, CURRENT_DATE, 'Cliente')",[ $nome , $email ,$senha_hash]);
```

**Motivo:** O esquema SQL define `data_cadastro` e `tipo_usuario` como NOT NULL. O hash deve ser feito apenas quando as validações passarem. Por padrão, novos usuários são do tipo 'Cliente'.

---

## 4. Arquivo: `public/flow/authemprestimo.php`

### Alteração 1: Linha 11
**Problema:** O parâmetro GET estava sendo buscado como 'ISBN' mas o formulário envia como 'emprestimo'.

**Antes:**
```php
$isbn = $_GET['ISBN'];
```

**Depois:**
```php
$isbn = $_GET['emprestimo'];
```

**Motivo:** O formulário em `book.php` linha 39 usa `name='emprestimo'`.

---

### Alteração 2: Linhas 13-18
**Problema:** A query estava tentando buscar em uma coluna `isbn` que não existe mais na tabela `emprestimo`.

**Antes:**
```php
$resultado=pg_query_params(
      $dbconn,
      "SELECT FROM emprestimo as E WHERE E.isbn = $1",
      [$isbn]
);
```

**Depois:**
```php
$resultado_livro = pg_query_params(
      $dbconn,
      "SELECT id_livro FROM livro WHERE isbn = $1",
      [$isbn]
);

$livro = pg_fetch_assoc($resultado_livro);

if(!empty($livro)) 
{
  // Verificar se já existe empréstimo ativo para este livro
  $resultado_emprestimo = pg_query_params(
        $dbconn,
        "SELECT id_emprestimo FROM emprestimo WHERE id_livro = $1 AND (status_emprestimo = 'Ativo' OR status_emprestimo IS NULL OR status_emprestimo = '')",
        [$livro['id_livro']]
  );
```

**Motivo:** A tabela `emprestimo` agora usa `id_livro` em vez de `isbn`. É necessário primeiro buscar o `id_livro` na tabela `livro` usando o ISBN, e então verificar se existe um empréstimo ativo.

---

### Alteração 3: Linhas 24-25
**Problema:** Estava usando `pg_exec()` (função inexistente) e referenciando coluna `isbn` que não existe.

**Antes:**
```php
pg_exec($dbconn,"INSERT INTO emprestimo (id_usuario, isbn, data_emprestimo, data_prevista_devolucao) VALUES ($1, $2, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days')",
[$_SESSION['usuario_id'], $isbn]);
```

**Depois:**
```php
$resultado_insert = pg_query_params(
  $dbconn,
  "INSERT INTO emprestimo (id_usuario, id_livro, data_emprestimo, data_prevista_devolucao, status_emprestimo) VALUES ($1, $2, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days', 'Ativo')",
  [$_SESSION['usuario_id'], $livro['id_livro']]
);
```

**Motivo:** A função correta é `pg_query_params()`. A coluna correta é `id_livro`, não `isbn`. Também é necessário definir o `status_emprestimo` como 'Ativo'.

---

## 5. Arquivo: `class/books.php`

### Alteração 1: Linhas 12-18
**Problema:** A query estava usando coluna `isbn` que não existe na tabela `emprestimo`.

**Antes:**
```php
$disponivel = empty(pg_fetch_all(
    pg_query_params(
        $dbconn,
        "SELECT FROM emprestimo as E WHERE E.isbn = $1",
        [$book["isbn"]]
    )
));
```

**Depois:**
```php
// Buscar id_livro baseado no ISBN
$livro_result = pg_query_params(
    $dbconn,
    "SELECT id_livro FROM livro WHERE isbn = $1",
    [$book["isbn"]]
);
$livro = pg_fetch_assoc($livro_result);

// Verificar disponibilidade do livro
$disponivel = empty(pg_fetch_all(
    pg_query_params(
        $dbconn,
        "SELECT id_emprestimo FROM emprestimo WHERE id_livro = $1 AND (status_emprestimo = 'Ativo' OR status_emprestimo IS NULL OR status_emprestimo = '')",
        [$livro["id_livro"]]
    )
));
```

**Motivo:** É necessário primeiro buscar o `id_livro` usando o ISBN, depois verificar empréstimos ativos usando `id_livro` e considerando o `status_emprestimo`.

---

### Alteração 2: Linhas 20-26
**Problema:** Os nomes das colunas não correspondiam ao esquema SQL real.

**Antes:**
```php
$this->list[] = new Book($book["id"],
                         $book["title"], 
                         $book["isbn"], 
                         $book["authors"], 
                         $book["description"],
                         $disponivel,
                         $book["img_path"]);
```

**Depois:**
```php
$this->list[] = new Book($book["id_livro"],
                         $book["titulo"], 
                         $book["isbn"], 
                         $book["autor"], 
                         $book["titulo"],
                         $disponivel,
                         $book["isbn"] . ".jpg");
```

**Motivo:** O esquema SQL usa: `id_livro`, `titulo`, `autor`. Não há colunas `id`, `title`, `authors`, `description` ou `img_path`. Usar `titulo` como descrição e gerar o caminho da imagem a partir do ISBN.

---

## 6. Arquivo: `public/pesquisa.php`

### Alteração 1: Linha 52
**Problema:** Os nomes das colunas na query não correspondiam ao esquema SQL.

**Antes:**
```php
$sql = 'SELECT * FROM livro WHERE title ILIKE $1 OR description ILIKE $1 ORDER BY title';
```

**Depois:**
```php
$sql = 'SELECT * FROM livro WHERE titulo ILIKE $1 OR autor ILIKE $1 ORDER BY titulo';
```

**Motivo:** O esquema SQL usa `titulo` e `autor`, não `title` e `description`. A busca agora procura em título e autor.

---

## 7. Arquivo: `public/cadastro.php`

### Alteração 1: Linhas 23-24 (após senha)
**Problema:** Faltava o campo de confirmação de senha no formulário.

**Antes:**
```html
<label for="senha">Senha:</label>
<input type="password" id="senha" name="senha" required><br><br>

<input type="submit" value="Cadastrar">
```

**Depois:**
```html
<label for="senha">Senha:</label>
<input type="password" id="senha" name="senha" required><br><br>

<label for="confirmar_senha">Confirmar Senha:</label>
<input type="password" id="confirmar_senha" name="confirmar_senha" required><br><br>

<input type="submit" value="Cadastrar">
```

**Motivo:** O código em `authcadastro.php` espera um campo `confirmar_senha` para validação, mas o formulário não o fornecia.

---

## Resumo das Correções

### Erros Corrigidos:
1. **SQL:** Inconsistência entre colunas da tabela emprestimo (isbn → id_livro)
2. **SQL:** Case sensitivity nas referências de foreign key
3. **authlogin.php:** Erro ao buscar e verificar dados do usuário
4. **authlogin.php:** Tentativa de acessar campo inexistente no formulário
5. **authcadastro.php:** Validação de senha após hash
6. **authcadastro.php:** Campos obrigatórios faltando no INSERT
7. **authemprestimo.php:** Parâmetro GET incorreto
8. **authemprestimo.php:** Uso de função inexistente e coluna incorreta
9. **books.php:** Nomes de colunas incorretos do banco de dados
10. **pesquisa.php:** Nomes de colunas incorretos na query
11. **cadastro.php:** Campo de confirmação de senha ausente

### Impacto:
- Sistema de login agora funciona corretamente
- Sistema de cadastro valida senhas apropriadamente
- Sistema de empréstimos usa as colunas corretas do banco
- Pesquisa de livros funciona com o esquema SQL real
- Todas as inconsistências entre código e banco de dados foram resolvidas
