<?php

require_once "connection/bdd_connection.php";
require_once 'userClass.php';
require_once 'emailSenderClass.php';

Class contactClass_wip extends userClass {

    protected $contactId;
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
    public function __construct($userId, $firstName, $lastName, $phone, $email, $contactId, $message)
    {
        parent::__construct($userId, $firstName, $lastName, $phone, $email);
        $this->contactId = $contactId;
        $this->message = trim(htmlspecialchars($message));
    }

    /**
     * ON CRÉE UN NOUVEAU CONTACT EN INSÉRANT LES INFOS DANS LA BASE DE DONNÉE ET ON ENVOIE UN MAIL RÉCAPITULATIF À L'UTILISATEUR ET AU PROFESSIONNEL, ON RETOURNE ENFIN LE STATUT
     * @return false|string
     */
    public function newMessage()
    {
        $pdo = newDatabase();

        $checkUser = $this->checkUser();

        if($checkUser === false){
            $newUser = $this->newUser();
            $userId = $newUser['id'];
        }
        else {
            $userId = $checkUser;
        }

        $query = $pdo->prepare('INSERT INTO contact (userId, message, creationTimestamp)
            VALUES ( :userId, :message, NOW())');
        $query->bindValue(":userId", $userId);
        $query->bindValue(":message", $this->message);
        $query->execute();
        $contactId = $pdo->lastInsertId();

        $status = ['status'=>"OK","msg"=>$contactId];

        emailSender::sendProContactEmail($contactId);
        emailSender::sendClientContactEmail($contactId);

        return json_encode($status);

    }

    public function getMessage($id)
    {
        $pdo = newDatabase();

    }

}