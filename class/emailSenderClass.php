<?php

require_once "connection/bdd_connection.php";


class emailSender {

    //CONST TO À CHANGER, C'EST ICI MON ADRESSE MAIL POUR LES TESTS, CONST FROM À MODIFIER, ELLE N'EXISTE PAS POUR LE MOMENT//
    const TO = 'celia.rouby@gmail.com';
    const FROM = 'From: Célia Rouby - Sophrologue <celia.rouby@gmail.com>';
    const HEADERS = 'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=UTF8' . "\r\n";
    const SUBJECT = 'Message envoyé à Célia Rouby Sophrologue.';
    

    /**
     * Récupère les données du contact dans la table "contact" de la base de donnée d'après l'Id.
     * @param $contactId
     * @return array
     */
    public static function getContactMailData($contactId)
    {
        $pdo = newDatabase();
        $query = $pdo->prepare("SELECT * from contact WHERE id =  :id");
        $query->bindValue(':id', $contactId);
        $query->execute();
        $data = $query->fetchAll();

        return $data[0];
    }

    /**
     * Prépare les données à injecter dans les templates d'emails de contact.
     * $dest servira à définir l'utilisation du template 'Client' ou 'Pro'.
     * @param $contactId
     * @param $dest
     * @return false|string|string[]
     */
    public static function createContactMail($contactId, $dest)
    {
        $contactData = self::getContactMailData($contactId);
        $message = file_get_contents('emails/to'.$dest.'ContactMail.phtml');

        //ON REMPLACE CHAQUE ESPACE ENTOURÉ PAR DES {{}} DANS LE TEMPLATE PAR LA VALEUR VENANT DE LA TABLE 'CONTACT'. ON PROTÈGE DU HACK LES VALEURS À LA LECTURE//
        foreach ($contactData as $key => $value) {
            if($key === 'firstName' || $key === 'lastName' ){
                $value = ucwords($value);
            }
            $message = str_replace('{{ ' . $key . ' }}', htmlspecialchars($value), $message);
        }
        $message = nl2br($message);
        return $message;
    }


    /**
     * Injecte les données dans le template d'email de contact 'Pro' et l'envoie. Retourne true une fois effectué.
     * ON PROTÈGE DU HACK LES SUBJECT ET HEADERS.
     * @param $contactId
     * @return bool
     */
    public static function sendProContactEmail($contactId)
    {
        $contactData = self::getContactMailData($contactId);
        $message = self::createContactMail($contactId, 'Pro');
        $subject = 'Nouveau message de ' . ucwords($contactData['firstName']) . ' ' . ucwords($contactData['lastName']) . '.';
        $headers = self::HEADERS;
        $headers .= 'From:'.$contactData['email'];

        $mail = mail(self::TO, htmlspecialchars($subject), $message, htmlspecialchars($headers));

        if ($mail){
            return true;
            
        } else{
            return false;
        }
    }

