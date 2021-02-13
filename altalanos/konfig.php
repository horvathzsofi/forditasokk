<?php

/* Adatbázis hitelesítő adatok. */
define('DB_SERVER', 'localhost'); /* Az adatbázis localhoston fut*/
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); /* nincs jelszóval védve*/
define('DB_NAME', 'forditasokk'); /*nevet később módosítani kell*/
 
/* Kapcsolódás a adatbázishoz - UTF-8 erőltetésével  */
try{
  $adatbazisom = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME,DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  // PDO Hiba mód: Kivételdobás
  $adatbazisom->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $kivetel){
    die("Hiba: A kapcsolódás nem lehetséges!" . $kivetel->getMessage());
}

?>