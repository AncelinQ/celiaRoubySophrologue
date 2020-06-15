<?php

require_once "connection/bdd_connection.php";
require_once 'userClass.php';
require_once 'emailSenderClass.php';

Class patientClass {

    protected $patientId;
    protected $userId;
    protected $nbOfSeances;

    public function __construct($userId, $nbOfSeances)
    {
        $this->userId = $userId;
        $this->nbOfSeances = $nbOfSeances;
    }

    public function setPatientId($val) {
        $this->patientId = $val;
    }

    public function setUserId($val) {
        $this->userId = $val;
    }

    public function setNbOfSeances($val) {
        $this->nbOfSeances = $val;
    }

    public function getPatientId() {
        return $this->patientId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getNbOfSeances() {
        return $this->nbOfSeances;
    }

    public function checkPatient($id)
    {
        $pdo = newDatabase();

        $query = $pdo->prepare('SELECT `nbOfSeances` FROM `user` WHERE id = :id');
        $query->bindValue(":id", $id );
        $query->execute();
        $patient = $query->fetch();

        if($patient != false) {
            return $patient;
        }
        else{
            return false;
        }
    }

    public function getPatient($patientId){

        $pdo = newDatabase();

        $query = $pdo->prepare('SELECT * FROM `user` WHERE id = :id');
        $query->bindValue(":id", $patientId );
        $query->execute();
        $patientData = $query->fetchAll();

        if($patientData != false) {
            return $patientData[0];
        }
        else{
            return false;
        }
    }

    public function newPatient(){

        $pdo = newDatabase();

        $userCheck = $this->checkUser();

        if($userCheck === false){
            $user = $this->newPatient();
            $userId = $user['msg'];
        }else {
            $userId = $this->userId;
        }

            $query = $pdo->prepare('INSERT INTO `user` (userId, nbOfSeances, creationTimestamp)
        VALUES ( :userId, :nbOfSeances, NOW())');
            $query->bindValue(":userId", $userId);
            $query->bindValue(":nbOfSeances", 1);
            $query->execute();
            $patientId = $pdo->lastInsertId();

            if ($patientId != false){
                $status = ['status'=>"OK","id"=>$patientId];
            }
            else {
                $status = ['status'=>"Error","msg"=>"Erreur interne : le patient n'a pas été enregistré"];
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