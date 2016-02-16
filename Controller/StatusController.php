<?php namespace Controller;

use Controller\Client;

class StatusController
{
    private $xmlObject;
    private $platformName;

    public function __construct($platformName)
    {
        $this->platformName = $platformName;

        //$fileName = $platformName.".xml";
        try {
            $this->xmlObject = XMLParser::parseXMLFromFile($this->getXML($platformName));
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
            exit;
        }
    }

    /**
     * Return the status of a given module.
     * @param $moduleIdentifier
     * @return bool|string
     */
    public function resolveModule($moduleIdentifier)
    {
        switch($moduleIdentifier) {
            case "bearer_box":
                return $this->getBearerBox();
            case "sms":
                return $this->getSMS();
            case "dlr":
                return $this->getDLR();
            case "sms_box":
                return $this->getSMSBox();
            case "sql_box":
                return $this->getSQLBox();
            case "boxes":
                return $this->getBoxStatus();
            case "sms_c":
                return $this->getSMSC();
        }

        return "Module Name Not Recognized";
    }

    /**
     * Returns the bearer box status.
     * @return string
     */
    public function getBearerBox()
    {
        $status = substr($this->xmlObject->status, 0, 1) === "r" ? "Running":"Stopped";
        $upTime =substr(stristr($this->xmlObject->status, "time"), 4);
        return array(
            "status" => $status,
            "uptime" => $upTime
        );
    }

    public function getGatewayUptime()
    {
        return (string) $this->xmlObject->status;
    }

    /**
     * Returns 1 if this box is online nd 0 if it is offline
     * @return int
     */
    public function getSMSBox()
    {
        $isLive = false;
        foreach ($this->xmlObject->boxes->box as $b) {
            if ($b) {
                $boxId = (string) $b->id;
                if ($boxId === "thesmsbox") {
                    $status = (string) $b->status;
                    if (substr($status, 0, 2) === "on")
                        $isLive = substr($status, 8);
                }
            }
        }
        if ($isLive)
            return $this->convertToMinuteSeconds($isLive, " ");
        return 0;
    }

    /**
     * Returns 1 if this box is live and o if it is offline
     * @return int
     */
    public function getSQLBox()
    {
        $isLive = false;
        foreach ($this->xmlObject->boxes->box as $b) {
            if ($b) {
                $boxId = (string) $b->id;
                if ($boxId !== "thesmsbox") {
                    $status = (string) $b->status;
                    if (substr($status, 0, 2) === "on")
                        $isLive = substr($status, 8);
                }
            }
        }
        if ($isLive)
            return $this->convertToMinuteSeconds($isLive, " ");
        return 0;
    }

    /**
     * This returns an array containing the sms status of (this) platform.
     * @return array
     */
    public function getSMS()
    {
        $received = array(
            "total" => (string) $this->xmlObject->sms->received->total,
            "queued" => (string) $this->xmlObject->sms->received->queued
        );

        $sent = array(
            "total" => (string) $this->xmlObject->sms->sent->total,
            "queued" => (string) $this->xmlObject->sms->sent->queued
        );

        return array(
            "received" => $received,
            'sent' => $sent,
            "dlr" => $this->getDLR(),
            "storesize" => (string) $this->xmlObject->sms->storesize,
            "inbound" => (string) $this->xmlObject->sms->inbound,
            "outbound" => (string) $this->xmlObject->sms->outbound
        );
    }

    /**
     * This returns an array containing the dlr status of (this) platform.
     * @return array
     */
    public function getDLR()
    {
        /*return array(
            "received" => (string) $this->xmlObject->dlr->received->total,
            'sent' => (string) $this->xmlObject->dlr->sent->total,
            "inbound" => (string) $this->xmlObject->dlr->inbound,
            "outbound" => (string) $this->xmlObject->dlr->outbound,
            "queued" => (string) $this->xmlObject->dlr->queued
        );*/
        return array(
            "received" => (string) $this->xmlObject->dlr->received->total,
            'sent' => (string) $this->xmlObject->dlr->sent->total,
            "queued" => (string) $this->xmlObject->dlr->queued
        );
    }

    /**
     * This return the status and the queue of sms box and sql box.
     * @return array
     */
    public function getBoxStatus()
    {
        $responseArray = array();
        $areThereAnyLiveBox = false;

        foreach ($this->xmlObject->boxes->box as $b) {
            if(isset($b)) {
                $areThereAnyLiveBox = true;
                $boxId = (string) $b->id;
                if ($boxId === "thesmsbox") {
                    $status = (string) $b->status;
                    $responseArray['smsbox']['status'] = substr($status, 0, 2) === "on" ? "online":"offline";
                    $responseArray['smsbox']['queue'] = (string) $b->queue;
                } else {
                    $status = (string) $b->status;
                    $responseArray['sqlbox']['status'] = substr($status, 0, 2) === "on" ? "online":"offline";
                    $responseArray['sqlbox']['queue'] = (string) $b->queue;
                }
            }
        }
        if ($areThereAnyLiveBox)
            return $responseArray;
        return "There is currently no box available.";
    }

    /**
     * This function is similar to getBoxesStatus but returns only status only.
     * @return array
     */
    private function getPrimaryBoxStatus()
    {
        $responseArray = array();
        foreach ($this->xmlObject->boxes->box as $b) {

            $boxId = (string) $b->id;
            if ($boxId === "thesmsbox") {
                $status = (string) $b->status;
                $responseArray['smsbox']['status'] = substr($status, 0, 2) === "on" ? $status:"dead";
            } else {
                $status = (string) $b->status;
                $responseArray['sqlbox']['status'] = substr($status, 0, 2) === "on" ? $status:"dead";
            }
        }
        return $responseArray;
    }

