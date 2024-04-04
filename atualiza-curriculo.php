<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

include('config.php');
$config = load_config();

//Verifica se a sessão está habilitada mas não foi inicada
if (session_status() === PHP_SESSION_NONE) {
    // A sessão não foi iniciada, então iniciamos ela
    session_start();
}

if ($_SESSION['alterarcurriculo']) {
    gerencia_requisicao();
} else {
    echo msg_erro('Sessão não autorizada');
    exit();
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
    return "<h1>" . $msg . "</h1>";
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
    <h1>Seu curriculo foi atualizado com Sucesso!</h1>
    <p style="color:43a047">Link de alteração enviado para ' . $email . ' </p></td>
    </table>';
}

/**
 * Atualiza os dados no banco atravéz de um POST, é validado se o arquivo está
 * anexado e se os demais campos estão preenchidos corretanete, após isso é feita uma query
 * de UPDATE do registro.
 * A função retorna: se a query falhou ou não, nome do candidato, email e nome do arquivo.
 *
 * @return array
 */
function atualiza_curriculo()
{
    $connect = conexao_banco();

    if (!isset($_POST) || empty($_POST)) {
        echo msg_erro('Nada foi postado!');
        exit();
    }
    $required_faltando = campos_required($_POST, array('nome', 'email', 'endereco', 'bairro', 'cidade', 'estado', 'cep', 'whatsapp', 'funcao', 'aceite_termo', 'pcd', 'tem_indicacao', 'atualizar'));
    if ($required_faltando != "") {
        echo msg_erro($required_faltando);
        exit();
    }

    if ($connect) {
        try {

            $arquivo = $_FILES['arquivo'];
            $nome = mysqli_escape_string($connect, $_POST["nome"]); //criando as conexões com a base de dados
            $data_nasc = mysqli_escape_string($connect, $_POST["data_nasc"]); //criando as conexões com a base de dados
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
            $aceite_termo = mysqli_escape_string($connect, $_POST["aceite_termo"]);
            
        } catch (Exception $e) {
            echo msg_erro('Campos faltando informar!');
        }
        if ($arquivo !== null) {
            preg_match("/\.(pdf){1}$/i", $arquivo["name"], $ext);
            //gera um nome unico para o arquivo

            if ($ext == true) {
                $nome_arquivo = md5(uniqid(time())) . "." . $ext[1];
                $caminho_arquivo = "banco/documentos/" . $nome_arquivo;
                $arquivo1 = mysqli_escape_string($connect, $caminho_arquivo);
                move_uploaded_file($arquivo["tmp_name"], $caminho_arquivo);
                if (file_exists($caminho_arquivo)) {
                    if (isset($_POST['atualizar'])) {

                        $consulta = "UPDATE banco_talentos SET nome='$nome', data_nasc='$data_nasc',endereco='$endereco',
                            bairro='$bairro',cidade='$cidade', estado='$estado', cep='$cep', whatsapp='$whatsapp',
                            telefone='$telefone', funcao='$funcao', pcd='$pcd', qual_deficiencia='$qual_deficiencia',
                            tem_indicacao='$tem_indicacao', quem_indicou='$quem_indicou', aceite_termo='$aceite_termo', arquivo='$arquivo1', status_processo='Cadastrado'
                             WHERE email = '$email'";

                        if ($query_bem_sucedida = mysqli_query($connect, $consulta)) {
                            //Caso a altração seja bem sucedida o arquivo antigo é excluido da pasta banco/documentos/
                            $arquivoantigo = $_POST["hash"];
                            $hash_antigo = explode("=", $arquivoantigo);
                            $path = "banco/documentos/" . $hash_antigo[1] . ".pdf";
                            unlink($path);
                            mysqli_close($connect);
                        } else {
                            echo msg_erro("Tente Novamente!");
                        }
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
        "Sua tentativa Falhou!";
    }
    return array($query_bem_sucedida, $nome, $email, $nome_arquivo);
}

/**
 * Recebe as requisições e chama os metódos de acordo com a action passada
 *
 * @return void
 */
function gerencia_requisicao()
{
    if (isset($_POST['atualizar'])) {
        valida_query();
    }
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
    $dados = atualiza_curriculo();

    if ($dados[0] == 1) {
        envia_email($dados[1], $dados[2], $dados[3]);
    } else {
        echo "Ocorreu algum erro";
    }
}

/**
 * Recebe o nome, email, e nome do arquivo, usa a função explode para pegar somento o código
 * md5, monta um link de alteração e envia para o e-mail do cliente usando a função PHPMailer.
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
        $mail -> charSet = 'UTF-8';                                      
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'teste@gmail.com';                     
        $mail->Password   = 'senha';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;// ENCRYPTION_STARTTLS;      //Habilitando criptografia TLS 
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
        if(!$mail->send()) {
            echo 'Erro: ' . $mail->ErrorInfo;
        } else {
            //echo 'Email enviado com sucesso!';
            echo msg_sucesso($nome, $email);
        }
    } catch (Exception $e) {
        echo "E-mail não pode ser enviado: {$mail->ErrorInfo}";
    }
}
