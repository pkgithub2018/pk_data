<?php
 
 $con = pg_connect("host=localhost dbname=dbEphyto user=postgres password=ephyto@2025 port=5432");

    if (!$con) {
        die("Database connection failed: " . pg_last_error());
    }  

?>