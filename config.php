<?php

/**
 * Lê um arquivo com template php
 *
 * @param [type] $file Arquivo com o template.php
 * @param [type] $args Argumentos a serem utilizados no templete
 * @return void
 * ex:
 * template('template.php',Array(Array('Nome' =>'Teste')))
 * --- template.php
 * <h1>Nome</h1>.
 */
function template( $file, $args ){  
  $template = $file;
  $template_dir_file = __DIR__.'/templates/'.$file;
  if ( !file_exists( $file ) ) {
    if ( !file_exists( $template_dir_file ) ) {
      return '';
    } else  {
      $template = $template_dir_file;
    }
  }
  if ( is_array( $args ) ){
    extract( $args );
  }
  ob_start();
  include $template;
  return ob_get_clean();
}
/**
 * Carrega as configurações de ambiente para teste e produção e retorna o arquivo a ser usado,
 * se houver o arquivo config.dev significa que a aplicação está em ambiente de teste
 * @return @array_config
 */
function load_config() {
  if (file_exists('config.dev')) {
    $array_config = parse_ini_file("config.dev",true);
  } else {
    $array_config = parse_ini_file("config.ini", true);
  }
  return $array_config;
}
/**
 * Verifica a origem da requisição e retorna o ip do cliente
 *
 * @return @ipaddress
 */
function get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}
/**
 * Valida o acesso, verificando se o ip do cliente está na lista de ip's liberados
 *
 * @return boolean
 */
function valida_acesso(){
  $ipfull = get_client_ip();
  $ipaddress = explode(".", $ipfull);
  $ips_liberados = array('::1','10','127.0.0.1','187.44.167.46','187.18.9.46'); // lista de IP´s Permitidos
  if( in_array($ipfull, $ips_liberados) || in_array($ipaddress[0], $ips_liberados) ){
      return True;
  }
  return False;
}
/**
 * Retorna uma conexão com o banco de dados
 *
 * @return void
 */
function conexao_banco()
{
    global $config;
    $server = $config['ACESSO_MYSQL']['server'];
    $user = $config['ACESSO_MYSQL']['user'];
    $password = $config['ACESSO_MYSQL']['password'];
    $dbname = $config['ACESSO_MYSQL']['db_name'];
    $connect =  mysqli_connect($server, $user, $password, $dbname);
    return $connect;
}
/**
 * Retorna uma conexão com o servidor em que está o MYSql
 *
 * @return @connect
 */
function conexao_server()
{
  global $config;
  $server = $config['ACESSO_MYSQL']['server'];
  $user = $config['ACESSO_MYSQL']['user'];
  $password = $config['ACESSO_MYSQL']['password'];
  $connect =  mysqli_connect($server, $user, $password);
  return $connect;
}

