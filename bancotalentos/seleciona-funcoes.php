<?php
include('../config.php');
include('verificar-login.php');
$config = load_config();
/**
 * Cria conexão com o banco de dados e faz um select * trazendo todos os campos da tabela. Caso a 
 * conexão falhe será retornado um erro 'Falha na conexão'.
 * @return void
 */
function selecao_funcoes(){
    global $config;
   
    $connect = conexao_banco();

    if ($connect->connect_errno) { //verficando se a conexão esta ok
        die("Falha na conexão! (" . $connect->connect_errno . ")" . $connect->connect_error);
    } else {
        $consulta = "SELECT * FROM vagas";
        $con = mysqli_query($connect, $consulta);
    }
    mysqli_close($connect);
    return $con;
};
