<?php
include('verificar-login.php');
download_pdf();

/**
 * Verifica a origem da requisição retornando o ip do cliente
 *
 * @return @var ipaddress
 */
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
/**
 * Valida quem esta solicitando acesso verificando se o ip esta na lista de ip's liberados
 *
 * @return boolean
 */
function valida_acesso()
{
    $ipfull = get_client_ip();
    $ipaddress = explode(".", $ipfull);
    $ips_liberados = array('::1', '10', '127.0.0.1'); // lista de IP´s Permitidos
    if (in_array($ipfull, $ips_liberados) || in_array($ipaddress[0], $ips_liberados)) {
        return True;
    }
    return False;
}

/**
 * Direciona para a HOME do Google caso o ip não esteja liberado para acessar o banco de 
 * talentos, permite o download do aqruivo .pdf com o nome do candidato
 * 
 *
 * @return void
 */
function download_pdf()
{
    if (!valida_acesso()) {
        header("Location: https://google.com");
    }
    if (isset($_GET['hash'])) {
        $hash_file = $_GET["hash"];
        $nome = $_GET["nome"] . ".pdf";
        $arquivo = 'C:\\xampp\\htdocs\\curriculo\\' . $hash_file;
        if (file_exists($arquivo)) {
            header('Content-Disposition: attachment; filename=' . $nome . ';');
            header('Content-Type:application/pdf');
            header('Content-Transfer-Encoding:binary');
            header('Content-Length:' . filesize($arquivo));
            readfile($arquivo);
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}
