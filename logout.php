<?php
//ON DETRUIT LA SESSION ET ON REDIRIGE VERS LA PAGE DE LOGIN//
session_start();
unset($_SESSION['id']);
session_destroy();
header('Location: admin-login.php');
exit();