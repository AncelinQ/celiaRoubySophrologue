<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'connection/bdd_connection.php';
require_once 'class/contactClass.php';

//SI ON RECOIT DES INFOS DU FORMLAIRE DE CONTACT, ON LES ENVOIE ET ON CRÉE UNE NOUVELLE ENTRÉE DANS LA TABLE 'CONTACT'//
if (isset($_POST['contactData'])){

   $newContact = new contactClass($_POST['firstName'], $_POST['lastName'], $_POST['phone'], $_POST['email'], $_POST['message']);
   $newContact->newContact();

}

$template = 'contact';
require_once 'layout.phtml';

