<?php
ob_start();
// Arquivo: validar_login.php
session_start();
require_once 'conexao.php'; 

// 1. Recebe os dados do formulário (Chaves: 'login' e 'pswd' do HTML)
$login_usuario = $_POST['login'] ?? ''; 
$senha_usuario = $_POST['pswd'] ?? ''; 

//2. Verifica se a conexão PDO foi bem-sucedida (se não, retorna erro 3)
if (!$pdo) {
    ob_end_clean();
    header("Location: index.php?erro=3"); 
    exit;
}

// 3. Verifica se os campos foram preenchidos (se não, retorna erro 2)
if (empty($login_usuario) || empty($senha_usuario)) {
    ob_end_clean();
    header("Location: index.php?erro=2");
    exit;
}

// Lógica de Consulta Segura 
// CORREÇÃO: Usando o placeholder :login (coerente com o campo WHERE)
// Nota: 'controle' é o nome da tabela.
$instrucaoSQL = 'SELECT senha_hash, nome FROM usuario WHERE login = :login'; 
$stmt = $pdo->prepare($instrucaoSQL);

// 4. Execução: A chave 'login' agora corresponde ao placeholder :login
$stmt->execute(['login' => $login_usuario]);
$controle = $stmt->fetch(); // Busca o registro

// 5. VERIFICAÇÃO FINAL
if ($controle && password_verify($senha_usuario, $controle['senha_hash'])) {
    // SUCESSO
    session_regenerate_id(true); // Boa prática de segurança
    $_SESSION['usuario_logado'] = $login_usuario;
    
    // Usa a variável $controle para buscar o nome
    $_SESSION['nome_completo'] = $controle['nome']; 
    
    ob_end_clean();
    header("Location: dashboard.php");
    exit;
} else {
    // FALHA
    ob_end_clean();
    header("Location: index.php?erro=1");
    exit;
}