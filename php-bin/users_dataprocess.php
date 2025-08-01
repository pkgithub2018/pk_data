
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

//5.) Enable and disable country 
if(isset($_POST['countryid']) && isset($_POST['countrystatus'])){
  $cid = $_POST['countryid'];
    
    $status = ($_POST['countrystatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbcountries SET enabled = '" . $status . "' WHERE id = '$cid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      echo "<script>window.location.href = 'masterdata.php?part=countries';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating location data.');</script>";
    }   
}

//6.) Enable and disable product

if(isset($_POST['productid']) && isset($_POST['productchboxstatus'])){
    $pid = $_POST['productid'];
    
    $status = ($_POST['productchboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbproduct SET enabled = '" . $status . "' WHERE id = '$pid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=product';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating product data.');</script>";
    }   
}

//7.) Enable and disable product group
if(isset($_POST['productgroupid']) && isset($_POST['productgroupchboxstatus'])){
    $pgid = $_POST['productgroupid'];
    
    $status = ($_POST['productgroupchboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbproduct_group SET enabled = '" . $status . "' WHERE id = '$pgid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=productgroup';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating product group data.');</script>";
    }   
}

//8.) Enable and disable product unit
if(isset($_POST['productunitid']) && isset($_POST['productunitchboxstatus'])){
    $punitid = $_POST['productunitid'];
    
    $status = ($_POST['productunitchboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbproduct_unit SET enabled = '" . $status . "' WHERE id = '$punitid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=productunit';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating product unit data.');</script>";
    }   
}

//9.) Enable and disable conveyance
if(isset($_POST['conveyanceid']) && isset($_POST['conveyancechboxstatus'])){
    $conveyid = $_POST['conveyanceid'];
    
    $status = ($_POST['conveyancechboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbconveyance SET enabled = '" . $status . "' WHERE id = '$conveyid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=conveyance';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating conveyance data.');</script>";
    }   
}
//10.) Enable and disable inspection method  

if(isset($_POST['inspectionmethodid']) && isset($_POST['inspectionmethodchboxstatus'])){
    $imethodid = $_POST['inspectionmethodid'];
    
    $status = ($_POST['inspectionmethodchboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbinspection_method SET enabled = '" . $status . "' WHERE id = '$imethodid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=inspectionmethod';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating inspection method data.');</script>";
    }   
}

//11.) Enable and disable treatment method 
if(isset($_POST['treatmentmethodid']) && isset($_POST['treatmentmethodchboxstatus'])){
    $tmid = $_POST['treatmentmethodid'];
    
    $status = ($_POST['treatmentmethodchboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbtreatment_method SET enabled = '" . $status . "' WHERE id = '$tmid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=treatmentmethod';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating treatment method data.');</script>";
    }   
}

//12.) Enable and disable entity type
if(isset($_POST['entitytypeid']) && isset($_POST['entitytypechboxstatus'])){
    $etid = $_POST['entitytypeid'];
    
    $status = ($_POST['entitytypechboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbentity_type SET enabled = '" . $status . "' WHERE id = '$etid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=entitytype';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating entity type data.');</script>";
    }   
}

//13.) Enable and disable module
if(isset($_POST['moduleid']) && isset($_POST['modulechboxstatus'])){
    $mid = $_POST['moduleid'];
    
    $status = ($_POST['modulechboxstatus'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbmodules SET enabled = '" . $status . "' WHERE id = '$mid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=modules';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating module data.');</script>";
    }   
}

//14.) Enable permit-delete
if(isset($_POST['permitid_delete']) && isset($_POST['permitchboxstatus_delete'])){
    
    $pid_delete = $_POST['permitid_delete'];
       
    $status = ($_POST['permitchboxstatus_delete'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbgrouppermits SET pdelete = '" . $status . "' WHERE id = '$pid_delete'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=permits';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating permit data.');</script>";
    }   
} 
//15.) Enable permit-edit
if(isset($_POST['permitid_edit']) && isset($_POST['permitchboxstatus_edit'])){
    
    $pid_edit = $_POST['permitid_edit'];
       
    $status = ($_POST['permitchboxstatus_edit'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbgrouppermits SET pupdate = '" . $status . "' WHERE id = '$pid_edit'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=permits';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating permit data.');</script>";
    }   
}
//16.) Enable permit-add 
if(isset($_POST['permitid_add']) && isset($_POST['permitchboxstatus_add'])){
    
    $pid_add = $_POST['permitid_add'];
       
    $status = ($_POST['permitchboxstatus_add'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbgrouppermits SET padd = '" . $status . "' WHERE id = '$pid_add'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=permits';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating permit data.');</script>";
    }   
}
//17.) Enable permit-read
if(isset($_POST['permitid_read']) && isset($_POST['permitchboxstatus_read'])){
    
    $pid_read = $_POST['permitid_read'];
       
    $status = ($_POST['permitchboxstatus_read'] === 'true') ? 'yes' : 'no';
       
    $sqlupdate = "UPDATE tbgrouppermits SET pread = '" . $status . "' WHERE id = '$pid_read'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
  
     if ($result) {
      
      echo "<script>window.location.href = 'masterdata.php?part=permits';</script>";
      //exit();
    } else {
      echo "<script>alert('Error updating permit data.');</script>";
    }   
}
?>