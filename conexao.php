<?php
# use "use" after include or require

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './vendor/autoload.php';

include "config.php";
$config = load_config();
//gerencia_requisicao();
//Verifica se a sessão está habilitada mas não foi inicada
if (session_status() === PHP_SESSION_NONE) {
    // A sessão não foi iniciada, então iniciamos ela
    session_start();
}

if (isset($_SESSION['session'])) {
    gerencia_requisicao();
} elseif (isset($_SESSION['alterarcurriculo'])) {
    gerencia_requisicao();
} else {
    echo msg_erro('Sessão não autorizada');
    exit();
}

function cadastrarCandidato($connect, $result_data_nasc_formatada, $caminho_arquivo)
{


    $nome = mysqli_escape_string($connect, $_POST["nome"]); //criando as conexões com a base de dados
    $data_nasc = mysqli_escape_string($connect, $result_data_nasc_formatada);
    $email = mysqli_escape_string($connect, $_POST["email"]);
    $endereco = mysqli_escape_string($connect, $_POST["endereco"]);
    $bairro = mysqli_escape_string($connect, $_POST["bairro"]);
    $cidade = mysqli_escape_string($connect, $_POST["cidade"]);
    $estado = mysqli_escape_string($connect, $_POST["estado"]);
    $cep = mysqli_escape_string($connect, $_POST["cep"]);
    $whatsapp = mysqli_escape_string($connect, $_POST["whatsapp"]);
    $telefone = mysqli_escape_string($connect, $_POST["telefone"]);
    $funcao = mysqli_escape_string($connect, $_POST["funcao"]);
    $pcd = mysqli_escape_string($connect, $_POST["pcd"]);
    $qual_deficiencia = mysqli_escape_string($connect, $_POST["qual_deficiencia"]);
    $tem_indicacao = mysqli_escape_string($connect, $_POST["tem_indicacao"]);
    $quem_indicou = mysqli_escape_string($connect, $_POST["quem_indicou"]);
    $arquivo = mysqli_escape_string($connect, $caminho_arquivo);
    $aceite_termo = mysqli_escape_string($connect, $_POST["aceite_termo"]);

    $cadastrar = "INSERT INTO banco_talentos "; //Inserção no banco de dados
    $cadastrar .= "(nome,data_nasc,email,endereco,bairro, cidade,estado,cep,whatsapp,telefone,funcao,pcd,qual_deficiencia,
   tem_indicacao, quem_indicou, arquivo, aceite_termo)";
    $cadastrar .= "VALUES ";
    $cadastrar .= "('$nome', str_to_date('$data_nasc', '%d-%m-%Y'), '$email', '$endereco','$bairro','$cidade', '$estado', '$cep', '$whatsapp', '$telefone', '$funcao',
   '$pcd', '$qual_deficiencia', '$tem_indicacao', '$quem_indicou','$arquivo','$aceite_termo') ";


    $query_bem_sucedida = mysqli_query($connect, $cadastrar);

    if (!$query_bem_sucedida) {
        // Erro na query
        $erro_msg = mysqli_error($connect);
        $erro_code = mysqli_errno($connect);

        if ($erro_code == 1062) {
            echo msg_erro("Registro já existe na nossa base de dados!");
        } else {
            echo msg_erro("Erro na inserção: $erro_msg");
        }
        return false;
    }

    return true;
}

/**
 * Converte Formato da data de 'Y-m-d' para 'd-m-y'
 * @param [string] $data
 * @return [string]  
 */
function retornarDataFormatada($data)
{
    //$str_data_atendimento = $data;
    $data = trim($data);
    $data_nascimento = DateTime::createFromFormat('Y-m-d', $data);
    if ($data_nascimento === false || $data_nascimento->format('Y-m-d') !== $data) {
        $errors = DateTime::getLastErrors();
        $erroNaConversao = "Erro ao converter data tente novamente:  " . implode(', ', $errors['errors']);
        return [false, $erroNaConversao];
    } else {
        $data_nascimento_formatada = $data_nascimento->format('d-m-Y');
        return [true, $data_nascimento_formatada];
    }
}

/**
 * Valida quais campos estão faltando
 *
 * @param [type] $request
 * @param [type] $campos
 * @return void
 */
