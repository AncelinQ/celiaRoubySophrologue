<?php

require_once "connection/bdd_connection.php";
require_once "contactClass.php";
require_once 'emailSenderClass.php';

Class rdvClass extends contactClass{

    protected $timeSlotDateTime;
    protected $timeSlotFull;
    protected $motif;

    /**
     * ON INSTENCIE LA CLASSE À PARTIR DE LA CLASSE PARENTE AVEC LES PRENOM, NOM, TELEPHONE, EMAIL, MOTIF, CRÉNEAU AU FORMAT DATETIME, CRÉNEAU AU FORMAT TEXTE PLEIN ET LE MESSAGE DE L'UTILISATEUR
     * rdvClass constructor.
     * @param $firstName
     * @param $lastName
     * @param $phone
     * @param $email
     * @param $message
     * @param $motif
     * @param $timeSlotDateTime
     * @param $timeSlotFull
     */
    public function __construct($firstName, $lastName, $phone, $email, $message, $motif, $timeSlotDateTime, $timeSlotFull)
    {
        parent::__construct($firstName, $lastName, $phone, $email, $message);

        $this->timeSlotDateTime = $timeSlotDateTime;
        $this->timeSlotFull = $timeSlotFull;
        $this->motif = $motif;
    }

    /**
     *  ON CRÉE UN NOUVEAU RENDEZ VOUS EN INSÉRANT LES INFOS DANS LA BASE DE DONNÉE ET ON ENVOIE UN MAIL RÉCAPITULATIF À L'UTILISATEUR ET AU PROFESSIONNEL, ON RETOURNE ENFIN LE STATUT
     * @return false|string
     */
    public function newRdv()
    {

        $pdo = newDatabase();

        $query = $pdo->prepare('INSERT INTO rdv (firstName, lastName, phone, email, motif, timeSlotDateTime, timeSlotFull, message, creationTimestamp)
        VALUES ( :firstname, :lastname, :phone, :email, :motif, :timeSlotDateTime, :timeSlotFull, :message, NOW())');
        $query->bindValue(":firstname", $this->firstName);
        $query->bindValue(":lastname", $this->lastName);
        $query->bindValue(":phone", $this->phone);
        $query->bindValue(":email", $this->email);
        $query->bindValue(":motif", $this->motif);
        $query->bindValue(":timeSlotDateTime", $this->timeSlotDateTime);
        $query->bindValue(":timeSlotFull", $this->timeSlotFull);
        $query->bindValue(":message", $this->message);
        $query->execute();
        $rdvId = $pdo->lastInsertId();

        $status = ['status'=>"OK","msg"=>$rdvId];

        emailSender::sendProRdvEmail($rdvId);
        emailSender::sendClientRdvEmail($rdvId);

        return json_encode($status);
    }

    /**
     * ON RECUPÈRE TOUS LES RENDEZ VOUS SITUÉS ENTRE LE DATETIME $START ET LE DATETIME $END
     * @param Datetime $start
     * @param Datetime $end
     * @return array
     */
    public static function getRdvsBetween(Datetime $start, Datetime $end)
    {

        $pdo = newDatabase();

        $query = $pdo->query("SELECT * from rdv WHERE timeSlotDateTime BETWEEN '{$start->format('Y-m-d 00:00:00')}' AND '{$end->format('Y-m-d 23:59:59')}'");

        return $query->fetchAll();
    }

    /**
     * ON ORGANISE PAR JOUR TOUS LES RENDEZ VOUS SITUÉS ENTRE LE DATETIME $START ET LE DATETIME $END
     * @param Datetime $start
     * @param Datetime $end
     * @return array
     */
    public static function getRdvsBetweenByDay(Datetime $start, Datetime $end)
    {
        $rdvs = self::getRdvsBetween($start, $end);
        $days = [];

        //POUR CHAQUE RDV TROUVÉ ON REGARDE LA COLONNE AVEC LES INFORMATIONS DU CRÉNEAU EN DATETIME PUIS S'IL N'EST PAS DÉJÀ DANS LE TABLEAU $DAYS, ON L'Y AJOUTE ET ON RETOURNE LE TABLEAU//
        foreach ($rdvs as $rdv){
            $timeSlot = $rdv['timeSlotDateTime'];
            if(!isset($days[$timeSlot])){
                $days[$timeSlot] = [$rdv];
            }
            else
                //SI LE CRENEAU EXISTE DÉJÀ DANS LE TABLEAU ON RETOURNE UNE ERREUR//
            {
                echo "ERREUR INTERNE --- CE CRÉNEAU EST DÉJÀ PRIS.";
            }
        }
        return $days;
    }

    /**
     * ON RECUPÈRE LES INFORMATIONS D'UN RDV EN PARTICULIER PAR SON CRENEAU EN DATETIME
     * @param $timeSlot
     * @return array|bool
     */
    public static function getRdv($timeSlot)
    {

        $pdo = newDatabase();

        $query = $pdo->prepare("SELECT * FROM rdv WHERE timeSlotDateTime = :timeSlot");
        $query->bindValue(":timeSlot", $timeSlot);
        $query->execute();
        $data = $query->fetchAll();
        if($data != false){
            return $data;
        }
        else{
            return false;
        }
    }

    /**
     * ON RETOURNE LES INFOS DU RDV SELECTIONNÉ EN JSON
     * @param $timeSlot
     * @return false|string
     */
    public static function sendToJsonRdvData($timeSlot)
    {

        $rdvData = self::getRdv($timeSlot);

        if($rdvData != false){
            $rdv = $rdvData[0];
            //ON PROTÈGE D'UN HACK LA LECTURE DES DONNÉES À LA RÉCUPÉRATION//
            $firstName = htmlspecialchars($rdv['firstName']);
            $lastName = htmlspecialchars($rdv['lastName']);
            $phone = htmlspecialchars($rdv['phone']);
            $email = htmlspecialchars($rdv['email']);
            $motif = htmlspecialchars($rdv['motif']);
            $infos = ["firstName"=>$firstName,"lastName"=>$lastName,"phone"=>$phone,"email"=>$email,"motif"=>$motif];
            //SI L'UTILISATEUR A AJOUTÉ UN MESSAGE  ON L'AJOUTE//
            if($rdv['message'] != ""){
                $message = htmlspecialchars($rdv['message']);
                $infos += ["message"=>$message];
            }
        } else{
            $infos =["KO"=>"Il n'y a pas de rendez-vous enregistré sur ce créneau."];
        }

        return json_encode($infos);
    }

    /**
     * ON AJOUTE OU ON SUPPRIME À LA TABLE 'UNAVAILABLE' DES CRÉNEAUX PAR JOUR
     * @param $thisDay
     * @return bool
     */
    public static function switchDayAvailability($thisDay)
    {
        $pdo = newDatabase();

        //ON DÉFINI UN TABLEAU AVEC LES DIFFÉRENTS CRÉNEAUX JOURNALIERS EN MODE FORMAT 'TIME'//
        $TimeSlotArray = ['09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00'];

        //ON RÉCUPÈRE TOUS LES CRÉNEAUX INDISPONIBLES DU JOUR $THISDAY ENVOYÉ//
        $query = $pdo->prepare("SELECT * FROM unavailable WHERE `date` = :thisDay");
        $query->bindValue(":thisDay", $thisDay);
        $query->execute();
        $data = $query->fetchAll();

        //SI ON TROUVE DES CRÉNEAUX, ON LES SUPPRIME DE LA TABLE (ILS DEVIENNENT DISPONIBLES) ET ON RETOURNE TRUE//
        if ($data != false) {

            $query = $pdo->prepare("DELETE FROM unavailable WHERE `date` = :thisDay");
            $query->bindValue(":thisDay", $thisDay);
            $query->execute();
            return true;
        }
        //S'IL N'Y EN A PAS ON PRÉPARE LEUR INSERTION ET ON RETOURNE FALSE //
        else {
            foreach ($TimeSlotArray as $thisTimeSlot){
                //ON VERIFIE SI DES RDVS NE SONT PAS ENREGISTRÉS SUR CES CRÉNEAUX//
                $checkRdv = self::getRdv($thisDay.' '.$thisTimeSlot);

                //SI LES CRÉNEAUX SONT VIDES, ON LES INSERT (ILS DEVIENNENT DONC INDISPONIBLES)//
                if($checkRdv === false) {
                    $query = $pdo->prepare("INSERT INTO unavailable (`date`, `time`, creationTimestamp) VALUES (:thisDay, :thisTimeSlot, NOW())");
                    $query->bindValue(":thisDay", $thisDay);
                    $query->bindValue(":thisTimeSlot", $thisTimeSlot);
                    $query->execute();
                }
            }
            return false;
        }
    }

    /**
     * ON VÉRIFIE SI LE JOUR SÉLECTIONNÉ A DES CRÉNEAUX DISPONIBLES OU INDISPONIBLES, RETOURNE FALSE OU BIEN UN TABLEAU AVEC TOUS LES CRÉNEAUX INDISPONIBLES DE CE JOUR
     * @param $thisDay
     * @return array|bool
     */
    public static function getDayAvailability($thisDay)
    {
        $pdo = newDatabase();

        $query = $pdo->prepare("SELECT * FROM unavailable WHERE `date` = :thisDay");
        $query->bindValue(":thisDay", $thisDay);
        $query->execute();
        $data = $query->fetchAll();
        if ($data != false) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * ON ENVOIE EN JSON LES DONNÉES DE DISPONIBILITÉ DU JOUR SELECTIONNÉ.
     * @param $dayToSwitch
     * @return false|string
     */
    public static function sendToJsonDayToSwitch($dayToSwitch)
    {

        $dayToSwitchData = self::switchDayAvailability($dayToSwitch);
        $dayRdvs = self::getDayAvailability($dayToSwitch);

        //SI $DAYTOSWITCH EST TRUE, LE JOUR EST DISPONIBLE//
        if ($dayToSwitchData === true) {

            $status =["status"=>"avDay", "msg"=>"Journée activée"];

        }
        //SINON LE JOUR EST DONC (ENTIEREMENT OU PARTIELLEMENT) INDISPONIBLE//
        else{
            //SI IL N'Y A PAS 8 ELEMENTS DANS LE TABLEAU, IL N'Y A PAS 8 CRÉNEAUX INDISPONIBLES, IL Y A DONC UN RDV D'ENREGISTRÉ//
            if(!isset($dayRdvs[7])){
                $status = ["status" => "rdvDay", "msg" => "Des rendez vous sont enregistrés ce jour-ci"];
            }
            //SINON TOUTE LA JOURNÉE EST INDISPONIBLE//
            else{
                $status = ["status"=>"unavDay", "msg"=>"Journée désactivée"];
            }
        }
        return json_encode($status);
    }

    /**
     * SEPARE LES DONNÉES DU CRENEAU DE DATETIME VERS DATE ET TIME
     * @param $fullTimeSlot
     * @return array
     */
    public static function splitTimeSlot($fullTimeSlot)
    {
        return explode(' ',$fullTimeSlot);
    }

    /**
     * ON RECUPÈRE LES DONNÉES DU CRENEAU SELECTIONNÉ, S'IL EST DISPONIBLE OU NON (ABSENT OU PRÉSENT DANS LA TABLE 'UNAVAILABLE')
     * @param $fullTimeSlot
     * @return array|bool
     */
    public static function getTimeSlotAvailability($fullTimeSlot)
    {

        $pdo = newDatabase();

        $thisDay = self::splitTimeSlot($fullTimeSlot)[0];
        $thisTimeSlot = self::splitTimeSlot($fullTimeSlot)[1];

        $query = $pdo->prepare("SELECT * FROM unavailable WHERE `date` = :thisDay AND `time`= :thisTimeSlot");
        $query->bindValue(":thisDay", $thisDay);
        $query->bindValue(":thisTimeSlot", $thisTimeSlot);
        $query->execute();
        $data =  $query->fetchAll();
        if ($data != false) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * SELON QUE LE CRÉNEAU SELECTIONNÉ EST DISPONIBLE OU NON, ON L'INSERT OU ON LE SUPPRIME DE LA TABLE 'UNAVAILABLE'
     * @param $fullTimeSlot
     * @return bool
     */
    public static function switchTimeSlotAvailability($fullTimeSlot)
    {
        $pdo = newDatabase();
        $data = self::getTimeSlotAvailability($fullTimeSlot);
        $thisDay = self::splitTimeSlot($fullTimeSlot)[0];
        $thisTimeSlot = self::splitTimeSlot($fullTimeSlot)[1];

        if($data != false){

            $query = $pdo->prepare("DELETE FROM unavailable WHERE `date` = :thisDay AND `time`= :thisTimeSlot");
            $query->bindValue(":thisDay", $thisDay);
            $query->bindValue(":thisTimeSlot", $thisTimeSlot);
            $query->execute();
            return true;
        }
        else{
            $query = $pdo->prepare("INSERT INTO unavailable (`date`,`time`, creationTimestamp) VALUES (:thisDay, :thisTimeSlot, NOW())");
            $query->bindValue(":thisDay", $thisDay);
            $query->bindValue(":thisTimeSlot", $thisTimeSlot);
            $query->execute();
            return false;
        }
    }

    /**
     * ON ENVOIE EN JSON LE STATUT DE DISPONIBILITÉ DU CRÉNEAU
     * @param $timeSlotToSwitch
     * @return false|string
     */
    public static function sendToJsonTimeSlotToSwitch($timeSlotToSwitch)
    {

        $timeSlotToSwitchData = self::switchTimeSlotAvailability($timeSlotToSwitch);

        if ($timeSlotToSwitchData == true) {

            $status = ["status"=>"avTime", "msg"=>"créneau activé"];

        } else{

            $status = ["status"=>"unavTime", "msg"=>"créneau désactivé"];
        }
        return json_encode($status);
    }

    /**
     * SUPPRIME LE RDV SELECTIONNÉ
     * @param $rdvToDelete
     * @return bool
     */
    public static function deleteRdv($rdvToDelete)
    {

        $pdo = newDatabase();

        $query = $pdo->prepare("DELETE FROM rdv WHERE timeSlotDateTime = :thisTimeSlot");
        $query->bindValue(":thisTimeSlot", $rdvToDelete);
        $query->execute();
        return true;

    }

    /**
     * ON GÈRE LA SUPPRESSION ET L'ENVOI EN JSON DES DONNÉES SUR LE RDV À SUPPRIMER
     * @param $rdvToDelete
     * @return false|string
     */
    public static function sendToJsonRdvToDelete($rdvToDelete)
    {
        $rdvToDeleteData = self::getRdv($rdvToDelete);

        //SI IL Y A BIEN UN RDV ENREGISTRÉ SUR CE CRÉNEAU ON ENVOIE DES MAILS AU CLIENT ET AU PROFESSIONNEL (AVANT DE SUPPRIMER EFFECTIVEMENT LE RDV !!)//
        if ($rdvToDeleteData != false){

            $mailToClient = emailSender::rdvDeleteMailToClient($rdvToDelete);
            $mailToPro = emailSender::rdvDeleteMailToPro($rdvToDelete);

            //SI L'ENVOI DES MAILS S'EFFECTUE BIEN, ON SUPPRIME LE RDV//
            if ($mailToClient === true && $mailToPro === true ) {
                $rdvDelete = self::deleteRdv($rdvToDelete);

                //SI LA FONCTION RETOURNE TRUE ON RETOURNE SUCCESS//
                if ($rdvDelete === true) {
                    $status = ["status"=>"success", "msg"=>"Le rendez vous a bien été annulé."];
                }
                //SI ELLE NE RETOURNE PAS TRUE, C'EST ANORMAL, IL Y A UNE ERREUR INTERNE//
                else
                {
                    $status = ["status"=>"error", "msg"=>"ERREUR INTERNE --- Le rendez vous n'a pas pu être annulé."];
                }
            }
            //SI LE MAIL CLIENT NE S'EST PAS ENVOYÉ, ON RETOURNE UN PROBLÈME//
            else if ($mailToClient === false && $mailToPro === true ){
                $status = ["status"=>"mailError", "msg"=>"Problème d'envoi de mail au particulier, RDV maintenu."];
            }
            //SI LE MAIL PRO NE S'EST PAS ENVOYÉ, ON RETOURNE UN PROBLÈME//
            else if ($mailToClient === true && $mailToPro === false ) {
                $status = ["status" => "mailError", "msg" => "Problème d'envoi de mail au professionnel, RDV maintenu."];
            }
            //SI AUCUN MAIL NE S'EST PAS ENVOYÉ, ON RETOURNE UN PROBLÈME//
            else {
                $status = ["status"=>"mailError", "msg"=>"Problème d'envoi des mails, RDV maintenu."];
            }
        }
        //SI IL N'Y A PAS DE DONNÉ, LE RDV N'EXISTE PAS//
        else{
            $status = ["status"=>"notFound", "msg"=>"Ce rendez-vous semble ne pas exister."];
        }

        return json_encode($status);
    }

}