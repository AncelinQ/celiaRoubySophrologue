<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'connection/bdd_connection.php';

$template = 'gestion-de-la-douleur';
require_once 'layout.phtml';

