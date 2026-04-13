<?php
 // To reload the product list after adding a new product
require("php-bin/connection.php");
require("php-bin/supports.php");

// Output only the table rows for the commodity table
ApplicationProductList($con);

?>