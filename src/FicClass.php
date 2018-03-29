<?php

namespace Caereservices\Fic;

use Caereservices\Fic\FicStatus as FicStatus;

class FicClass {

   protected $api_uid;
   protected $api_key;
   protected $api_url;
   protected $haveCredentials;
   protected $status;

   protected function addCredentials($request = [])
   {
      if( $this->haveCredentials ) {
         $request["api_uid"] = $this->api_uid;
         $request["api_key"] = $this->api_key;
      }
      return $request;
   }

   protected function makeRequest($url = "", $params = [])
   {
      $emptyResult = [
         "messaggio" => "Errore generico",
         "limite_breve" => "0",
         "limite_medio" => "0",
         "limite_lungo" => "0",
         "success" => false
      ];
      if( $url != "" ) {
         $request = $this->addCredentials($params);
         $options = array(
            "http" => array(
               "header"  => "Content-type: text/json\r\n",
               "method"  => "POST",
               "content" => json_encode($request)
            ),
         );
         $context = stream_context_create($options);
         $result = json_decode(file_get_contents($url, false, $context), true);
         return $result;
      } else {
         return json_decode($emptyResult, true);
      }
   }

   protected function processResult($result = [])
   {
      if( count($result) > 0 ) {
         if( isset($result["success"]) && $result["success"] ) {
            return $result;
         } else if( isset($result["error_code"]) ) {
            return intval($result["error_code"]);
         } else {
            return false;
         }
      } else {
         return false;
      }
   }

   function __construct()
   {
      $this->haveCredentials = false;
   }

   function setCredentials($apiUid = "", $apiKey = "", $baseUrl = "")
   {
      if( ($apiUid != "") && ($apiKey != "") && ($baseUrl != "") ) {
         $this->api_uid = $apiUid;
         $this->api_key = $apiKey;
         $this->api_url = $baseUrl;
         $this->haveCredentials = true;
      }
   }

   function getStatus()
   {
      return $this->status;
   }

   function canUseApi()
   {
      $this->status = FicStatus::ALL_OK;
      $url = $this->api_url . '/richiesta/info';
      $result = $this->processResult($this->makeRequest($url));
      if( $result === false ) {
         $this->status = FicStatus::ERR_GENERIC;
         return false;
      }
      if( is_numeric($result) ) {
         $this->status = $result;
         return false;
      }
      return true;
   }

}

?>
