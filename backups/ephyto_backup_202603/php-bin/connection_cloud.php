<?php
 // Database connection script-PostgreSQL

 $con = pg_connect("host=localhost dbname=ephytoin_dbEphyto user=ephytoin_ephyto password=ephyto@2025 port=5432");

    if (!$con) {
        die("Database connection failed: " . pg_last_error());
    }  

?>