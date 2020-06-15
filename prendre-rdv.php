<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'connection/bdd_connection.php';
require_once 'class/userClass.php';
require_once 'class/patientClass.php';
require_once 'class/rdvClass.php';

//SI ON RECOIT DES INFOS DU FORMLAIRE DE CONTACT, ON LES ENVOIE ET ON CRÉE UNE NOUVELLE ENTRÉE DANS LA TABLE 'RDV'//
if (isset($_POST['rdvData'])){

    $user = new userClass($_POST['firstName'], $_POST['lastName'], $_POST['phone'], $_POST['email']);
    $user = $user->newUser();
    $patient = new patientClass();
    $patient = $user->newPatient();
    $newRdv = new rdvClass($_POST['timeSlotDateTime'], $_POST['timeSlotFull'], $_POST['motif'], $_POST['message']);
    var_dump($newRdv);
    $newRdv = $newRdv->newRdv();
    var_dump($newRdv);

}


$template = 'prendre-rdv';
require_once 'layout.phtml';
