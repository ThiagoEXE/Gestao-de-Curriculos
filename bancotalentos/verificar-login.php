<?php
session_start();
verifica_acesso();

function verifica_acesso(){
    if(!$_SESSION['usuario']){
        header('Location: index.php');
        exit();
    }
}
