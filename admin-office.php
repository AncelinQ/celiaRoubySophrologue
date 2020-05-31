<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'connection/bdd_connection.php';
require_once 'class/calendarClass.php';
require_once 'class/rdvClass.php';
require_once 'class/adminClass.php';

//SI L'ID DE SESSION N'EST PAS CHARGÉ ET QU'IL N'Y A PAS D'INFOS DE LOGIN, ON REDIRIGE VERS LA PAGE DE LOGIN//
if (!isset($_SESSION['id']) && !isset($_POST['loginData'])){

    header("Location: admin-login.php");
}

//SI ON RECOIT LES INFOS DE LOGIN EN POST ON RECUPÈRE LES DONNÉES DE LA METHODE LOGIN DANS ADMINCLASS //
else if (isset($_POST['loginData'])){

    echo adminClass::login($_POST['login'], $_POST['password']);

}

//SI UN CRENEAU EST SELECIONNÉ ON VA CHERCHER SES DONNÉES ET ON LES RETOURNE //
else if (isset($_POST['rdvTimeSlot'])){

    echo rdvClass::sendToJsonRdvData($_POST['rdvTimeSlot']);

}

//SI UN JOUR EST SELECTIONNÉ POUR ACTIVER/DESACTIVER SES DISPONIBILITÉS, ON VA CHERCHER LES INFOS ET ON LES RETOURNE//
else if (isset($_POST['dayToSwitch'])) {

    echo rdvClass::sendToJsonDayToSwitch($_POST['dayToSwitch']);

}

//SI UN CRÉNEAU EST SELECTIONNÉ POUR ACTIVER/DESACTIVER SA DISPONIBILITÉ, ON VA CHERCHER L'INFO ET ON LA RETOURNE//
else if (isset($_POST['timeSlotToSwitch'])) {

    echo rdvClass::sendToJsonTimeSlotToSwitch($_POST['timeSlotToSwitch']);
}

//SI ON REÇOIT LA DEMANDE DE SUPPRESION D'UN CRÉNEAU, ON L'ENVOI ET ON RETOURNE L'INFOS//
else if (isset($_POST['rdvToDelete'])){

    echo rdvClass::sendToJsonRdvToDelete($_POST['rdvToDelete']);

}

//ENFIN SI LA SESSION EST ACTIVE ET QUE L'ID DE LA SESSION EST ACTIVE ET SI L'ID CORRESPOND À CELUI DE L'ADMIN, ALORS ON CHARGE UN NOUVEAU CALENDRIER ET LA PAGE ADMIN-OFFICE//
else if(isset($_SESSION) && isset($_SESSION['id']) && $_SESSION['id'] === "113HdB* ") {

            $calendar = new calendarClass($_GET['week'] ?? null, $_GET['month'] ?? null, $_GET['year'] ?? null);

            $start = $calendar->getFirstDay()->modify('last monday');
            $monday = $calendar->getWeekDay(1);
            $friday = $calendar->getWeekDay(5);
            $allWeek = $calendar->getMondayToFriday();

            $weekRdvs = rdvClass::getRdvsBetweenByDay($monday, $friday);


            $template = 'admin-office';
            require_once 'layout.phtml';
}






