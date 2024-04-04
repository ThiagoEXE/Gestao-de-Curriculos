<?php

include('config.php');
$config = load_config();
gerencia_requisicao();
/**
 * Recebe status, mensagem e um array que são transformado em json
 *
 * @param integer $status
 * @param string $msg
 * @param array $dados
 * @return json
 */
function dados_json($status = 200, $msg = 'Ok', $dados = array())
{
    if ($status == 204) {
        return json_encode($dados);
    } else {
        $result['status'] = $status; // Status do Ação
        $result['msg'] = $msg; // Message do Ação
        $result['dados'] = $dados; // Resultado do Ação
        return json_encode($result);
    }
}
/**
 * Insere nova função/vaga no banco de dados
 *
 * @return void
 */
function insere_funcoes()
{

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    $connect =  conexao_banco();

    if ($connect->connect_errno) {
        echo dados_json($connect->connect_errno, $connect->connect_error, array());
    } else {
        // print_r($_POST);
        if (isset($_POST['nome_vaga'])) {
            $nome_vaga = $_POST["nome_vaga"];

            $idade_minima = !empty($_POST["idade_minima"]) ? $_POST["idade_minima"] : 18;
            $idade_maxima = !empty($_POST["idade_maxima"]) ? $_POST["idade_maxima"] : 99;

            $cadastrar = "INSERT INTO vagas(nome_vaga, idade_minima, idade_maxima) VALUES (?,?,?)";

            $stmt = $connect->prepare($cadastrar);

            $stmt->bind_param("sii", $nome_vaga, $idade_minima, $idade_maxima);

            if ($stmt->execute()) {
                echo dados_json(200, 'Função Cadastrada com Sucesso!', array());
            } else {
                echo dados_json(400, 'Falha de Gravação!', array());;
            }

            $stmt->close();
        }
    }
    mysqli_close($connect);
}
/**
 * Deleta função/vaga 
 *
 * @return void
 */
function deleta_funcao()
{

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    $connect =  conexao_banco();
    if (!$connect) {
        echo dados_json(400, mysqli_connect_error(), array());;
    }
    if (isset($_POST['nome_vaga'])) {
        $nome_vaga = $_POST['nome_vaga'];
        $sql = "DELETE FROM vagas WHERE nome_vaga='$nome_vaga'";
    }
    if (mysqli_query($connect, $sql)) {
        echo dados_json(200, 'Função deletada com Sucesso!', array());
    } else {
        echo dados_json(400, 'Sua tentativa falhou!', array());
    }
    mysqli_close($connect);
}
/**
 * Reecebe um inteiro indicando o status da conexão e faz um SELECT no banco trazendo 
 * todas as funções/vagas.
 *
 * @param integer $isok
 * @return void
 */
function lista_funcoes($isok = 204)
{
    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    $connect = conexao_banco();
    if ($connect->connect_errno) {
        echo dados_json($connect->connect_errno, $connect->connect_error, array());
    } else {
        $sql = "SELECT * FROM vagas";
        $query = mysqli_query($connect, $sql);
    }
    $result = array();

    //itera cada linha da consulta e armazena os valores em um array associativo 
    while ($dado = $query->fetch_assoc()) {
        array_push($result, $dado);
    }
    //fecha a conexao
    mysqli_close($connect);
    //Transforma o array associativo em um json
    echo dados_json($isok, 'Ok', $result);
}

/**
 * Atualiza no banco de dados o status dos candidatos selecionados
 *
 * @return void
 */
function altera_status()
{

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    $connect =  conexao_banco();
    if (!$connect) {
        echo dados_json(400, mysqli_connect_error(), array());;
    } else {
        $lista = $_POST['lista'];
        $lista_emails = array();
        foreach ($lista as $item) {
            array_push($lista_emails, "'" . $item["email"] . "'");
        }
        $email_in = implode(',', $lista_emails);
        $status = $_POST['status'];
        $sql = "UPDATE banco_talentos SET status_processo='" . $status . "' WHERE email IN(" . $email_in . ")";
        $result['sql'] = $sql;
        if (mysqli_query($connect, $sql)) {
            echo dados_json(200, 'Alterado com Sucesso!', array());
        } else {
            echo dados_json(400, 'Sua tentativa falhou!', array());
        }
    }
    mysqli_close($connect);
}
/**
 * Valida a existência de um e-mail na base de dados e retorna uma mensagem indicando 
 * se o e-mail já existe ou não
 *
 * @return void
 */
function consulta_email()
{
    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    $connect =  conexao_banco();
    if (!$connect) {
        echo dados_json(400, mysqli_connect_error(), array());;
    } else {
        if (isset($_GET['email'])) {

            $email = $_GET['email'];
            $sql = "SELECT * FROM banco_talentos WHERE email='$email'";

            $consulta = mysqli_query($connect, $sql);
            $linhas = mysqli_num_rows($consulta);
            if ($linhas === 0) {
                echo "E-mail novo pode prosseguir com o cadastro!";
            } else {
                echo "E-mail já existe na nossa base de dados!";
            }
        }
    }
    mysqli_close($connect);
}
// Termino Funções

// Main

/**
 * Chama os métodos que fazem as querys no banco de acordo com a action passada na requisição
 *
 * @return void
 */
function gerencia_requisicao()
{
    if (!isset($_REQUEST['action'])) {
        lista_funcoes();
    } else {
        $action = $_REQUEST['action'];

        if ($action == 'getfuncoes') {
            lista_funcoes(200);
        }
        if ($action == 'addfuncoes' && !empty($_POST)) {
            insere_funcoes();
        }
        if ($action == 'delfuncoes' && !empty($_POST)) {
            deleta_funcao();
        }
        if ($action == 'status') {
            altera_status();
        }
        if ($action == 'email') {
            consulta_email();
        }
    }
}
