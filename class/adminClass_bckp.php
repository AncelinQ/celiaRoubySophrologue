<?php

require_once "connection/bdd_connection.php";
//POUR LES TESTS LE LOGIN EST admin ET LE MOT DE PASSE password//
Class adminClass {

    /**
     * CLASSE PERMETTANT LA CONNEXION EN TANT QU'ADMINISTRATEUR
     * @param $login
     * @param $password
     * @return false|string
     */
    public static function login($login, $password){

        //ON ENCODE LES LOGIN ET PASSWORD INSÉRÉS EN SHA512//
        $loginEncode = hash('sha512', $login);
        $passwordEncode = hash('sha512', $password);

        //ON VERIFIE DANS LA BASE DE DONNÉE SI CELA CORRESPOND AUX LOGIN ET PASSWORD DE L'ID ADMIN//
        $pdo = newDatabase();

        $query = $pdo->prepare('SELECT id FROM admin WHERE login = :login AND password = :password');
        $query->bindValue(":login", $loginEncode);
        $query->bindValue(":password", $passwordEncode);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);

        //SI IL Y A DES DONNÉES, ON REGARDE SI ELLES CORRESPONDENT//
        if($data != false){
            $loginId=$data['id'];
            //SI OUI ON ACTIVE $_SESSION ET ON RETOURNE OK//
            if($data['id'] === "113HdB* "){
                $_SESSION['id']=$loginId;
                $status=["status"=>"OK","msg"=>"Connexion ok"];
            }
            //S'IL Y A DES DONNÉES QUI NE CORRESPONDENT PAS, CE N'EST PAS NORMAL, ON RETOURNE UNE ERREUR INTERNE.//
            else{
                $status=["status"=>"KO","msg"=>"Erreur interne"];
            }
        }
        //S'IL N'Y A PAS DE DONNÉES, C'EST QUE LES IDENTIFIANTS ENVOYÉS SONTE ERRONNÉS, ON RETOURNE UNE ERREUR DE CONNEXION //
        else{
            $status=["status"=>"KO","msg"=>"Erreur de connexion"];
        }

        return json_encode($status);

    }

}