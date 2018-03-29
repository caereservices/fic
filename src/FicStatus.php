<?php

namespace Caereservices\Fic;

class FicStatus {
   const ALL_OK = -99;
   const ERR_USR_DATA_EMPTY = -1;
   const ERR_NO_ID_SPECIFIED = -2;
   const ERR_INVOICE_DATA_EMPTY = -3;
   const ERR_NO_TOKEN_SPECIFIED = -4;
   const ERR_MAIL_ADDR_EMPTY = -5;
   const ERR_GENERIC = 0;
   const ERR_AUTHENTICATION = 1000;
   const ERR_INVALID_PARAMETER = 1001;
   const ERR_USER_NOT_EXIST = 1004;
   const ERR_INVALID_CONTENT = 1100;
   const ERR_LICENSE_ENDING = 2000;
   const ERR_EXCEED_REQ_NUM = 2002;
   const ERR_API_BLOCKED = 2004;
   const ERR_FUNC_NOT_ENABLED = 2005;
   const ERR_RESTRICTED_DATA = 2006;
   const ERR_UNKNOWN_USER = 4000;
   const ERR_DATA_NUM_EXCEED = 4001;
   const ERR_AMOUNT_CALC = 5000;
}
