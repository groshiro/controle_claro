<?php
// Arquivo: processar_crud.php
require_once 'conexao.php';

// Ativar sessão para redirecionar
session_start(); 

if (!$pdo) {
    die("Falha na conexão.");
}

$acao = $_GET['acao'] ?? $_POST['acao'] ?? ''; 
$id = $_GET['id'] ?? $_POST['id'] ?? null; 

if (!$id || empty($acao)) {
    header("Location: dashboard.php");
    exit;
}

try {
    if ($acao == 'excluir') {
        // --- 1. AÇÃO DE EXCLUIR (DELETE) ---
        // Ação de excluir o usuário/incidente (depende da tabela referenciada)
        // Se a intenção for excluir o incidente, mude para 'DELETE FROM controle'
        $sql_delete = "DELETE FROM usuario WHERE id = :id"; 
        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute(['id' => $id]);

        $mensagem = "Registro ID {$id} excluído com sucesso!";
        header("Location: dashboard.php?status=excluido");
        exit;

    } elseif ($acao == 'alterar_usuario' && $_SERVER['REQUEST_METHOD'] === 'POST') { 
        // --- NOVO BLOCO: ALTERAR DADOS DO USUÁRIO (TABELA usuario) ---
        
        // 1. Receber campos do formulário de usuário:
        $nome = $_POST['nome'] ?? ''; 
        $login = $_POST['login'] ?? '';
        $email = $_POST['email'] ?? '';
        $nivel_permissao = $_POST['nivel_permissao'] ?? ''; 

        // 2. Instrução SQL para a tabela USUARIO
        $sql_update = "UPDATE usuario 
                       SET nome = :nome, 
                           login = :login, 
                           email = :email,
                           nivel_permissao = :nivel_permissao
                       WHERE id = :id";
                         
        $stmt = $pdo->prepare($sql_update);
        
        // 3. Execução: Vincular todos os parâmetros
        $stmt->execute([
            'nome' => $nome, 
            'login' => $login, 
            'email' => $email, 
            'nivel_permissao' => $nivel_permissao,
            'id' => $id
        ]);

        $mensagem = "Permissões do Usuário ID {$id} alteradas com sucesso!";
        header("Location: dashboard.php?status=usuario_alterado");
        exit;
        
    } elseif ($acao == 'alterar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // --- BLOCO ORIGINAL: AÇÃO DE ALTERAR INCIDENTE (TABELA controle) ---
        
        // 1. Receber todos os campos do formulário:
        $incidente = $_POST['incidente'] ?? '';
        $evento = $_POST['evento'] ?? '';
        $endereco = $_POST['endereco'] ?? ''; 
        $area = $_POST['area'] ?? ''; 
        $regiao = $_POST['regiao'] ?? ''; 
        $site = $_POST['site'] ?? ''; 
        $otdr = $_POST['otdr'] ?? '';

        // 2. Instrução SQL Completa para a tabela CONTROLE:
        $sql_update = "UPDATE controle 
                       SET incidente = :incidente, 
                           evento = :evento, 
                           endereco = :endereco,
                           area = :area,
                           regiao = :regiao,
                           site = :site,
                           otdr = :otdr
                       WHERE id = :id";
                             
        $stmt = $pdo->prepare($sql_update);
        
        // 3. Execução: Vincular TODOS os parâmetros
        $stmt->execute([
            'incidente' => $incidente, 
            'evento' => $evento, 
            'endereco' => $endereco, 
            'area' => $area, 
            'regiao' => $regiao, 
            'site' => $site, 
            'otdr' => $otdr, 
            'id' => $id
        ]);

        $mensagem = "Registro ID {$id} alterado com sucesso!";
        header("Location: dashboard.php?status=alterado");
        exit;
    }
    
} catch (PDOException $e) {
    // É uma boa prática redirecionar o erro para a página de dashboard para que o usuário veja o problema
    header("Location: dashboard.php?status=erro&msg=" . urlencode($e->getMessage()));
    exit;
}

// Redirecionamento padrão para qualquer ação não reconhecida
header("Location: dashboard.php");
exit;
?>