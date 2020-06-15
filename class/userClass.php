<?php

require_once "connection/bdd_connection.php";
require_once 'userClass.php';
require_once 'emailSenderClass.php';

Class userClass {

    protected $userId;
    protected $firstName;
    protected $lastName;
    protected $phone;
    protected $email;
    protected $nbOfSeances;

    /**
     * ON INSTENCIE LA CLASSE AVEC LES PRENOM, NOM, TELEPHONE, ET L'EMAIL DE L'UTILISATEUR
     * userClass constructor.
     * @param $firstName
     * @param $lastName
     * @param $phone
     * @param $email
     * @param $nbOfSeances
     */
    public function __construct($firstName, $lastName, $phone, $email, ?int $nbOfSeances = null)
    {
        $this->firstName = strtolower(trim(htmlspecialchars($firstName)));
        $this->lastName = strtolower(trim(htmlspecialchars($lastName)));
        $this->phone = trim($phone);
        $this->email = trim($email);
        $this->nbOfSeances = $nbOfSeances;
    }

    public function setUserId($val) {
        $this->contactId = $val;
    }

    public function setFirstName($val) {
        $this->firstName = $val;
    }

    public function setLastName($val) {
        $this->lastName = $val;
    }

    public function setPhone($val) {
        $this->phone = $val;
    }

    public function setEmail($val) {
        $this->email = $val;
    }

    public function setNbOfSeances($val) {
        $this->nbOfSeances = $val;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getEmail() {
        return $this->email;
    }
    
    public function getNbOfSeances() {
        return $this->nbOfSeances;
    }

    public function checkUser(){

        $pdo = newDatabase();

        $query = $pdo->prepare('SELECT * FROM `user` WHERE (firstName = :firstName) AND (lastName = :lastName) AND (phone = :phone) AND (email = :email)');
        $query->bindValue(":firstName", $this->firstName);
        $query->bindValue(":lastName", $this->lastName);
        $query->bindValue(":phone", $this->phone);
        $query->bindValue(":email", $this->email);
        $query->execute();
        $userData = $query->fetchAll();

        if($userData != false) {
            return $userData[0];
        }
        else{
            return false;
        }

    }

    public function newUser(){
        $userCheck = $this->checkUser();
        
        if ($userCheck != false){
            $status = ['status'=>"Exists","msg"=>$userCheck['id']];
        } 
        else
        {
            $pdo = newDatabase();

            $query = $pdo->prepare('INSERT INTO `user` (firstName, lastName, phone, email, creationTimestamp)
            VALUES ( :firstname, :lastname, :phone, :email, NOW())');
            $query->bindValue(":firstname", $this->firstName);
            $query->bindValue(":lastname", $this->lastName);
            $query->bindValue(":phone", $this->phone);
            $query->bindValue(":email", $this->email);
            $query->execute();
            $userId = $pdo->lastInsertId();
            if ($userId != false){
                $status = ['status'=>"OK","msg"=>$userId];
            }
            else {
                $status = ['status'=>"Error","msg"=>"Erreur interne : l'utilisateur n'a pas été enregistré"];
            }
        }
        return $status;
    }


    public function getUser($search){

        $pdo = newDatabase();

        $search = trim(strtolower($search));

        if (strpos($search, ' ') != false){

            $searchArray = explode(' ',$search);
            $searchArrayLength = count($searchArray);
            $i = 0;
            $results = [];

            while ($i < $searchArrayLength){
                $search = $searchArray[$i];
                $query = $pdo->prepare('SELECT * FROM `user` WHERE (firstName LIKE %:search%) OR (lastName LIKE %:search%) OR (phone LIKE %:search%) OR (email LIKE %:search%) ORDER BY lastName DESC)');
                $query->bindValue(":search", $search);
                $query->execute();
                $result = $query->fetchAll();

                if($result != false) {
                    array_push($results, $result);
                }

            }
            return $results;
        }

        else {
            $query = $pdo->prepare('SELECT * FROM `user` WHERE (firstName LIKE %:search%) OR (lastName LIKE %:search%) OR (phone LIKE %:search%) OR (email LIKE %:search%) ORDER BY lastName DESC)');
            $query->bindValue(":search", $search);
            $query->execute();
            return $query->fetchAll();
        }
    }
}