function campos_required($request, $campos)
{
    $result = "";
    foreach ($campos as $campo) {
        if (!isset($request[$campo])) {
            $result = $result . "Campo " . $campo . " Faltando.\n";
        }
    }
    return $result;
}

/**
 * Recebe uma variável como parâmetro e exibe uma mensagem
 *
 * @param [type] $msg
 * @return void
 */
function msg_erro($msg)
{
    return '<div align="center"><table><td style="width: 300px; height: 300px;">
    <img alt="svgImg" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDY0IDY0IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA2NCA2NCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8ZyBpZD0iQ2FwdGlvbnMiPg0KPC9nPg0KPGcgaWQ9Ikljb25zIj4NCgk8Zz4NCgkJPGc+DQoJCQk8cGF0aCBmaWxsPSIjRkE2NDUwIiBkPSJNMzAuMjQ0LDExLjIxOUw3LjQzMSw1My4wNDJDNi43MDUsNTQuMzc1LDcuNjY5LDU2LDkuMTg3LDU2aDQ1LjYyNWMxLjUxOCwwLDIuNDgzLTEuNjI1LDEuNzU2LTIuOTU4DQoJCQkJTDMzLjc1NiwxMS4yMTlDMzIuOTk4LDkuODI5LDMxLjAwMiw5LjgyOSwzMC4yNDQsMTEuMjE5eiIvPg0KCQk8L2c+DQoJCTxnPg0KCQkJPHBhdGggZmlsbD0iI0RDNDYzMiIgZD0iTTEwLjI0Nyw1My4wNDJMMzMuMDYsMTEuMjE5YzAuMDk3LTAuMTc4LDAuMjE2LTAuMzMyLDAuMzQ4LTAuNDY0Yy0wLjkwMi0wLjkwMS0yLjUwMy0wLjc0OC0zLjE2NCwwLjQ2NA0KCQkJCUw3LjQzMSw1My4wNDJDNi43MDUsNTQuMzc1LDcuNjY5LDU2LDkuMTg3LDU2aDIuODE2QzEwLjQ4NSw1Niw5LjUyLDU0LjM3NSwxMC4yNDcsNTMuMDQyeiIvPg0KCQk8L2c+DQoJCTxnPg0KCQkJPHBhdGggZmlsbD0iI0YwRjBGMCIgZD0iTTMyLDQyTDMyLDQyYy0xLjEwNSwwLTItMC44OTUtMi0yVjI2YzAtMS4xMDUsMC44OTUtMiwyLTJoMGMxLjEwNSwwLDIsMC44OTUsMiwydjE0DQoJCQkJQzM0LDQxLjEwNSwzMy4xMDUsNDIsMzIsNDJ6Ii8+DQoJCTwvZz4NCgkJPGc+DQoJCQk8cGF0aCBmaWxsPSIjRjBGMEYwIiBkPSJNMzIsNTBMMzIsNTBjLTEuMTA1LDAtMi0wLjg5NS0yLTJ2MGMwLTEuMTA1LDAuODk1LTIsMi0yaDBjMS4xMDUsMCwyLDAuODk1LDIsMnYwDQoJCQkJQzM0LDQ5LjEwNSwzMy4xMDUsNTAsMzIsNTB6Ii8+DQoJCTwvZz4NCgkJPGc+DQoJCQk8Y2lyY2xlIGZpbGw9IiNGQUI0MDAiIGN4PSI1MiIgY3k9IjEyIiByPSI4Ii8+DQoJCTwvZz4NCgkJPGc+DQoJCQk8cGF0aCBmaWxsPSIjREM5NjAwIiBkPSJNNDUuOTgzLDEyYzAtNC4wODIsMy4wNTktNy40NDMsNy4wMDktNy45MzJDNTIuNjY2LDQuMDI3LDUyLjMzNiw0LDUyLDRjLTQuNDE4LDAtOCwzLjU4Mi04LDgNCgkJCQlzMy41ODIsOCw4LDhjMC4zMzYsMCwwLjY2Ni0wLjAyNywwLjk5MS0wLjA2OEM0OS4wNDIsMTkuNDQzLDQ1Ljk4MywxNi4wODIsNDUuOTgzLDEyeiIvPg0KCQk8L2c+DQoJCTxnPg0KCQkJPHBvbHlnb24gZmlsbD0iI0YwRjBGMCIgcG9pbnRzPSI1Ni4yNDMsMTQuODI4IDUzLjQxNCwxMiA1Ni4yNDMsOS4xNzIgNTQuODI4LDcuNzU3IDUyLDEwLjU4NiA0OS4xNzIsNy43NTcgNDcuNzU3LDkuMTcyIA0KCQkJCTUwLjU4NiwxMiA0Ny43NTcsMTQuODI4IDQ5LjE3MiwxNi4yNDMgNTIsMTMuNDE0IDU0LjgyOCwxNi4yNDMgCQkJIi8+DQoJCTwvZz4NCgk8L2c+DQo8L2c+DQo8L3N2Zz4NCg=="/>
    </td><td><h1>' . $msg . '</h1><hr>
    </table></div>';
}

