<?php

require_once "connection/bdd_connection.php";
require_once 'userClass.php';
require_once 'emailSenderClass.php';

Class contactClass {

    protected $contactId;
    protected $userId;
    protected $message;


    public function __construct($userId, $message)
    {
        $this->userId = $userId;
        $this->message = trim(htmlspecialchars($message));
    }

    public function setContactId($val) {
        $this->contactId = $val;
    }

    public function setUserId($val) {
        $this->userId = $val;
    }

    public function setMessage($message) {
        $this->message = trim(htmlspecialchars($message));
    }

    public function getContactId() {
        return $this->contactId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getMessage() {
        return $this->message;
    }

    public function hydrate($data) {
        /*
        $this->setContactId($data['contactId']);
        $this->setMessage($data['message']);
        */
        // on boucle sur $data
        foreach($data as $propriete => $valeur) { // $propriete (index du tableau) = contactId / $valeur (valeur) = 1
          $methodName = 'set'.ucfirst($propriete); // set + contactId => ContactId  => setContactId - setMessage // on génère le nom de la méthode
          if(method_exists($this, $methodName)) { // si la méthode existe dans la classe courante alors on l'exécute
            $this->$methodName($valeur);
          }
        }
    }

    /**
     * ON CRÉE UN NOUVEAU CONTACT EN INSÉRANT LES INFOS DANS LA BASE DE DONNÉE ET ON ENVOIE UN MAIL RÉCAPITULATIF À L'UTILISATEUR ET AU PROFESSIONNEL, ON RETOURNE ENFIN LE STATUT
     * @return false|string
     */
    public function newMessage()
    {
        $pdo = newDatabase();

        $query = $pdo->prepare('INSERT INTO contact (userId, message, creationTimestamp)
            VALUES ( :userId, :message, NOW())');
        $query->bindValue(":userId", $this->userId);
        $query->bindValue(":message", $this->message);
        $query->execute();
        $contactId = $pdo->lastInsertId();

        $status = ['status'=>"OK","msg"=>$contactId];

        emailSender::sendProContactEmail($contactId);
        emailSender::sendClientContactEmail($contactId);

        return json_encode($status);

    }

}