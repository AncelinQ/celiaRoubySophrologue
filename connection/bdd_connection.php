<?php

	//	Connexion à la base de données
function newDatabase()
{
    return new PDO
    (
        'mysql:host=localhost;dbname=celia_rouby_sophrologue;charset=UTF8',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

}