/**
 * Função para mensagem de sucesso, a mesma recebe o nome e o e-mail do candidato e exibe
 * a mensagem
 *
 * @param [type] $nome
 * @param [type] $email
 * @return void
 */
function msg_sucesso($nome, $email)
{
    return '<center><table><td>
    <img alt="svgImg" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iMjQwIiBoZWlnaHQ9IjI0MCIKdmlld0JveD0iMCAwIDE3MiAxNzIiCnN0eWxlPSIgZmlsbDojMDAwMDAwOyI+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJub256ZXJvIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgc3Ryb2tlLWRhc2hhcnJheT0iIiBzdHJva2UtZGFzaG9mZnNldD0iMCIgZm9udC1mYW1pbHk9Im5vbmUiIGZvbnQtd2VpZ2h0PSJub25lIiBmb250LXNpemU9Im5vbmUiIHRleHQtYW5jaG9yPSJub25lIiBzdHlsZT0ibWl4LWJsZW5kLW1vZGU6IG5vcm1hbCI+PHBhdGggZD0iTTAsMTcydi0xNzJoMTcydjE3MnoiIGZpbGw9Im5vbmUiIHN0cm9rZT0ibm9uZSI+PC9wYXRoPjxnIHN0cm9rZT0ibm9uZSI+PHBhdGggZD0iTTE1Ny42NjY2Nyw4NmMwLDM5LjU3NzkyIC0zMi4wODg3NSw3MS42NjY2NyAtNzEuNjY2NjcsNzEuNjY2NjdjLTM5LjU3NzkyLDAgLTcxLjY2NjY3LC0zMi4wODg3NSAtNzEuNjY2NjcsLTcxLjY2NjY3YzAsLTM5LjU3NzkyIDMyLjA4ODc1LC03MS42NjY2NyA3MS42NjY2NywtNzEuNjY2NjdjMzkuNTc3OTIsMCA3MS42NjY2NywzMi4wODg3NSA3MS42NjY2Nyw3MS42NjY2N3oiIGZpbGw9IiM0Y2FmNTAiPjwvcGF0aD48cGF0aCBkPSJNMTIzLjk5MDUsNTIuMzIzODNsLTQ4Ljc0MDUsNDguNzIyNThsLTIwLjA3MzgzLC0yMC4wNTk1bC0xMC4wMjI1OCwxMC4wMjI1OGwzMC4wOTY0MiwzMC4xMTA3NWw1OC43NTk1LC01OC43NzM4M3oiIGZpbGw9IiNjY2ZmOTAiPjwvcGF0aD48L2c+PGcgc3Ryb2tlPSJub25lIj48Zz48cGF0aCBkPSJNMTcyLDEzNi4xNjY2N2MwLDE5LjcwODMzIC0xNi4xMjUsMzUuODMzMzMgLTM1LjgzMzMzLDM1LjgzMzMzYy0xOS43MDgzMywwIC0zNS44MzMzMywtMTYuMTI1IC0zNS44MzMzMywtMzUuODMzMzNjMCwtMTkuNzA4MzMgMTYuMTI1LC0zNS44MzMzMyAzNS44MzMzMywtMzUuODMzMzNjMTkuNzA4MzMsMCAzNS44MzMzMywxNi4xMjUgMzUuODMzMzMsMzUuODMzMzMiIGZpbGw9IiM0M2EwNDciPjwvcGF0aD48cGF0aCBkPSJNMTU3LjY2NjY3LDEyOWgtMTQuMzMzMzN2LTE0LjMzMzMzaC0xNC4zMzMzM3YxNC4zMzMzM2gtMTQuMzMzMzN2MTQuMzMzMzNoMTQuMzMzMzN2MTQuMzMzMzNoMTQuMzMzMzN2LTE0LjMzMzMzaDE0LjMzMzMzeiIgZmlsbD0iI2ZmZmZmZiI+PC9wYXRoPjwvZz48L2c+PHBhdGggZD0iTTEwMC4zMzMzMywxNzJ2LTcxLjY2NjY3aDcxLjY2NjY3djcxLjY2NjY3eiIgaWQ9Im92ZXJsYXktZHJhZyIgZmlsbD0iI2ZmMDAwMCIgc3Ryb2tlPSJub25lIiBvcGFjaXR5PSIwIj48L3BhdGg+PC9nPjwvc3ZnPg=="/>
    </td><td><h2>' . $nome . '</h2><hr>
    <h1>Seu curriculo foi cadastrado com Sucesso!</h1>
    <p style="color:43a047">Link de alteração enviado para ' . $email . ' </p></td>
    </table>';
}
/**
 * Recebe o nome do candidato e retorna a mensagem de exclusão 
 *
 * @param [type] $nome
 * @return void
 */
