
<?php
  //  echo "<script>alert('Hello from users_dataprocess.php');</script>";
   include 'connection.php';

//1.) Enable and disable user groups from handleCheckboxChange function in users-validate.js
  if (isset($_POST['groupid']) && isset($_POST['chboxstatus'])) {  // Ajax
      $groupid = $_POST['groupid'];
      
    $status = ($_POST['chboxstatus'] === 'true') ? 'yes' : 'no';
    //echo "<script>alert('Group ID: " . $groupid . "');</script>";
   
    $sqlupdate = "UPDATE tbusergroup SET enabled = '" . $status . "' WHERE id = '$groupid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      echo "<script>window.location.href = 'users.php?part=ugroup';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating user data.');</script>";
    }   
  }
 
//2.) Enable and disable users from userhandleCheckboxChange function in users-validate.js
  if (isset($_POST['userid']) && isset($_POST['userchboxstatus'])) {  // Ajax
    $userid = $_POST['userid'];
    
    $status = ($_POST['userchboxstatus'] === 'true') ? 'yes' : 'no';
    //echo "<script>alert('User ID: " . $userid . "');</script>";
   
    $sqlupdate = "UPDATE tbusers SET enabled = '" . $status . "' WHERE id = '$userid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      echo "<script>window.location.href = 'users.php?part=userslist';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating user data.');</script>";
    }   
  }

//3.) Enable and disable Location from handleLocationCheckboxChange function in users-validate.js
if (isset($_POST['locationid']) && isset($_POST['locationchboxstatus'])) {  // Ajax
    $locationid = $_POST['locationid'];
    
    $status = ($_POST['locationchboxstatus'] === 'true') ? 'yes' : 'no';
    //echo "<script>alert('Location ID: " . $locationid . "');</script>";
   
    $sqlupdate = "UPDATE tblocations SET enabled = '" . $status . "' WHERE id = '$locationid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      echo "<script>window.location.href = 'masterdata.php?part=locations';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating location data.');</script>";
    }   
}
 
//4.) Get province on change from SelectProvinceOnChange function in users-validate.js
if( isset($_POST['provinceid'])){
    //$provinceid = $_POST['provinceid'];
    $provinceid = pg_escape_string($con, $_POST['provinceid']);
    $sql = "SELECT id, dname FROM tbdistricts WHERE pid='$provinceid' ORDER BY dname ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        echo '<option value="">*** Please select one ***</option>';
        while ($row = pg_fetch_assoc($result)) {
            echo "<option value=\"{$row['id']}\" $selected>{$row['dname']}</option>";
        }
    }
    
     
}
?>