<?php 
echo "Hello from PHP!"; 
$conn = pg_connect("host=localhost dbname=dbEphyto user=postgres password=ephyto@2025 port=5432");

if (!$conn) {
    echo "❌ Connection failed.";
} else {
    echo "✅ Connected to PostgreSQL!";
    $query = "SELECT * FROM tbprovinces";
    $result = pg_query($conn, $query);  
    if (!$result) {
        echo "❌ Query failed.";
    } else {
        echo "✅ Query executed successfully!";
        while ($row = pg_fetch_assoc($result)) {
            echo "<br>Province: " . $row['pname'];
        }
    }
    pg_close($conn);

}
?>