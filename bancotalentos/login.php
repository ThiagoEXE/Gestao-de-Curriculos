<?php
session_start();
include("../config.php");
$config = load_config();
login();


function login()
{

    $conexao = conexao_banco();
    if (!$conexao) {
        die("Não conectou");
    }


    if (empty($_POST['usuario']) || empty($_POST['senha'])) {
        header('Location: index.php');
        exit();
    }

    $usuario = mysqli_escape_string($conexao, $_POST['usuario']);
    $senha = mysqli_escape_string($conexao, $_POST['senha']);

    $query = "SELECT id, usuario, senha FROM usuario WHERE usuario= '$usuario'";

    $result = mysqli_query($conexao, $query);
    if (!$result) {
        die("Acesso negado");
    }

    $row = mysqli_fetch_assoc($result);

    var_dump($row);
    if ($row) {
        if (password_verify($senha, $row["senha"])) {

            $_SESSION['usuario'] = $usuario;
            header('Location: principal.php');
            exit();
        } else {
            $_SESSION['nao-autenticado'] = true;
            header('Location: index.php');
            exit();
        }
    } else {
        $_SESSION['nao-autenticado'] = true;
        header('Location: index.php');
        exit();
    }
}
