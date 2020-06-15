<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connection/bdd_connection.php';

session_start();

//SI LA SESSION EST ACTIVE ET QUE L'ID DE LA SESSION EST ACTIVE ET SI L'ID CORRESPOND À CELUI DE L'ADMIN, ALORS ON REDIRIGE VERS ADMIN-OFFICE//
if(isset($_SESSION) && isset($_SESSION['id']) && $_SESSION['id'] === "113HdB* ") {

    header('Location: admin-office.php');

}

//SINON SI ON RECOIT LES INFOS DE LOGIN EN POST ON RECUPÈRE LES DONNÉES DE LA METHODE LOGIN DANS ADMINCLASS //
else if (isset($_POST['loginData'])){

    echo adminClass::login($_POST['login'], $_POST['password']);

}

$template = 'admin-login';
require_once 'layout.phtml';

