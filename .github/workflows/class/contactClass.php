<?php

require_once "connection/bdd_connection.php";
require_once 'emailSenderClass.php';

Class contactClass {

    protected $firstName;
    protected $lastName;
    protected $phone;
    protected $email;
    protected $message;

    /**
     * ON INSTENCIE LA CLASSE AVEC LES PRENOM, NOM, TELEPHONE, EMAIL ET LE MESSAGE DE L'UTILISATEUR
     * contactClass constructor.
     * @param $firstName
     * @param $lastName
     * @param $phone
     * @param $email
     * @param $message
     */
    public function __construct($firstName, $lastName, $phone, $email, $message)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * ON CRÉE UN NOUVEAU CONTACT EN INSÉRANT LES INFOS DANS LA BASE DE DONNÉE ET ON ENVOIE UN MAIL RÉCAPITULATIF À L'UTILISATEUR ET AU PROFESSIONNEL, ON RETOURNE ENFIN LE STATUT
     * @return false|string
     */
    public function newContact(){

        $pdo = newDatabase();

        $query = $pdo->prepare('INSERT INTO contact (firstName, lastName, phone, email, message, creationTimestamp)
        VALUES ( :firstname, :lastname, :phone, :email, :message, NOW())');
        $query->bindValue(":firstname", $this->firstName);
        $query->bindValue(":lastname", $this->lastName);
        $query->bindValue(":phone", $this->phone);
        $query->bindValue(":email", $this->email);
        $query->bindValue(":message", $this->message);
        $query->execute();
        $contactId = $pdo->lastInsertId();

        $status = ['status'=>"OK","msg"=>$contactId];

        emailSender::sendProContactEmail($contactId);
        emailSender::sendClientContactEmail($contactId);

        return json_encode($status);

    }

}