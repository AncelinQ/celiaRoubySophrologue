<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connection/bdd_connection.php';
require_once 'class/calendarClass.php';
require_once 'class/rdvClass.php';

//ON CRÉE UN NOUVEAU CALENDRIER ET ON RÉCUPÈRE LES RDVS INSÉRÉS ENTRE LE LUNDI ET LE VENDREDI AFFICHÉ//
$calendar = new calendarClass($_GET['week'] ?? null, $_GET['month'] ?? null, $_GET['year'] ?? null);

$monday = $calendar->getWeekDay(1);
$friday = $calendar->getWeekDay(5);

$weekRdvs = rdvClass::getRdvsBetweenByDay($monday, $friday);

require_once 'calendar.phtml';
