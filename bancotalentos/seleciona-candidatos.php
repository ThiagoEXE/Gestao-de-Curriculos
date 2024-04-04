<?php
include('../config.php');
include('verificar-login.php');
$config = load_config();

/**
 * Cria conexão com o banco de dados e faz um select * trazendo todos os campos da tabela. Caso a 
 * conexão falhe será retornado um erro 'Falha na conexão'.
 * @return void
 */
function selecao_candidatos()
{
    $connect = conexao_banco();
    if ($connect->connect_errno) { //verficando se a conexão esta ok
        die("Falha na conexão! (" . $connect->connect_errno . ")" . $connect->connect_error);
    } else {
        $consulta = "
        SELECT nome,
         floor((to_days(curdate()) - to_days(data_nasc)) / 365) as idade,
         email,
         endereco,
         bairro,
         cidade,
         whatsapp,
         telefone,
         funcao,
         qual_deficiencia,
         quem_indicou,
         date_format(data_inclusao, '%Y-%m-%d') as data_inclusao,
         status_processo,
         arquivo
        FROM banco_talentos";

        $con = mysqli_query($connect, $consulta);
    }
    mysqli_close($connect);
    return $con;
};
