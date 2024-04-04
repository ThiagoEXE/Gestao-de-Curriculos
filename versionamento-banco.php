<?php
include("config.php");
$config = load_config();
cria_base_dados();


function cria_base_dados()
{
  $connect = conexao_server();

  if (!$connect) {
    die("Conexão falhou: " . mysqli_connect_error());
  } else {

    $sql = "CREATE DATABASE curriculo";
    if (mysqli_query($connect, $sql)) {
      echo "Banco de dados criado com sucesso!<br>" . PHP_EOL;
    } else {
      echo "Erro ao criar banco de dados: " . mysqli_error($connect) . "<br>" . PHP_EOL;
    }
    mysqli_close($connect);
  }
}

$conexao = conexao_banco();
if ($conexao) {
  criar_tabela_scripts_executados($conexao);
  executar_scripts($conexao);
} else {
  echo "Não conectou";
}

function criar_tabela_scripts_executados($connect)
{
  // $connect = conexao_banco();

  if (!$connect) {
    die("Conexão falhou: " . mysqli_connect_error());
  } else {


    $sql = "CREATE TABLE `scripts_executados` 
                 (`id` int(11) AUTO_INCREMENT, 
                 `nome_do_script` VARCHAR(50), 
                 `data_execucao` DATE, 
                PRIMARY KEY(`id`))";

    if (mysqli_query($connect, $sql)) {
      echo "Tabela scripts_executados criada com com sucesso!<br>" . PHP_EOL;
    } else {
      echo "Erro ao criar tabela scripts_executados: " . mysqli_error($connect) . "<br>" . PHP_EOL;
    }
    //mysqli_close($connect);
  }
}

function script_executado($script, $conexao)
{
  $sql = "SELECT * FROM scripts_executados WHERE nome_do_script = '$script'";
  $resultado = mysqli_query($conexao, $sql);
  return mysqli_num_rows($resultado) > 0;
}

function executar_scripts($conexao)
{
  //require 'conexao.php'; // abre a conexão com o banco de dados
  $pasta_scripts = './scripts-banco';
  $arquivos = scandir($pasta_scripts);

  foreach ($arquivos as $arquivo) {

    if (!in_array($arquivo, array('.', '..')) && is_file($pasta_scripts . '/' . $arquivo)) {
      if (!script_executado($arquivo, $conexao)) {
        $sql = file_get_contents($pasta_scripts . '/' . $arquivo);
        $result = mysqli_query($conexao, $sql);
        if ($result) {
          insere_registro_execucao($conexao, $arquivo);
        } else {
          echo "Erro ao executar script $arquivo: " . mysqli_error($conexao) . "<br>" . PHP_EOL;
        }
      }
    }
  }
  mysqli_close($conexao); // fecha a conexão com o banco de dados
}
function insere_registro_execucao($conexao, $arquivo)
{
  $data_execucao = date('Y-m-d H:i:s');
  $sql = "INSERT INTO scripts_executados (nome_do_script, data_execucao) VALUES ('$arquivo', '$data_execucao')";
  $insere_controle = mysqli_query($conexao, $sql);
  if ($insere_controle) {
    echo "Script $arquivo executado com sucesso!<br>" . PHP_EOL;
  } else {
    echo "Script já executado anteriormente<br>" . PHP_EOL;
  }
}
//mysqli_close($conexao); // fecha a conexão com o banco de dados