function msg_exclusao($nome)
{
    return '<center><table><td>
    <img alt="svgImg" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iMjQwIiBoZWlnaHQ9IjI0MCIKdmlld0JveD0iMCAwIDE3MiAxNzIiCnN0eWxlPSIgZmlsbDojMDAwMDAwOyI+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJub256ZXJvIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgc3Ryb2tlLWRhc2hhcnJheT0iIiBzdHJva2UtZGFzaG9mZnNldD0iMCIgZm9udC1mYW1pbHk9Im5vbmUiIGZvbnQtd2VpZ2h0PSJub25lIiBmb250LXNpemU9Im5vbmUiIHRleHQtYW5jaG9yPSJub25lIiBzdHlsZT0ibWl4LWJsZW5kLW1vZGU6IG5vcm1hbCI+PHBhdGggZD0iTTAsMTcydi0xNzJoMTcydjE3MnoiIGZpbGw9Im5vbmUiIHN0cm9rZT0ibm9uZSI+PC9wYXRoPjxnIHN0cm9rZT0ibm9uZSI+PHBhdGggZD0iTTE1Ny42NjY2Nyw4NmMwLDM5LjU3NzkyIC0zMi4wODg3NSw3MS42NjY2NyAtNzEuNjY2NjcsNzEuNjY2NjdjLTM5LjU3NzkyLDAgLTcxLjY2NjY3LC0zMi4wODg3NSAtNzEuNjY2NjcsLTcxLjY2NjY3YzAsLTM5LjU3NzkyIDMyLjA4ODc1LC03MS42NjY2NyA3MS42NjY2NywtNzEuNjY2NjdjMzkuNTc3OTIsMCA3MS42NjY2NywzMi4wODg3NSA3MS42NjY2Nyw3MS42NjY2N3oiIGZpbGw9IiM0Y2FmNTAiPjwvcGF0aD48cGF0aCBkPSJNMTIzLjk5MDUsNTIuMzIzODNsLTQ4Ljc0MDUsNDguNzIyNThsLTIwLjA3MzgzLC0yMC4wNTk1bC0xMC4wMjI1OCwxMC4wMjI1OGwzMC4wOTY0MiwzMC4xMTA3NWw1OC43NTk1LC01OC43NzM4M3oiIGZpbGw9IiNjY2ZmOTAiPjwvcGF0aD48L2c+PGcgc3Ryb2tlPSJub25lIj48Zz48cGF0aCBkPSJNMTcyLDEzNi4xNjY2N2MwLDE5LjcwODMzIC0xNi4xMjUsMzUuODMzMzMgLTM1LjgzMzMzLDM1LjgzMzMzYy0xOS43MDgzMywwIC0zNS44MzMzMywtMTYuMTI1IC0zNS44MzMzMywtMzUuODMzMzNjMCwtMTkuNzA4MzMgMTYuMTI1LC0zNS44MzMzMyAzNS44MzMzMywtMzUuODMzMzNjMTkuNzA4MzMsMCAzNS44MzMzMywxNi4xMjUgMzUuODMzMzMsMzUuODMzMzMiIGZpbGw9IiM0M2EwNDciPjwvcGF0aD48cGF0aCBkPSJNMTU3LjY2NjY3LDEyOWgtMTQuMzMzMzN2LTE0LjMzMzMzaC0xNC4zMzMzM3YxNC4zMzMzM2gtMTQuMzMzMzN2MTQuMzMzMzNoMTQuMzMzMzN2MTQuMzMzMzNoMTQuMzMzMzN2LTE0LjMzMzMzaDE0LjMzMzMzeiIgZmlsbD0iI2ZmZmZmZiI+PC9wYXRoPjwvZz48L2c+PHBhdGggZD0iTTEwMC4zMzMzMywxNzJ2LTcxLjY2NjY3aDcxLjY2NjY3djcxLjY2NjY3eiIgaWQ9Im92ZXJsYXktZHJhZyIgZmlsbD0iI2ZmMDAwMCIgc3Ryb2tlPSJub25lIiBvcGFjaXR5PSIwIj48L3BhdGg+PC9nPjwvc3ZnPg=="/>
    </td><td><h2>' . $nome . '</h2><hr>
    <h1>Seu curriculo foi retirado da nossa base de dados com Sucesso!</h1></td>
    </table>';
}
/**
 * Recebe as requisições e chama os metódos de acordo com a action passada
 *
 * @return void
 */
