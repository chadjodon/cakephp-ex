<?php

//----------------------------------------------------------------
// Class to verify data and print message indicating the problem
//----------------------------------------------------------------
class Validator {
   function validemail($email) {
      if ($email == NULL) return False;

      $atSymbol = strrchr($email, "@");
      if (!$atSymbol) return False;

      $dotChar  =strrchr($atSymbol, ".");
      if (!$dotChar) return False;

      return True;
   }

   function validname($fname, $lname) {
     //if ($fname == NULL) return False;
     //if ($lname == NULL) return False;
     return True;
   }

   function validaddr($addr) {
      if ($addr == NULL) return False;
      return True;
   }

   function validcity($city) {
      if ($city == NULL) return False;
      return True;
   }

   function validstate($state) {
      if ($state == NULL) return False;
      return True;
   }


   function validpassword($pw, $cpw) {
     if ($pw == NULL) return False;
     if (strcmp($pw,$cpw) != 0) return False;
     if (strlen($pw) < 6) return False;
     return True;
   }


   function validzip ($zip) {
     if ($zip == NULL) return False;
     if ( $zip > 99999) return False;
     if ( $zip < 10000) return False;
     return True;
   }

   function validprice($price) {

     if ($price == NULL) return FALSE;
     if (!strstr($price,".")) $price = $price.".00";

     $dot = substr($price, strlen($price)-3, 1);
      if (
          strcmp($dot,".")!=0 |
          !$this->validint(substr($price,0,strlen($price)-3)) |
          !$this->validint(substr($price,strlen($price)-2,strlen($price)))
         ) {
        return False;
      }

     return TRUE;

   }

   function validint($intstr) {
     if ($intstr == NULL) return FALSE;
     for ($i =0; $i<strlen($intstr); $i++) {
       if (ord("0") > ord(substr($intstr,$i,1)) | ord("9") < ord(substr($intstr,$i,1)) ) return FALSE;
     }
     return TRUE;
   }

   function validccnum($ccnum) {
     if (!$this->validint($ccnum)) return FALSE;
     if (strlen($ccnum) != 16 ) return FALSE;
     return TRUE;
   }

   function validcccvv2($cccvv2) {
     if ($cccvv2 == NULL | $cccvv2 == "") return FALSE;
     if (!$this->validint($cccvv2)) return FALSE;
     if (strlen($cccvv2) != 3 ) return FALSE;
     return TRUE;
   }


   function validccexpiry($ccdate, $ccyear) {
     if ($ccdate == NULL | !$this->validint($ccdate)) return FALSE;
     if ($ccyear == NULL | !$this->validint($ccyear)) return FALSE;
     $ccyear = "20".$ccyear;
     $today = getdate();
     $month = $today['mon'];
     $year = $today['year'];
     if ($ccyear > $year) return TRUE;
     elseif ($ccyear == $year & $ccdate >= $month) return TRUE;
     else return FALSE;
   }

   function validitemname($name) {
      if ($name == NULL) return False;
      return True;
   }

   function validdate($date) {
     if ($date != NULL) {
       $year = substr($date,0,4);
       $dash = substr($date,4,1);
       $month = substr($date,5,2);
       $dash2 = substr($date,7,1);
       $day = substr($date,8,2);

       if (!$this->validint($year)) return FALSE;
       if (!$this->validint($month)) return FALSE;
       if (!$this->validint($day)) return FALSE;
       if (strcmp($dash,"-")!=0 | strcmp($dash2,"-")!=0 ) return FALSE;
     }
     return TRUE;
   }
}
?>
