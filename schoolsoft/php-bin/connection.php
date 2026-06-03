<?php
 $sql_host='localhost';
 $sql_user='root';
 $sql_pass='';
 /*
 //************Old php5.5 mysql_connect for PHP 7: mysqli_connect
 $MysqlConnect=mysqli_connect($sql_host,$sql_user,$sql_pass) or 
  die("Can't connect to server");    
 $mydatabase=mysqli_select_db('vkshop_2190170',$MysqlConnect) or 
  die("Can't connect to database".mysqli_connect_error($MysqlConnect));  
*/  
   $con = mysqli_connect($sql_host, $sql_user, $sql_pass, "dbschool_2190170");
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $db_select = mysqli_select_db($con, "dbschool_2190170");
    if (!$db_select) {
        die("Database selection failed: " . mysqli_connect_error());
    }
?>