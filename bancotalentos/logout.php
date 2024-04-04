<?php
session_start();
logout();

function logout(){
    unset($_SESSION['usuario']);
    header('Location: index.php');
    exit();
}