function gerencia_requisicao()
{
    if (isset($_POST['cadastrar'])) {
        valida_query();
        //cadastrar();
    } else if (isset($_GET['deleta'])) {
        deleta_curriculo();
    } else if (isset($_GET['altera'])) {
        seleciona_curriculo();
    } else {
        header('Location: index.php');
        exit();
    }
}
/**
 * Estabelece uma conexão com o banco de dados e faz um select no banco
 * retornando a query.
 *
 * @return @conn
 */
function seleciona_curriculo()
{

    $connect = conexao_banco();
    if ($connect->connect_errno) {
        die("Falha na conexão! (" . $connect->connect_errno . ")" . $connect->connect_error);
    } else if (isset($_GET['altera'])) {

        $cod_md5 = $_GET['altera'];
        $arquivo = "banco/documentos/" . $cod_md5 . ".pdf";
        $consulta = "SELECT * FROM banco_talentos WHERE arquivo =?";
        $stmt = $connect->stmt_init();
        $stmt = $connect->prepare($consulta);

        $stmt->bind_param("s", $arquivo);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar se há registros
        if ($result->num_rows == 0) {
            echo msg_erro("Nenhum registro encontrado.");
            exit;
        }

        // Fechar a declaração preparada
        $stmt->close();
    } else {
        echo msg_erro("Não foi possível consultar Dados");
        $connect = null;
    }
    return $result;
}
/**
 * Estabelece conexão com o banco e deleta o registo, assim como o arquivo do currículo e 
 * retorna a query para quem chamar.
 * @return void
 */
