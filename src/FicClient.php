<?php

namespace Caereservices\Fic;

use Caereservices\Fic\FicStatus as FicStatus;

class FicClient extends FicClass {

   protected $isEmpty;

   protected $id;
   protected $nome;
   protected $indirizzo_via;
   protected $indirizzo_cap;
   protected $indirizzo_citta;
   protected $indirizzo_provincia;
   protected $mail;
   protected $tel;
   protected $piva;
   protected $cf;

   protected function save($id = "")
   {
      if( !$this->empty && ($this->nome != "") ) {
         $data = [
            "nome" => $this->nome,
            "mail" => $this->mail,
            "indirizzo_via" => $this->indirizzo_via,
            "indirizzo_cap" => $this->indirizzo_cap,
            "indirizzo_citta" => $this->indirizzo_citta,
            "indirizzo_provincia" => $this->indirizzo_provincia,
            "tel" => $this->tel,
            "piva" => $this->piva,
            "cf" => $this->cf,
            "paese_iso" => "IT"
         ];
         $url = $this->api_url . '/clienti/nuovo';
         if( ($id != "") || ($this->id != "") ) {
            $data["id"] = ($id != "" ? $id : $this->id);
            $url = $this->api_url . '/clienti/modifica';
         }
         $result = $this->processResult($this->makeRequest($url, $data));
         if( $result === false ) {
            $this->status = FicStatus::ERR_GENERIC;
            return false;
         }
         if( is_numeric($result) ) {
            $this->status = $result;
            return false;
         }
         $this->id = $result["id"];
         return $this->id;
      } else {
         $this->status = FicStatus::ERR_USR_DATA_EMPTY;
         return false;
      }
   }

   function __construct()
   {
      parent::__construct();
      $this->clearFields();
   }

   function clearFields()
   {
      $this->id = "";
      $this->nome = "";
      $this->indirizzo_via = "";
      $this->indirizzo_cap = "";
      $this->indirizzo_citta = "";
      $this->indirizzo_provincia = "";
      $this->mail = "";
      $this->tel = "";
      $this->piva = "";
      $this->cf = "";
      $this->empty = true;
   }

   function setField($name = "", $value = "")
   {
      if( $name != "" ) {
         if( isset($this->{$name}) ) {
            $this->empty = false;
            $this->{$name} = $value;
         }
      }
   }

   function setFields($data = [])
   {
      if( count($data) > 0 ) {
         foreach( $data as $key => $value ) {
            if( isset($this->{$key}) ) {
               $this->empty = false;
               $this->{$key} = $value;
            }
         }
      }
   }

   function create()
   {
      return $this->save("");
   }

   function update()
   {
      return $this->save($this->id);
   }

   function delete($id = "")
   {
      if( $id != "" ) {
         $data = [
            "id" => $id
         ];
         $url = $this->api_url . '/clienti/elimina';
         $result = $this->processResult($this->makeRequest($url, $data));
         if( $result === false ) {
            $this->status = FicStatus::ERR_GENERIC;
            return false;
         }
         if( is_numeric($result) ) {
            $this->status = $result;
            return false;
         }
         if( $result["success"] ) {
            $this->clearFields();
         }
         return $result["success"];
      } else {
         $this->status = FicStatus::ERR_NO_ID_SPECIFIED;
         return false;
      }
   }

}
