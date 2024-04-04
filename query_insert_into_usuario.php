<?php
include('config.php');
$config = load_config();

// Função para cadastrar um novo usuário
function cadastrarUsuario($usuario, $senha)
{
    $conexao = conexao_banco();
    if (!$conexao) {
        die("Não conectou");
    }

    // Hash da senha usando password_hash()
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    $param_1 = mysqli_escape_string($conexao, $usuario);
    $param_2 = mysqli_escape_string($conexao, $senhaHash);
    // Preparar a query de inserção
    
    
    $query = "SELECT id, usuario, senha FROM usuario WHERE usuario= '$param_1'";
    $result = mysqli_query($conexao, $query);
    $numero_linhas_retornadas = mysqli_num_rows($result);
    if ($numero_linhas_retornadas > 0) {
        echo "Usuário já existe!";
        exit;
    }
    
    $query = "INSERT INTO usuario (usuario, senha) VALUES ('$param_1', '$param_2')";

    // Executar a query
    $result = mysqli_query($conexao, $query);
   
    if ($result) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar usuário: " . mysqli_error($conexao);
    }

    // Fechar a conexão
    mysqli_close($conexao);
}

$usuarioNovo = 'teste';
$senhaNova = 'teste@123';

// Chamar a função para cadastrar o novo usuário
cadastrarUsuario($usuarioNovo, $senhaNova);
?>