function deleta_curriculo()
{

    $connect = conexao_banco();
    if ($connect->connect_errno) {
        die("Falha na conexão! (" . $connect->connect_errno . ")" . $connect->connect_error);
    }

    if (isset($_GET['deleta']) && isset($_GET['nome']) && isset($_GET['arquivo'])) {

        $email = $_GET['deleta'];
        $nome = $_GET['nome'];
        $cod_md5 = $_GET['arquivo'];
        $arquivo = "banco/documentos/" . $cod_md5 . ".pdf";

        $consulta = "DELETE FROM banco_talentos WHERE email =? AND arquivo=?";

        $stmt = $connect->stmt_init();
        $stmt = $connect->prepare($consulta);

        $stmt->bind_param("ss", $email, $arquivo);
        $stmt->execute();
        if ($stmt->affected_rows >= 1) {
            if (file_exists($arquivo)) {
                unlink(($arquivo));
                echo msg_exclusao($nome);
            } else {
                echo msg_erro("Arquivo ou registro não existe em nossa Base de Dados!");
            }
        } else {
            echo msg_erro("Dados Não localizados: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo msg_erro("Parâmetros inválidos!");
        $connect = null;
    }
    //return $con;
}
/**
 * Faz a conexão com o banco e a validação dos parametros passados via POST, é validado se 
 * existe arquivo .pdf em anexo se sim o nome é alterado para um formato md5 contendo letras
 * e números, após isso é feito o Insert no banco de dados.
 * 
 * @return void
 */

function cadastrar()
{

    $connect =  conexao_banco();
    $arquivo = $_FILES['arquivo'];

    if (!isset($_POST) || empty($_POST)) {
        echo msg_erro('Nada foi postado!');
        exit();
    }
    $required_faltando = campos_required($_POST, array('nome', 'email', 'endereco', 'bairro', 'cidade', 'estado', 'cep', 'whatsapp', 'funcao', 'aceite_termo', 'pcd', 'tem_indicacao', 'cadastrar'));
    if ($required_faltando != "") {
        echo msg_erro($required_faltando);
        exit();
    }

    if ($connect) {

        //verifica se a data de nascimento é valida
        $verifica_data = retornarDataFormatada($_POST["data_nasc"]);
        $data_valida = $verifica_data[0];
        $result_data_nasc_formatada = $verifica_data[1];
        if ($data_valida === false) {
            echo msg_erro($result_data_nasc_formatada);
            exit;
        }
        if ($arquivo !== null) {
            preg_match("/\.(pdf){1}$/i", $arquivo["name"], $ext);
            //gera um nome unico para o arquivo

            if ($ext == true) {
                $nome_arquivo = md5(uniqid(time())) . "." . $ext[1];
                $caminho_arquivo = "banco/documentos/" . $nome_arquivo;
                //Salva arquivo no repositório local
                move_uploaded_file($arquivo["tmp_name"], $caminho_arquivo);
                if (file_exists($caminho_arquivo)) {
                    if (isset($_POST['cadastrar'])) {
                        $registroIncluido = cadastrarCandidato($connect, $result_data_nasc_formatada, $caminho_arquivo);
                    } else {
                        echo msg_erro("Opção de Cadastro Invalida!");
                    }
                } else {
                    echo msg_erro("Falha no Upload do Arquivo");
                }
            } else {
                echo msg_erro("Arquivo anexado não é .PDF favor corrigir.");
            }
        } else {
            echo msg_erro("Nenhum Arquivo Anexado");
        }
    } else {
        echo msg_erro("Sua tentativa falhou!");
    }
    return array($registroIncluido, $_POST["nome"], $_POST["email"], $nome_arquivo);
}

/**
 * Valida se o método cadastrar() retornou true ou false
 * caso true é acionado o método envia_email() passando por parâmetro o nome, email e nome
 * do arquivo  que contém o link para alteração.
 * 
 * @return void
 */
function valida_query()
{
    $dados = cadastrar();

    if ($dados[0] == 1) {
        envia_email($dados[1], $dados[2], $dados[3]);
    }
}

/**
 * Recebe o nome, email, e nome do arquivo, usa a função explode para pegar somento o código
 * md5, monta um link de alteração e envia para o e-mail do cliente
 *
 * @param [type] $nome
 * @param [type] $email
 * @param [type] $nome_arquivo
 * @return void
 */
function envia_email($nome, $email, $nome_arquivo)
{
    //Envio de e-mail com o hash
    $hash_curriculo = explode(".", $nome_arquivo);
    $link_de_alteracao = 'http://127.0.0.1/curriculo/pagina-de-alteracao.php?altera=' . $hash_curriculo[0];

    $mail = new PHPMailer(true);

    try {

        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->charSet = 'UTF-8';
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'teste@gmail.com';
        $mail->Password   = 'senha';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // ENCRYPTION_STARTTLS;      //Habilitando criptografia TLS 
        $mail->Port       = 465;
        //Desabilitando o SSL default
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //Emissor e receptor
        $mail->setFrom('teste@gmail.com', 'Curriculo');
        $mail->addAddress($email);
        $mail->addReplyTo('teste@gmail.com', 'Curriculo');

        //Conteúdo
        $mail->isHTML(true);
        $mail->Subject = 'Alteracao de Curriculo';
        $mail->Body    = "Segue o link para alteracao do seu curriculo: <br>" . $link_de_alteracao . ", guarde-o e nao compartilhe";

        //$mail->send();
        if (!$mail->send()) {
            echo 'Erro: ' . $mail->ErrorInfo;
        } else {
            //echo 'Email enviado com sucesso!';
            echo msg_sucesso($nome, $email);
        }
    } catch (Exception $e) {
        echo "E-mail não pode ser enviado: {$mail->ErrorInfo}";
    }
}