    /**
     * This function gets smsc statuses for each subscriber.
     * @return array
     */
    public function getSMSC()
    {
        $smscs = array();
        $smscStatus = array();
        foreach ($this->xmlObject->smscs->smsc as $sm) {
            $smscId = (string) $sm->id;
            if ( ! in_array($smscId, $smscs)) {
                $smscs[] = $smscId;
                $smscStatus[$smscId]['status'] = substr((string) $sm->status, 0, 2) === "on" ? "online":"offline";
                $smscStatus[$smscId]['failed'] = (int) $sm->failed;
                $smscStatus[$smscId]['queued'] = (int) $sm->queued;
                $smscStatus[$smscId]['sms_sent'] = (int) $sm->sms->sent;
                $smscStatus[$smscId]['sms_received'] = (int) $sm->sms->received;
                $smscStatus[$smscId]['inbound'] = (string) $sm->sms->inbound;
                $smscStatus[$smscId]['outbound'] = (string) $sm->sms->outbound;
                $smscStatus[$smscId]['dlr_sent'] = (int) $sm->dlr->sent;
                $smscStatus[$smscId]['dlr_received'] = (int) $sm->dlr->received;
            } else {
                $smscStatus[$smscId]['failed'] = $smscStatus[$smscId]['failed'] + (int) $sm->failed;
                $smscStatus[$smscId]['queued'] =  $smscStatus[$smscId]['queued'] + (int) $sm->queued;
                $smscStatus[$smscId]['sms_sent'] = $smscStatus[$smscId]['sms_sent'] + (int) $sm->sms->sent;
                $smscStatus[$smscId]['sms_received'] = $smscStatus[$smscId]['sms_received'] + (int) $sm->sms->received;
                $smscStatus[$smscId]['inbound'] = $this->splitAndAddString($smscStatus[$smscId]['inbound'], (string) $sm->sms->inbound);
                $smscStatus[$smscId]['outbound'] = $this->splitAndAddString($smscStatus[$smscId]['outbound'], (string) $sm->sms->outbound);
                $smscStatus[$smscId]['dlr_sent'] = $smscStatus[$smscId]['dlr_sent'] + (int) $sm->dlr->sent;
                $smscStatus[$smscId]['dlr_received'] = $smscStatus[$smscId]['dlr_received'] + (int) $sm->dlr->received;
            }
        }

        //$boxesStatus = $this->getPrimaryBoxStatus();

        $moduleStatus = array(
            "sevas" => $this->convertToMinuteSeconds($this->getSevassAppStatus($this->platformName), ","),
            "sql_box" => $this->getSQLBox(),
            "sms_box" => $this->getSMSBox()
        );

        return array(
            "gateway_uptime" => $this->getGatewayUptime(),
            "total"=>$this->getSMS(),
            "smppp_bind"=>$smscStatus,
            "modules" => $moduleStatus
        );
    }

    /**
     * This queries the status.xml of this platform,. creates a file if it does not already exists.
     * @param $platform
     * @return bool|string
     */
    private function getXML($platform)
    {
        $url = "http://{$platform}.atp-sevas.com:13013/status.xml";
        if ($output = Client::makeCall($url)) {
            /*$file = $platform.".xml";
            $fileResource = fopen($file, "w");
            fwrite($fileResource, $output);
            fclose($fileResource);*/
            return $output;
        }
        return false;
    }

    /**
     * This function takes two string and adds them up.
     * @param $FirstString
     * @param $secondString
     * @return string
     */
    private function splitAndAddString($FirstString, $secondString)
    {
        $firstStringArray = explode(',', $FirstString);
        $secondStringArray = explode(',', $secondString);
        $totalArray = array();
        $arrayLength = sizeof($firstStringArray);

        for ($i = 0; $i < $arrayLength; $i++) {
            $totalArray[$i] = $firstStringArray[$i] + $secondStringArray[$i];
            $totalArray[$i] = number_format((float)$totalArray[$i], 2, '.', '');
        }

        return implode(',', $totalArray);
    }

    /**
     * This application gets Sevass Application Status
     * @return bool|mixed|string
     */
    public static function getSevassAppStatus($platform)
    {
        $url = "http://{$platform}.atp-sevas.com:8585/sevas/upm";
        if ($uptime = Client::makeCall($url))
            return str_replace("\n", "", $uptime);
        return 0;
    }

    /**
     * This function returns the Minute and Seconds Representation of An uptime.
     * @param $string
     * @return string
     */
    private function convertToMinuteSeconds($string, $spitWith)
    {
        $stringPieces = explode($spitWith, $string);
        $lengthOfStringPieces = sizeof($stringPieces);
        if ($lengthOfStringPieces < 4) {
            $minutes = intval($stringPieces[1]);
            $hoursToMinutes = intval($stringPieces[0] * 60);
            return ($minutes+$hoursToMinutes)."m". " ".$stringPieces[2];
        }
        $minutes = intval($stringPieces[2]);
        $hoursToMinutes = intval($stringPieces[1] * 60);
        $dayToMinutes = intval($stringPieces[0] * 24 * 60);
        return ($minutes+$hoursToMinutes+$dayToMinutes)."m". " ".$stringPieces[3];
    }
}
