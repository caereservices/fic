<?php

namespace Caereservices\Fic;

use Caereservices\Fic\FicStatus as FicStatus;

class FicInvoice extends FicClass {

   protected $isEmpty;

   protected $id;
   protected $id_cliente;
   protected $txn_id;
   protected $subs_code;
   protected $subs_desc;
   protected $subs_price;
   protected $subs_price_tax;
   protected $payment_amount;
   protected $invoice_token;
   protected $mail_mittente;
   protected $mail_destinatario;
   protected $mail_oggetto;
   protected $mail_messaggio;

   protected function save($id = "")
   {
      if( !$this->empty && ($this->id_cliente != "") ) {
         $data = [
            "id_cliente" => $this->id_cliente,
            "id_fornitore" => "0",
            "autocompila_anagrafica" => true,
            "salva_anagrafica" => false,
            "data" => date("d/m/Y"),
            "valuta" => "EUR",
            "prezzi_ivati" => false,
            "nascondi_scadenza" => true,
            "ddt" => false,
            "ftacc" => false,
            "id_template" => 0, // Create template on platform
            "mostra_info_pagamento" => true,
            "metodo_pagamento" => "PayPal",
            "metodo_titoloN" => "TXNID",
            "metodo_descN" => $this->txn_id,
            "mostra_totali" => "tutti",
            "mostra_bottone_paypal" => false,
            "mostra_bottone_bonifico" => false,
            "mostra_bottone_notifica" => false,
            "lista_articoli" => [
               [
                  "id" => "0",
                  "codice" => $this->subs_code,
                  "nome" => "",
                  "um" => "",
                  "quantita" => 1,
                  "descrizione" => $this->subs_desc,
                  "prezzo_netto" => $this->subs_price,
                  "prezzo_lordo" => $this->subs_price_tax,
                  "tassabile" => true,
                  "sconto" => 0,
                  "magazzino" => false
               ]
            ],
            "lista_pagamenti" => [
               [
                  "data_scadenza" => date("d/m/Y"),
                  "importo" => $this->payment_amount,
                  "metodo" => "not",
                  "data_saldo" => date("d/m/Y")
               ]
            ],
            "PA" => false,
            "split_payment" => false
         ];
         $url = $this->api_url . '/fatture/nuovo';
         if( ($id != "") || ($this->id != "") ) {
            $data["id"] = ($id != "" ? $id : $this->id);
            $url = $this->api_url . '/fatture/modifica';
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
         if( isset($result["success"]) && $result["success"] ) {
            $this->id = $result["new_id"];
            $this->invoice_token = $result["token"];
         }
         return $result["success"];
      } else {
         $this->status = FicStatus::ERR_INVOICE_DATA_EMPTY;
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
      $this->id_cliente = "";
      $this->txn_id = "";
      $this->subs_code = "";
      $this->subs_desc = "";
      $this->subs_price = "";
      $this->subs_price_tax = "";
      $this->payment_amount = "";
      $this->invoice_token = "";
      $this->mail_mittente = "";
      $this->mail_destinatario = "";
      $this->mail_oggetto = "";
      $this->mail_messaggio = "";
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

   function delete($token = "")
   {
      if( $token != "" ) {
         $data = [
            "token" => $token
         ];
         $url = $this->api_url . '/fatture/elimina';
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
         $this->status = FicStatus::ERR_NO_TOKEN_SPECIFIED;
         return false;
      }
   }

   public function sendMail()
   {
      if( !$this->empty && ($this->invoice_token != "") ) {
         if( ($this->mail_mittente != "") && ($this->mail_destinatario != "") ) {
            $data = [
               "token" => $this->invoice_token,
               "mail_mittente" => $this->mail_mittente,
               "mail_destinatario" => $this->mail_destinatario,
               "oggetto" => $this->mail_oggetto,
               "messaggio" => $this->mail_messaggio,
               "includi_documento" => false,
               "invia_ddt" => false,
               "invia_fa" => false,
               "includi_allegato" => false,
               "invia_copia" => true,
               "allega_pdf" => true
            ];
            $url = $this->api_url . '/fatture/inviamail';
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
            $this->status = FicStatus::ERR_MAIL_ADDR_EMPTY;
            return false;
         }
      } else {
         $this->status = FicStatus::ERR_NO_TOKEN_SPECIFIED;
         return false;
      }
   }

}
