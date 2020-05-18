<?php

class calendarClass {

    public $days = ['lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.', 'dim.'];
    public $fullDays = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
    public $months = ['jan.', 'fév.', 'mar.', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
    public $fullMonths = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    public $week;
    public $month;
    public $year;


    /**
     * calendarClass constructor.
     * @param int $month le mois compris enre 1 et 12
     * @param int $year l'année
     * @param int $week la semaine
     */
    public function __construct(?int $week = null, ?int $month = null, ?int $year = null)
    {
        if ($month === null || $month < 1 || $month > 12){
            $month = intval(date("m"));
        }
        if ($year === null){
            $year = intval(date("Y"));
        }
        if ($week === null || $week < 1 || $week > 52 ){
            if (date('N')== 5 || date('N')== 6 || date('N')== 7){
                $week = intval(date("W"))+1;
            }else{
                $week = intval(date("W"));
            }
        }

        $this->month = $month;
        $this->year = $year;
        $this->week = $week;
    }

    /**
     * RETOURNE LA DATE D'AUJOURD'HUI AU FORMAT STRING
     * @return string
     */
    public function getDayToday(){

        $day = date('d');
        $month = date('m');
        $year= date('Y');
        return $year.'-'.$month.'-'.$day;

    }

    /**
     * récupère le premier jour de chaque mois.
     * @return DateTime
     * @throws Exception
     */
    public function getFirstDay(){

        return new DateTime("{$this->year}-{$this->month}-01");

    }

    /**
     * RETOURNE LE JOUR DE LA SEMAINE SELECTIONNÉ AU FORMAT DATETIME
     * @param $dayIndex
     * @return DateTime
     * @throws Exception
     */
    public function getWeekDay($dayIndex)
    {
        $week=$this->week;
        $day = $dayIndex;
        if($week < 10) {
            $week = '0'. $this->week;
        }
        $date = strtotime($this->year ."W". $week . $day);
        $dayNumber = date('d', $date);
        $month = date('n', $date);
        $year = date('Y', $date);
        return new DateTime("{$year}-{$month}-{$dayNumber}");
    }

    /**
     * VERIFIE SI C'EST UN JOUR DE WEEK END : VENDREDI, SAMEDI, OU DIMANCHE
     * @return bool
     */
    public function weekendCheck(){
        if(date('N')==5 || date('N')==6 || date('N')==7){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * DANS LE CAS OU ON EST UN JOUR DE WEEK END, ON VERIFIE SI LE LUNDI DE LA SEMAINE AFFICHÉE EST CELUI QUI ARRIVE JUSTE APRÈS LE WEEK END DE LA DATE DU JOUR OU CELUI D'UNE AUTRE SEMAINE ET SI IL EST INCLU DANS LE MÊME MOIS QU'AUJOURD'HUI.
     * @return bool
     */
    public function mondayCheck(){

        $todayName = date('D');
        $todayNum = intval(date('d'));
        $monday = intval(date('d', $this->date(1)));

        if(($todayName =='Fri' && $monday === ($todayNum+3)) || ($todayName =='Sat' && $monday === ($todayNum+2)) || ($todayName=='Sun' && $monday === ($todayNum+1) ) || ($todayNum>27 && $monday<4)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * CONVERTI LA DATE DU JOUR EN STRING
     * @param $day
     * @return string
     */
    public function getDayToString($day){
        $month = $this->month;
        $year = $this->year;
        if($month < 10) {
            $month = '0'. $this->month;
        }
        return $year.'-'.$month.'-'.$this->getDayNumber($day);
    }

    /**
     * retourne la semaine affichée du lundi au vendredi en tableau
     * @return array
     * @throws Exception
     */
    public function getMondayToFriday(){
        $week =[];
        for ($day= 1; $day<=5; $day++ ){
            $weekDay = $this->getWeekDay($day);
           array_push($week, $weekDay);
        }
        return $week;
    }



    /**
     * retourne la date en fonction du jour au format timestamp
     * @param $day
     * @return false|int
     */
    public function date($day)
    {
        $week=$this->week;
        if($week < 10) {
            $week = '0'. $this->week;
        }

        $date = strtotime($this->year."W".$week.$day);

        return $date;
    }

    /**
     * renvoie le jour au format 01 - 31
     * @param $day
     * @return string
     */
    public function getDayNumber($day): string
    {

        $dayNumber = date('d', $this->date($day));

        return $dayNumber;
    }

    /**
     * renvoie le jour au format lundi - dimanche
     * @param $day
     * @return string
     */
    public function getDayName($day): string
    {
        $dayName = $this->fullDays[$day-1];

        return $dayName;
    }
    /**
     * renvoie le jour au format lun. - dim.
     * @param $day
     * @return string
     */
    public function getDayNameCal($day): string
    {
        $dayName = $this->days[$day-1];

        return $dayName;
    }

    /**
     * renvoie le mois au format 01 - 12 (pour insertion SQL DATETIME)
     * @param $day
     * @return false|string
     */
    public function getMonthNumberForId($day)
    {
        return date('m', $this->date($day));

    }
    /**
     * renvoie le mois au format 1 - 12 (index pour array $months)
     * @param $day
     * @return int
     */
    public function getMonthNumber($day): int
    {
        $monthNumber = intval(date('n', $this->date($day)));

        return $monthNumber;
    }

    /**
     * renvoie le mois au format janvier - décembre
     * @param $day
     * @return string
     */
    public function getMonthName($day): string
    {

        return $this->fullMonths[$this->getMonthNumber($day)-1];
    }

    /**
     * renvoie le mois au format jan. - déc.
     * @param $day
     * @return string
     */
    public function getMonthNameCal($day): string
    {

        return $this->months[$this->getMonthNumber($day)-1];
    }

    /**
     * renvoie l'année
     * @param $day
     * @return int
     */
    public function getYear($day): int
    {
        return date('Y', $this->date($day));
    }

    /**
     * RETOURNE L'ID D'UN CRÉNEAU SELON LE JOUR ENVOYÉ EN PARAMETRE (EN STRING AU FORMAT ANNÉE-MOIS-JOUR).
     * @param $day
     * @return string
     */
    public function getTimeSlotId($day){

        return $this->getYear($day).'-'.$this->getMonthNumberForId($day).'-'.$this->getDayNumber($day);
    }

    /**
     * RETOURNE LE NUMERO PREMIÈRE SEMAINE DU MOIS
     * @return int
     * @throws Exception
     */
    public function getWeeksStart() : int {
        //ON RÉCUPÈRE LE PREMIER JOUR DU MOIS//
        $start = $this->getFirstDay();
        return intval($start->format('W'));
    }

    /**
     * RETOURNE LE NUMERO DE LA DERNIÈRE SEMAINE DU MOIS
     * @return int
     * @throws Exception
     */
    public function getWeeksEnd() : int {
        //ON RÉCUPÈRE LE PREMIER JOUR DU MOIS//
        $start = $this->getFirstDay();
        //ON LE CLONE ET LE MODIFIE POUR AVOIR LE DERNIER JOUR DU MOIS//
        $end = (clone $start)->modify("+1 month -1 day");
        return intval($end->format('W'));
    }


    /**
     * renvoie la semaine suivante
     * @return calendarClass
     * @throws Exception
     */
    public function nextWeek() : calendarClass
    {
        //ON AJOUTE 1 AU NUMERO DE SEMAINE AFFICHÉ//
        $week = $this->week + 1;
        $month = $this->month;
        $year = $this->year;
        $weeksEnd = $this->getWeeksEnd();

        //SI LE NUMERO DE LA SEMAINE EST SUPÉRIEUR AU NUMÉRO DE LA DERNIÈRE SEMAINE DU MOIS, ON AJOUTE 1 AU MOIS//
       if($week > $weeksEnd){
             $month += 1;
         }
        //SI LE NUMERO DE LA SEMAINE EST SUPÉRIEUR À 52 ON RAJOUTE 1 A L'ANNÉE, LES NUMEROS DE LA SEMAINE ET DU MOIS SONT REINITIALISÉS ET REMIS À 1//
        if($week > 52){
            $year += 1;
            $week = 1;
            $month = 1;
        }
        return new calendarClass($week, $month, $year);
    }
    /**
     * renvoie la semaine précédente
     * @return calendarClass
     * @throws Exception
     */
    public function previousWeek() : calendarClass
    {
        //ON ÔTE 1 AU NUMERO DE SEMAINE AFFICHÉ//
        $week = $this->week - 1;
        $month = $this->month;
        $year = $this->year;
        $weeksStart = $this->getWeeksStart();

        //SI LE NUMERO DE LA SEMAINE EST INFÉRIEUR AU NUMÉRO DE LA PREMIÈRE SEMAINE DU MOIS, ON ÔTE 1 AU MOIS//
        if($week < $weeksStart){
            $month -= 1;
        }
        //SI LE NUMERO DE LA SEMAINE EST INFÉRIEUR À 1 ON ÔTE 1 A L'ANNÉE, LE NUMERO DE LA SEMAINE PASSE À 52 ET DU CELUI DU MOIS À 12//
        if($week < 1){
            $week = 52;
            $month = 12;
            $year -= 1;
        }
        return new calendarClass($week, $month, $year);
    }

}