    /**
     * Injecte les données dans le template d'email de contact 'Client' et l'envoie. Retourne true une fois effectué.
     * ON PROTÈGE DU HACK LE DESTINATAIRE.
     * @param $contactId
     * @return bool
     */
    public static function sendClientContactEmail($contactId)
    {
        $contactData = self::getContactMailData($contactId);
        $message = self::createContactMail($contactId, 'Client');
        $to = $contactData['email'];
        $headers = self::HEADERS;
        $headers .= self::FROM;

        $mail = mail(htmlspecialchars($to), self::SUBJECT, $message, $headers);

        if ($mail){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Récupère les données du contact dans la table "rdv" de la base de donnée d'après l'Id.
     * @param $rdvId
     * @return array
     */
    public static function getRdvMailData($rdvId)
    {
        $pdo = newDatabase();

        $query = $pdo->prepare("SELECT * from rdv WHERE id =  :id");
        $query->bindValue(':id', $rdvId);
        $query->execute();
        $data = $query->fetchAll();

        return $data[0];
    }

    /**
     * Prépare les données à injecter dans les templates d'emails de rdv.
     * $dest servira à définir l'utilisation du template 'Client' ou 'Pro'.
     * @param $rdvId
     * @param $dest
     * @return false|string|string[]
     */
    public static function createRdvMail($rdvId, $dest)
    {
        $rdvData = self::getRdvMailData($rdvId);
        $timeSlotArray = explode(' ', $rdvData['timeSlotFull']);
        $timeSlotDayName = $timeSlotArray[0];
        $timeSlotDayNumber = $timeSlotArray[1];
        $timeSlotMonth = $timeSlotArray[2];
        $preMessage = "Message :";
        $timeSlotDateTime = explode(' ', $rdvData['timeSlotDateTime']);
        $timeSlotDateTime = substr($timeSlotDateTime[1], 0, 5);
        $message = file_get_contents('emails/to'.$dest.'RdvMail.phtml');

        //ON REMPLACE CHAQUE ESPACE ENTOURÉ PAR DES {{}} DANS LE TEMPLATE PAR LA VALEUR VENANT DE LA TABLE 'CONTACT'. ON PROTÈGE DU HACK LES VALEURS À LA LECTURE//
        foreach ($rdvData as $key => $value) {
            if($key === 'firstName' || $key === 'lastName' ){
                $value = ucwords($value);
            }
            $message = str_replace('{{ ' . $key . ' }}', htmlspecialchars($value), $message);
        }

        $message = str_replace('{{ jour }}', $timeSlotDayName . ' ' . $timeSlotDayNumber . ' ' . $timeSlotMonth, $message);
        $message = str_replace('{{ heure }}', $timeSlotDateTime, $message);

        //ON AJOUTE L'INDICATION $PREMESSAGE SELON LA PRÉSENCE OU NON D'UN MESSAGE //
        if ($rdvData['message'] != null) {
            $message = str_replace('{{ preMessage }}', $preMessage, $message);
        } else {
            $message = str_replace('{{ preMessage }}', '', $message);
        }
        $message = nl2br($message);
        return $message;
    }

    /**
     * Injecte les données dans le template d'email de rdv 'Pro' et l'envoie. Retourne true une fois effectué.
     *  ON PROTÈGE DU HACK LES SUBJECT ET HEADERS.
     * @param $rdvId
     * @return bool
     */
    public static function sendProRdvEmail($rdvId)
    {
        $rdvData = self::getRdvMailData($rdvId);
        $message = self::createRdvMail($rdvId, 'Pro');
        $subject = 'Nouveau rendez-vous avec ' . ucwords($rdvData['firstName']) . ' ' . ucwords($rdvData['lastName']) . '.';
        $headers = self::HEADERS;
        $headers .= 'From: ' . $rdvData['email'];

        $mail = mail(self::TO, htmlspecialchars($subject), $message, htmlspecialchars($headers));

        if ($mail){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Injecte les données dans le template d'email de rdv 'Client' et l'envoie. Retourne true une fois effectué.
     *  ON PROTÈGE DU HACK LE DESTINATAIRE.
     * @param $rdvId
     * @return bool
     */
    public static function sendClientRdvEmail($rdvId)
    {
        $rdvData = self::getRdvMailData($rdvId);
        $message = self::createRdvMail($rdvId, 'Client');
        $to = $rdvData['email'];
        $headers = self::HEADERS;
        $headers .= self::FROM;

        $mail = mail(htmlspecialchars($to), self::SUBJECT, $message, $headers);

        if ($mail){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Récupère les données du rdv dans la table "rdv" de la base de donnée d'après le créneau sélectionné.
     * @param $timeSlot
     * @return array
     */
    public static function getRdvDeleteMailData($timeSlot)
    {
        $pdo = newDatabase();

        $query = $pdo->prepare("SELECT * from rdv WHERE timeSlotDateTime =  :timeSlot");
        $query->bindValue(':timeSlot', $timeSlot);
        $query->execute();
        $data = $query->fetchAll();

        return $data[0];
    }

    /**
     * Prépare les données à injecter dans les templates d'emails de suppression.
     * $dest servira à définir l'utilisation du template 'Client' ou 'Pro'.
     * @param $timeSlot
     * @param $dest
     * @return false|string|string[]
     */
    public static function createRdvDeleteMailData($timeSlot, $dest)
    {
        $rdvData = self::getRdvDeleteMailData($timeSlot);
        $timeSlotArray = explode(' ', $rdvData['timeSlotFull']);
        $timeSlotDayName = $timeSlotArray[0];
        $timeSlotDayNumber = $timeSlotArray[1];
        $timeSlotMonth = $timeSlotArray[2];
        $timeSlotDateTime = explode(' ', $rdvData['timeSlotDateTime']);
        $timeSlotDateTime = substr($timeSlotDateTime[1], 0, 5);
        $message = file_get_contents('emails/to'.$dest.'RdvDeleteMail.phtml');

        //ON REMPLACE CHAQUE ESPACE ENTOURÉ PAR DES {{}} DANS LE TEMPLATE PAR LA VALEUR VENANT DE LA TABLE 'CONTACT'. ON PROTÈGE DU HACK LES VALEURS À LA LECTURE//
        foreach ($rdvData as $key => $value) {
            if($key === 'firstName' || $key === 'lastName' ){
                $value = ucwords($value);
            }
            $message = str_replace('{{ ' . $key . ' }}', htmlspecialchars($value), $message);
        }

        $message = str_replace('{{ jour }}', $timeSlotDayName . ' ' . $timeSlotDayNumber . ' ' . $timeSlotMonth, $message);
        $message = str_replace('{{ heure }}', $timeSlotDateTime, $message);

        $message = nl2br($message);
        return $message;
    }

    /**
     *Injecte les données dans le template d'email de suppression 'Client' et l'envoie. Retourne true une fois effectué.
     * ON PROTÈGE DU HACK LE DESTINATAIRE.
     * @param $timeSlot
     * @return bool
     */
    public static function rdvDeleteMailToClient($timeSlot)
    {
        $rdvData = self::getRdvDeleteMailData($timeSlot);
        $message = self::createRdvDeleteMailData($timeSlot, 'Client');
        $to = $rdvData['email'];
        $subject = 'Célia Rouby Sophrologue : Rendez-vous annulé.';
        $headers = self::HEADERS;
        $headers .= self::FROM;

        $mail = mail(htmlspecialchars($to), $subject, $message, $headers);

        if ($mail){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Injecte les données dans le template d'email de suppression 'Pro' et l'envoie. Retourne true une fois effectué.
     * ON PROTÈGE DU HACK LE SUBJECT.
     * @param $timeSlot
     * @return bool
     */
    public static function rdvDeleteMailToPro($timeSlot)
    {
        $rdvData = self::getRdvDeleteMailData($timeSlot);
        $message = self::createRdvDeleteMailData($timeSlot, 'Pro');
        $subject = ' Rendez-vous annulé avec ' . ucwords($rdvData['firstName']) . ' ' . ucwords($rdvData['lastName']) . '.';
        $headers = self::HEADERS;
        $headers .= self::FROM;

        $mail = mail(self::TO, htmlspecialchars($subject), $message, $headers);

        if ($mail){
            return true;
        } else{
            return false;
        }
    }
}