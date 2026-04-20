<?php

// === CLOUD SERVER DIAGNOSTIC FUNCTION ===
function checkCloudServerCompatibility() {
    $issues = [];
    
    // Check PostgreSQL extension
    if (!extension_loaded('pgsql')) {
        $issues[] = 'PostgreSQL extension (php-pgsql) not loaded - CRITICAL';
    }
    
    // Check if pg functions exist
    if (!function_exists('pg_connect')) {
        $issues[] = 'pg_connect function not available';
    }
    
    if (!function_exists('pg_query')) {
        $issues[] = 'pg_query function not available';
    }
    
    // Check other extensions
    if (!extension_loaded('json')) {
        $issues[] = 'JSON extension not loaded';
    }
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        $issues[] = 'PHP version ' . PHP_VERSION . ' may be too old (recommend 7.4+)';
    }
    
    return $issues;
}

// Log compatibility issues for debugging
$compatibility_issues = checkCloudServerCompatibility();
if (!empty($compatibility_issues)) {
    error_log('Cloud Server Compatibility Issues: ' . implode(' | ', $compatibility_issues));
    // Add HTML comment for debugging (only if not AJAX request)
    if (!defined('AJAX_REQUEST')) {
        echo "<!-- CLOUD SERVER DIAGNOSTICS: " . implode(' | ', $compatibility_issues) . " -->\n";
    }
}

/*
  Grouplist: Show list of users group from tbusersgroup table
*/
function Grouplist($con){
    $sqlgroup="SELECT * FROM tbusergroup ORDER BY id DESC";
    $result = pg_query($con,$sqlgroup) or die(pg_last_error());
    $i = 0;
    if(pg_num_rows($result) > 0) {
       $nrows = pg_num_rows($result);
       while($row = pg_fetch_array($result)){
            $i++;
            $gid = $row['id'];
            $gname = $row['title'];
            $gdesc = $row['desc'];
            $gstatus = $row['enabled'];
            // Add checkbox for $gstatus
            // handleCheckboxChange function in users-validate.js
          print "<tr>
                  <td>$i</td>
                  <td>$gname</td>
                  <td>$gdesc</td>
                  <td>
                    <div class='form-check form-switch'>
                      <input class='form-check-input' role='switch' type='checkbox' id='$gid' data-nrows='$nrows' " . ($gstatus === 'yes' ? 'checked' : '') . " onchange='handleCheckboxChange(this)'>
                    </div>
                  </td>
                  <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addGroupModal' data-gid='$gid' data-gname='" . htmlspecialchars($gname, ENT_QUOTES) . "' data-gdesc='" . htmlspecialchars($gdesc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>
                  </td>
                  <td><a href='tables-usergroups.php?part=ugroup&ug=del&ugid=$gid' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                </tr>";     
       } // end of while loop     
    }
}

/*
  Groupnew: Add new group into tbusergroup table
*/
function Groupnew($gname,$gdesc,$con){
    $gname = pg_escape_string($con, $gname);
    $gdesc = pg_escape_string($con, $gdesc);
    // Check if the group name already exists
    $sqlgroup = "SELECT title FROM tbusergroup WHERE title='".$gname."'";
    $result = pg_query($con,$sqlgroup) or die(pg_last_error());
    $exgroup = "";
    if(pg_num_rows($result) > 0) {
        echo "<script>alert('Group name already exists. Please choose a different group name.');</script>";
        $exgroup = "yes";
        return $exgroup;
    } else {
       $sqladdgroup = "INSERT INTO \"tbusergroup\" (\"title\", \"desc\", \"enabled\") 
                VALUES ('" . $gname . "', '" . $gdesc . "', 'yes') RETURNING id"; // 'yes' is the default value for a new group
        $result = pg_query($con, $sqladdgroup);
        if ($result) {
          // Get the last inserted ID
            $last_id = pg_fetch_result($result, 0, 'id');
        } else {
          // Handle error if insertion fails
          echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
        } 
    }
}
/*
  Groupdelete: Delete group from tbusergroup table
*/
function Groupdelete($gid,$con){
    $sqlgroup = "DELETE FROM tbusergroup WHERE id='".$gid."'";
    $result = pg_query($con,$sqlgroup) or die(pg_last_error());
    if($result){
        echo "<script>alert('Group deleted successfully.');</script>";
        // Redirect back to the table
        echo "<script>window.location.href = 'tables-usergroups.php?part=ugroup';</script>";
    } else {
        echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
    }
}
/*
  Groupupdate: Update group from tbusergroup table 
  VIA FUNCTION: handleCheckboxChange in users-validate.js and users_dataprocess.php
*/
 function Groupupdate($gid,$gname,$gdesc,$con){
    $gid = pg_escape_string($con, $gid);
    $gname = pg_escape_string($con, $gname);
    $gdesc = pg_escape_string($con, $gdesc);
    
    // Update the group information
    // \"desc\"='".$gdesc."' - \"desc\" is a reserved word in PostgreSQL
    $sqlupdategroup = "UPDATE tbusergroup SET title='".$gname."', \"desc\"='".$gdesc."' WHERE id='".$gid."'";
    $result = pg_query($con, $sqlupdategroup) or die(pg_last_error());
    if ($result) {
        //echo "<script>alert('Group updated successfully.');</script>";
        // Redirect back to the table
        echo "<script>window.location.href = 'users.php?part=ugroup';</script>";
    } else {
        echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
    }
}
/* 
 GrouppermitName: Check if the user has permission to access a specific group
*/
function GrouppermitName($groupid,$con){ // userlogin is the email
    $sqlpermit="SELECT title FROM tbusergroup WHERE id='$groupid' AND enabled='yes'";
    $result = pg_query($con,$sqlpermit) or die(pg_last_error());
    list($title) = pg_fetch_array($result);  
    if(!empty($email) && !empty($groupid) && !empty($enable)){ // Admin user
        return true;
    } else {
        return false;
    }  
}
/*
 Userpermit: Check if the user has permission to access a specific page
*/
function Userpermit($userlogin,$con){ // userlogin is the email
    $sqlpermit="SELECT email, group_id, enabled FROM tbusers WHERE email='".$userlogin."' AND group_id='1' AND enabled='yes'";
    $result = pg_query($con,$sqlpermit) or die(pg_last_error());
    list($email,$groupid,$enable) = pg_fetch_array($result);  
    if(!empty($email) && !empty($groupid) && !empty($enable)){ // Admin user
        return true;
    } else {
        return false;
    }  
}

/*
  UserByEmail: Get user ID by email address
*/
function UserByEmail($email, $con) {
    $email = pg_escape_string($con, $email);
    $sql = "SELECT id FROM tbusers WHERE email='$email' AND enabled='yes'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_array($result);
        return $row['id'];
    } else {
        return null;
    }
}

/*
  Addnewuser: Add new users into tbusers table
*/

function Addusers($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $location, $con, $userid = null) {
    // Escape all inputs
    $name = pg_escape_string($con, $name);
    $surname = pg_escape_string($con, $surname);
    $sex = pg_escape_string($con, $sex);
    $psw = pg_escape_string($con, $psw);
    $position = pg_escape_string($con, $position);
    $unit = pg_escape_string($con, $unit);
    
    $phone = pg_escape_string($con, $phone);
    $email = pg_escape_string($con, $email);
    $lastlogin = pg_escape_string($con, date('Y-m-d H:i:s'));
    $groupid = pg_escape_string($con, $groupid);
    $admingroup = pg_escape_string($con, $admingroup);
    $location = pg_escape_string($con, $location);
    $status = pg_escape_string($con, 'yes');

    // Check if the email already exists
    $sqluser = "SELECT email FROM tbusers WHERE email='$email'";
    $result = pg_query($con, $sqluser) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        return "yes";
    } else {
        // Insert user, set last_login to NULL, group_admin to 'no' by default
        $sqladduser = "INSERT INTO \"tbusers\" 
            (\"name\", \"surname\", \"sex\", \"psw\", \"position\", \"unit\", \"phone\", \"email\", \"last_login\", \"group_id\", \"group_admin\", \"location_id\", \"enabled\") 
            VALUES 
            ('$name', '$surname', '$sex', '$psw', '$position', '$unit', '$phone', '$email', '$lastlogin', '$groupid', '$admingroup', '$location', '$status') RETURNING id";
        $result = pg_query($con, $sqladduser);
        if ($result) {
            $last_id = pg_fetch_result($result, 0, 'id');
            //$message = "User added successfully. User ID: " . $last_id;
            // ADD USER PROFILE with UID
            InitializeProfile($last_id, $con); // Initialize user profile with default values
            // Redirect to the user list page
            $redirect_url = 'users.php?part=userslist';
            if ($userid) {
                $redirect_url .= '&uid=' . $userid;
            }
            echo "<script>window.location.href = '$redirect_url';</script>";
        } else {
            echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 Deleteuser: Delete users from tbusers
*/
function Deleteuser($uid,$con, $current_userid = null){
    // Validate uid parameter - must be numeric and not empty
    if (empty($uid) || !is_numeric($uid)) {
        echo "<script>alert('Invalid user ID provided.');</script>";
        $redirect_url = 'users.php?part=userslist';
        if ($current_userid) {
            $redirect_url .= '&uid=' . $current_userid;
        }
        echo "<script>window.location.href = '$redirect_url';</script>";
        return;
    }
    
    $sql = "DELETE FROM tbusers WHERE id='$uid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result) {
        $redirect_url = 'users.php?part=userslist';
        if ($current_userid) {
            $redirect_url .= '&uid=' . $current_userid;
        }
        echo "<script>window.location.href = '$redirect_url';</script>";
    } else {
        echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
    }
}
/*
  Userlist: List all users from tbusers table
*/
function Userlist($con, $currentUserid = '', $lang = ''){
    $sqluserlist="SELECT * FROM tbusers ORDER BY id DESC"; // Order by ID in descending order
    $result = pg_query($con,$sqluserlist) or die(pg_last_error());
    $i = 0;
    $uidValue = $currentUserid !== '' ? $currentUserid : (isset($_GET['uid']) ? $_GET['uid'] : '');
    $langValue = $lang !== '' ? $lang : (isset($_GET['lang']) ? $_GET['lang'] : '');
    $uidParam = $uidValue !== '' ? '&uid=' . urlencode($uidValue) : '';
    $langParam = $langValue !== '' ? '&lang=' . urlencode($langValue) : '';
    if(pg_num_rows($result) > 0) {
       
       while($row = pg_fetch_array($result)){
            $i++;
            $uid = $row['id'];
            $name = $row['name'];
            $surname = $row['surname']; // Not used in the table
            //$position = $row['position']; 
            $unit = $row['unit']; // Not used in the table
            $phone = $row['phone'];
            $email = $row['email'];
            $lastlogin = $row['last_login']; 
            $usergroup = $row['group_id']; 
            $usergroup = Groupname($usergroup, $con); // Get group name from tbusergroup table
            //$groupadmin = $row['group_admin'];
            $location = $row['location_id'];
            $location = Locationname($location, $con); // Get location name from tblocations table
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$name</td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td>$lastlogin</td>  
                    <td>$usergroup</td>
                    <td>$location</td>
                    <td>
                     <div class='form-check form-switch'>
                      <input class='form-check-input' role='switch' type='checkbox' id='$uid' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleUserCheckboxChange(this)'>
                    </div>
                    </td>
                                        <td><a href='users.php?frm=userupdate&uidup=$uid$uidParam$langParam' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                                        <td><a href='users.php?frm=userdelete&uidup=$uid$uidParam$langParam' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>  
                  </tr>";
       } // end of while loop     
    }
}
/*
  Userdata: Get user data from tbusers table
*/
function Userdata($uid, $con) {
    // Validate uid parameter - must be numeric and not empty
    if (empty($uid) || !is_numeric($uid)) {
        return null;
    }
    
    $sql = "SELECT * FROM tbusers WHERE id='$uid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));

    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_array($result);
    }
    return null;
}
/*
  Userconnect: Get user connection data from tbuserconnections table
*/
function Userconnect($getuid,$postuid,$posthuid,$cookieuid,$serveruid,$con) {
    // Dynamic Authentication System - same as entity.php and main.php
    $userid = '';
    // Try multiple sources for userid (Dynamic Authentication System)
    // First, try to get from GET parameter (most reliable for sessionless)
    if (isset($getuid) && !empty($getuid)) {
        $userid = $getuid; // GET from URL in EntityExportList function in supports.php
    }
    // Try to get from POST parameter (form submissions)
    elseif (isset($postuid) && !empty($postuid)) {
        $userid = $postuid;
    }
    elseif (isset($posthuid) && !empty($posthuid)) {
        $userid = $posthuid;
    }
    // Try to get from cookies if set
    elseif (isset($cookieuid) && !empty($cookieuid)) {
        $userid = $cookieuid;
    }
    // Last resort: try to get from HTTP_REFERER if coming from other pages
    elseif (isset($serveruid) && !empty($serveruid)) {
    $referer = $serveruid;
    if (preg_match('/[?&]uid=([^&]+)/', $referer, $matches)) {
        $userid = urldecode($matches[1]);
        
    }
 }
    // Authentication check
    if(empty($userid)){
        // If user ID is not set, redirect to login page
        echo "<script>alert('Dynamic Authentication Required. Please log in to access this page.');</script>"; 
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
    return $userid;
}

/*
  Updateuser_values: Update user from tbusers table
*/
function Updateuser_values($uid,$con){
  
  // Validate uid parameter - must be numeric and not empty
  if (empty($uid) || !is_numeric($uid)) {
      return array('', '', '', '', '', '', '', '', '', '', '', '');
  }
  
  $sql = "SELECT * FROM tbusers WHERE id = '$uid'"; // Use the sanitized input in the query
  $result = pg_query($con, $sql) or die(pg_last_error());

  if ($result && pg_num_rows($result) > 0) {
      $user = pg_fetch_array($result);

      // Pre-fill the form fields with user data
      $name = $user['name'];
      $surname = $user['surname'];
      $sex = $user['sex'];
      $psw = $user['psw'];
      $position = $user['position'];
      $unit = $user['unit'];
      $phone = $user['phone'];
      $email = $user['email'];
      $groupid = $user['group_id']; 
      $admingroup = $user['group_admin']; // Assuming this is the admin group field
      $location = $user['location_id'];
      $status = $user['enabled'];
      return array($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $location, $status);
  } else {
      echo "<script>alert('User not found.');</script>";
  }
}

/*
  Updateuser-submit: Submit the updates on users from data form into tbusers table
*/

function UpdateuserSubmit($uid, $name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $location, $con, $current_userid = null) {
    // Validate uid parameter - must be numeric and not empty
    if (empty($uid) || !is_numeric($uid)) {
        echo "<script>alert('Invalid user ID provided.');</script>";
        return;
    }
    
    // Fetch current user data
    $sqlolduser = "SELECT * FROM tbusers WHERE id='$uid'";
    $result = pg_query($con, $sqlolduser) or die(pg_last_error($con));

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);

        // Check if any field has changed
        if (
            $user['name']      !== $name ||
            $user['surname']   !== $surname ||
            $user['sex']       !== $sex ||
            $user['psw']       !== $psw ||
            $user['position']  !== $position ||
            $user['unit']      !== $unit ||
            $user['phone']     !== $phone ||
            $user['email']     !== $email ||
            $user['group_id']  !== $groupid ||
            $user['group_admin'] !== $admingroup ||
            $user['location_id'] !== $location ||
            $user['enabled']   !== $status
        ) {
            // At least one field has changed, so update
            $sqlupdate = "UPDATE tbusers SET 
                name='$name',
                surname='$surname',
                sex='$sex',
                psw='$psw',
                position='$position',
                unit='$unit',
                phone='$phone',
                email='$email',
                group_id='$groupid',
                group_admin='$admingroup',
                location_id='$location',
                enabled='$status'
                WHERE id='$uid'";
            $updateresult = pg_query($con, $sqlupdate);

            if ($updateresult) {
                $redirect_url = 'users.php?part=userslist';
                if ($current_userid) {
                    $redirect_url .= '&uid=' . $current_userid;
                }
                echo "<script>window.location.href='$redirect_url';</script>";
            } else {
                echo "<script>alert('Error updating user: " . pg_last_error($con) . "');</script>";
            }
        } else {
            // No changes detected
            $redirect_url = 'users.php?part=userslist';
            if ($current_userid) {
                $redirect_url .= '&uid=' . $current_userid;
            }
            echo "<script>alert('No changes detected.');window.location.href='$redirect_url';</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }
  }

  /*
    SelectCountry: Select country from tbcountries table
  */
 function SelectCountry($countryid, $con){
    // Check if the country ID is set
    $sql = "SELECT id, title FROM tbcountries ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($countryid !== null && $countryid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
}

/*
 SelectProvince: Select province from tbprovinces table
*/
function SelectProvince($provinceid, $con){
    // Check if the province ID is set
    $sql = "SELECT id, pname FROM tbprovinces ORDER BY pname ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($provinceid !== null && $provinceid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['pname']}</option>";
        }
    }
}

/*
 SelectLocation: Select location from tbusers table
*/
 function SelectLocation($locid, $con){
      // Check if the location ID is set
       // Fetch all locations
    $sql = "SELECT id, name_eng FROM tblocations ORDER BY name_eng ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($locid !== null && $locid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['name_eng']}</option>";
        }
    }
 }

 /*
 SelectLocationtype: Select location from tblocationtype table
*/
function SelectLocationType($ltype, $con){
    // Check if the location type ID is set
    $sql = "SELECT id, title FROM tblocationtype ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($ltype !== null && $ltype == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
 }

 /*
 SelectUsergroup: Select user group from tbusergroup table
*/
 function SelectUsergroup($groupid, $con){
    // Check if the group ID is set
    $sql = "SELECT id, title FROM tbusergroup ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($groupid !== null && $groupid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
 }

 /*
 SelectUnit: Select unit from tbproduct_unit table
*/
 function SelectUnit($unitid, $con){
    // Check if the unit ID is set
    $sql = "SELECT id, symb FROM tbproduct_unit ORDER BY symb ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($unitid !== null && $unitid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['symb']}</option>";
        }
    }
 }

 /*
   UnitSymbol: Return unit symbol from tbproduct_unit table
 */
    function UnitSymbol($unitid, $con){
        // Check if the unit ID is set
        $sql = "SELECT symb FROM tbproduct_unit WHERE id='$unitid'";
        $result = pg_query($con, $sql);
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            return $row['symb'];
        } else {
            return '';
        }
    }

 /*
  SelectConveyance: Select conveyance from tbconveyance table
*/
 function SelectConveyance($conveyanceid, $con){
    // Check if the conveyance ID is set
    $sql = "SELECT id, conveytype FROM tbconveyance ORDER BY conveytype ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($conveyanceid !== null && $conveyanceid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['conveytype']}</option>";
        }
    }
}

/*
  SelectPurpose: Select purpose from tbpurpose table
*/
 function SelectPurpose($purposeid, $con){
    // Check if the purpose ID is set
    $sql = "SELECT id, title FROM tbpurpose ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($purposeid !== null && $purposeid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
}

/*
 SelectModules: Select modules from tbmodules table
*/
function SelectModules($moduleid, $con){
    // Check if the module ID is set
    $sql = "SELECT id, title FROM tbmodules ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($moduleid !== null && $moduleid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
 }

 /*
    InitializeProfile: IT IS USED IN Addusers FUNCTION, when a new user is created. Add user's profile into tbprofile table if it does not exist, 
 */
function InitializeProfile($uid,$con) {
    // Check if the user profile already exists

    $sqlcheck = "SELECT * FROM tbprofile WHERE uid = '$uid'";
    $resultcheck = pg_query($con, $sqlcheck);
    if (pg_num_rows($resultcheck) == 0) {
        $sql = "INSERT INTO tbprofile (uid, description, address, twitter, facebook, linkin, instagram, imgfilename, imgfilepath) 
        VALUES ('$uid', 'default_profile_data', 'default_address', 'default_twitter', 'default_facebook', 'default_linkin', 'default_instagram', 'default_imgfilename', 'default_imgfilepath') RETURNING id";
        $result = pg_query($con, $sql);
        if (!$result) {
            die(pg_last_error($con));
        }
    } else {
        // echo "<script>Profile already exists for user ID: $uid</script>";
    }
}
/*
  Profiledata: Get user profile data from tbprofile table
*/
function Profiledata($uid, $con) {
    $sql = "SELECT * FROM tbprofile WHERE uid = '$uid'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // Return null if no profile found
    }
}

/*
 ProfileImageUpload: Upload user profile image to tbprofile table
*/
function ProfileImageUpload($uid, $imgfilename, $imgfilepath, $con) {
    $sql = "UPDATE tbprofile SET imgfilename = '$imgfilename', imgfilepath = '$imgfilepath' WHERE uid = '$uid'";
    $result = pg_query($con, $sql);
    if (!$result) {
        die(pg_last_error($con));
    }
}

/*
 UpdateProfile: Update user profile data in tbprofile table
 $userid, $about, $address, $twitter, $facebook, $instagram, $linkedin
*/
function UpdateProfile($uid, $about, $address, $twitter, $facebook, $instagram, $linkedin, $position, $workunit, $phone, $email, $con) {
    require("php-bin/connection.php");
    $sqlprofile = "UPDATE tbprofile 
    SET description = '$about', 
        address = '$address',  
        twitter = '$twitter', 
        facebook = '$facebook', 
        instagram = '$instagram',
        linkin = '$linkedin'  
        WHERE uid = '$uid'";
     pg_query($con, $sqlprofile) or die(pg_last_error($con));
     
    // Update tbusers table with position, unit, phone, and email
    $sqlusers = "UPDATE tbusers SET 
        position = '$position',
        unit = '$workunit', 
        phone = '$phone', 
        email = '$email' 
        WHERE id = '$uid'";
    pg_query($con, $sqlusers) or die(pg_last_error($con));
    // Redirect to the same page to see the changes
  echo "<script>window.location.href='users-profile.php';</script>"; // Redirect to the same page
}

/*
 UpdateProfileChangePassword: Update user profile password in tbusers table
*/
function UpdateProfileChangePassword($uid, $newpassword, $con) {
    $sql = "UPDATE tbusers SET psw = '$newpassword' WHERE id = '$uid'";
    pg_query($con, $sql) or die(pg_last_error($con));
    // Redirect to the same page to see the changes
    echo "<script>window.location.href='users-profile.php';</script>"; // Redirect to the same page
}

/*
 currentPasswordCheck: Check if the current password is correct
*/
function currentPasswordCheck($uid, $currentpassword, $con) {
   // echo "<script>alert('Checking current password for user ID: $uid, password: $currentpassword');</script>";
    $sql = "SELECT * FROM tbusers WHERE id = '$uid' AND psw = '$currentpassword'";
    $result = pg_query($con, $sql);
    return pg_num_rows($result) > 0; // it returns true if the current password is correct
}

/*
  Groupname: Get group name from tbusergroup table
*/
function Groupname($gid, $con) {
    $sql = "SELECT title FROM tbusergroup WHERE id = '$gid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['title'];
    } else {
        return null; // Return null if no group found
    }
}

/*
    GroupPermitList: List all group permissions from tbgrouppermits table
*/
function GroupPermitList($con, $uid = '', $lang = '') {
    $sqlpermit = "SELECT * FROM tbgrouppermits ORDER BY id ASC";
    $result = pg_query($con, $sqlpermit) or die(pg_last_error());
    $i = 0;
    $uidValue = $uid !== '' ? $uid : (isset($_GET['uid']) ? $_GET['uid'] : '');
    $langValue = $lang !== '' ? $lang : (isset($_GET['lang']) ? $_GET['lang'] : '');
    $uidParam = $uidValue !== '' ? '&uid=' . urlencode($uidValue) : '';
    $langParam = $langValue !== '' ? '&lang=' . urlencode($langValue) : '';
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $gid = $row['gid'];
            $gname = Groupname($gid, $con); // Get group name from tbusergroup table 
            $mid = $row['mid'];
            $mod = ModuleName($mid, $con); // Get module name from tbmodules table
            
            print "<tr>
                    <td>$i</td>
                    <td>$gname</td>
                    <td>$mod</td>
                    <td>
                     <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($row['pread'] === 'yes' ? 'checked' : '') . " onchange='handlePermitReadCheckboxChange(this)'>
                      </div>
                     </td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($row['padd'] === 'yes' ? 'checked' : '') . " onchange='handlePermitAddCheckboxChange(this)'>
                      </div>
                    </td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($row['pupdate'] === 'yes' ? 'checked' : '') . " onchange='handlePermitEditCheckboxChange(this)'>
                      </div>
                    </td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($row['pdelete'] === 'yes' ? 'checked' : '') . " onchange='handlePermitDeleteCheckboxChange(this)'>
                      </div>
                    </td>
                    <td>
                                            <a href='users.php?part=upermits&id=$id&epermit=edit$uidParam$langParam' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a>
                    </td>
                                        <td><a href='users.php?part=upermits&id=$id&dpermit=del$uidParam$langParam' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                  </tr>";
        } // end of while loop     
    }
}
/*
 AddGroupPermit: Add group permissions into tbgrouppermit table
*/
function AddGroupPermit($groupid, $moduleid, $pread, $padd, $pupdate, $pdelete, $con) {
    // Escape inputs
    $groupid = pg_escape_string($con, $groupid);
    $moduleid = pg_escape_string($con, $moduleid);
    $pread = pg_escape_string($con, $pread);
    $padd = pg_escape_string($con, $padd);
    $pupdate = pg_escape_string($con, $pupdate);
    $pdelete = pg_escape_string($con, $pdelete);

    // Check if the permission already exists
    $sqlcheck = "SELECT * FROM tbgrouppermits WHERE gid='$groupid' AND mid='$moduleid'";
    $result = pg_query($con, $sqlcheck) or die(pg_last_error($con));
    
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Permission already exists for this group and module.');</script>";
        return "yes"; // Indicate that the permission already exists
    } else {
        // Insert new permission
        $sqladdpermit = "INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete) VALUES ('$groupid', '$moduleid', '$pread', '$padd', '$pupdate', '$pdelete') RETURNING id";
        $result = pg_query($con, $sqladdpermit) or die(pg_last_error($con));
        
        if ($result) {
            //echo "<script>alert('Permission added successfully.');</script>";
            echo "<script>window.location.href = 'users.php?part=upermits';</script>";
        } else {
            echo "<script>alert('Error adding permission: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
  UpdateGroupPermit: Update permit in tbgrouppermit
*/
function UpdateGroupPermit($pid, $gid, $mid, $pread, $padd, $pupdate, $pdelete, $con){
  $sqlup = "SELECT * FROM tbgrouppermits WHERE id='$pid'";
  $result = pg_query($con, $sqlup) or die(pg_last_error($con));

    if ($result && pg_num_rows($result) > 0) {
        $gpmt = pg_fetch_assoc($result);

        // Check if any field has changed
        if (
            $gpmt['gid']      !== $gid ||
            $gpmt['mid']   !== $mid ||
            $gpmt['pread']   !== $pread ||
            $gpmt['padd']   !== $padd ||
            $gpmt['pupdate']   !== $pupdate ||
            $gpmt['pdelete']   !== $pdelete
        ) {
             $sqlupdate = "UPDATE tbgrouppermits SET 
                gid='$gid',
                mid='$mid',
                pread='$pread',
                padd='$padd',
                pupdate='$pupdate',
                pdelete='$pdelete'
                WHERE id='$pid'";
            $upresult = pg_query($con, $sqlupdate);

            if ($upresult) {
                echo "<script>window.location.href='users.php?part=upermits';</script>";
            } else {
                echo "<script>alert('Error updating user: " . pg_last_error($con) . "');</script>";
            }
       }
    }
}
/*
  DeleteGroupPermit: Delete permit from tbgrouppermits
*/
function DeleteGroupPermit($pid,$con){
    $sqldel = "DELETE FROM tbgrouppermits WHERE id='$pid'";
    $result = pg_query($con,$sqldel);
    if($result){
      echo "<script>window.location.href='users.php?part=upermits';</script>";
     } else {
      echo "<script>alert('Error updating user: " . pg_last_error($con) . "');</script>";
    }
}  

/*
  GrouppermitVariables: return variables from tbgrouppermits table
*/
function GrouppermitVariables($pid, $con){
 $sql= "SELECT gid, mid, pread, padd, pupdate, pdelete FROM tbgrouppermits WHERE id='$pid'";
 $result = pg_query($con, $sql) or die(pg_last_error());
    if ($result && pg_num_rows($result) > 0) {
           list($gpId, $mpId, $pRead, $pAdd, $pUpdate, $pDelete) = pg_fetch_array($result);
           return array($gpId, $mpId, $pRead, $pAdd, $pUpdate, $pDelete);
        } else {
            return null; // Return null if no group permits found
        }

}

/*
 Locationlist: List all locations from tblocations table
*/
function Locationlist($con) {
    $sqllocation = "SELECT * FROM tblocations ORDER BY id ASC";
    $result = pg_query($con, $sqllocation) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        
        while ($row = pg_fetch_array($result)) {
            $i++;
            $locid = $row['id'];
            //$lcode = $row['lid'];
            $locname_eng = $row['name_eng'];
            $locname_lao = $row['name_lao'];
            $ltype = $row['location_type'];
            $ltype = Locationtype($ltype, $con); // Get location type from tblocationtype table
            $pid = $row['pid'];
            $pid = Provincename($pid, $con); // Get province name from tbprovinces table
            $did = $row['did'];
            $did = Districtname($did, $con); // Get district name from tbdistrict table
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$locname_eng</td>
                    <td>$locname_lao</td>
                    <td>$ltype</td>
                    <td>$did</td>
                    <td>$pid</td>
                    <td>
                      <div class='form-check form-switch'>
                       <input class='form-check-input' role='switch' type='checkbox' id='$locid' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleLocationCheckboxChange(this)'>
                     </div>
                    </td>
                    <td><a href='masterdata.php?loc=edit&id=$locid' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    <td><a href='masterdata.php?loc=del&id=$locid' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                  </tr>";
        } // end of while loop     
       
    }
} 

/*
 Addlocation: Add locations into tblocations table
*/
function Addlocation($locid, $nameeng, $namelao, $loctype, $pid, $did, $con) {
    // Escape all inputs
    $locid = pg_escape_string($con, $locid);
    $nameeng = pg_escape_string($con, $nameeng);
    $namelao = pg_escape_string($con, $namelao);
    $loctype = pg_escape_string($con, $loctype);
    $pid = pg_escape_string($con, $pid);
    $did = pg_escape_string($con, $did);

    // Check if the location name already exists
    $sqllocation = "SELECT lid, name_eng, name_lao FROM tblocations 
                    WHERE lid='$locid' 
                    AND name_eng='$nameeng' 
                    AND name_lao='$namelao'";
    $result = pg_query($con, $sqllocation) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Location name already exists. Please choose a different location name.');</script>";
        return "yes"; // Indicate that the location already exists
    } else {
        // Insert new location
        $sqladdlocation = "INSERT INTO \"tblocations\" (\"lid\",\"name_eng\", \"name_lao\", \"location_type\", \"pid\", \"did\", \"enabled\") 
                           VALUES ('".$locid."','".$nameeng."', '".$namelao."', '$loctype', '".$pid."', '".$did."','yes') RETURNING id";
        
        $result = pg_query($con, $sqladdlocation) or die(pg_last_error($con));
        if ($result) {
            // Redirect back to the table
            echo "<script>alert('Location added successfully.');</script>";
            echo "<script>window.location.href = 'masterdata.php?part=locations';</script>";
        } else {
            echo "<script>alert('Error adding location: " . pg_last_error($con) . "');</script>";
        }
    }
}

/*
 Locationupdate: Update locations from tblocations table
*/
function Locationupdate($id, $locid, $nameeng, $namelao, $loctype,$pid, $did, $con) {
   // NOT UPDATE FOR LOCATION ID
   $sql = "UPDATE tblocations SET 
            lid='$locid',
            name_eng='$nameeng',
            name_lao='$namelao', 
            location_type='$loctype',
            pid='$pid', 
            did='$did'
            WHERE id = '$id'";
     $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=locations';</script>";
    } else {
        echo "<script>alert('Error updating location: " . pg_last_error($con) . "');</script>";
    }
}
/*
  Locationname: Get name of locations from tblocations table
*/
function Locationname($locid, $con) {
    $sql = "SELECT name_eng FROM tblocations WHERE id = '$locid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['name_eng'];
    } else {
        return null; // Return null if no location found
    }
}

/*
  Locationtype: Get type of locations from tblocationtype table
*/
function Locationtype($ltype, $con) {
    $sql = "SELECT title FROM tblocationtype WHERE id = '$ltype'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['title'];
    } else {
        return null; // Return null if no location type found
    }
}
/*
  Locationvariables: Return variables from tblocations table
*/
function Locationvariables($locid, $con) {
    $sql = "SELECT * FROM tblocations WHERE id = '$locid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // Return null if no location found
    }
}
/*
  Provincename: Get province name from tbprovinces table
*/
function Provincename($pid, $con) {
    $sql = "SELECT pname FROM tbprovinces WHERE id = '$pid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['pname'];
    } else {
        return null; // Return null if no province found
    }
}

/*
  Districtname: Get district name from tbdistrict table
*/
function Districtname($did, $con) {
    $sql = "SELECT dname FROM tbdistricts WHERE id = '$did'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['dname'];
    } else {
        return null; // Return null if no district found
    }
}
/*
 SelectProvinces: Select provinces from tbprovinces table
*/
function SelectProvinces($pid, $con) {
    // Check if the province ID is set
    $sql = "SELECT id, pname FROM tbprovinces ORDER BY pname ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($pid !== null && $pid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['pname']}</option>";
        }
    }
}
/*
 SelectDistricts: Select districts from tbdistricts table
*/
function SelectDistricts($did, $pid, $con) {
    // Check if the district ID is set
    $sql = "SELECT id, dname FROM tbdistricts WHERE pid='$pid' ORDER BY dname ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($did !== null && $did == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['dname']}</option>";
        }
    }
}

/*
  SelectEntitytype: Select entity type from tbentitytype table
*/
function SelectEntitytype($etype, $con) {
    $sql = "SELECT id, title FROM tbentity_type ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($etype !== null && $etype == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
}

/*
 Countrylist: Select from tbcountries table
*/
function Countrylist($con) {
    $sqlcountry = "SELECT * FROM tbcountries ORDER BY id ASC";
    $result = pg_query($con, $sqlcountry) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $cid = $row['id'];
            $cname = $row['title'];
            $alcode = $row['alphacode'];
            $numcode = $row['numcode'];
            $desc = $row['desc'];
            $currency = $row['currency'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$cname</td>
                    <td>$alcode</td>
                    <td>$numcode</td>
                    <td>$currency</td>
                    <td>
                     <div class='form-check form-switch'>
                       <input class='form-check-input' role='switch' type='checkbox' id='$cid' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleCountryCheckboxChange(this)'>
                     </div>
                    </td>
                    <td>
                    <button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addCountryModal' data-cid='$cid' data-cname='" . htmlspecialchars($cname, ENT_QUOTES) . "' data-alcode='" . htmlspecialchars($alcode, ENT_QUOTES) . "' data-numcode='" . htmlspecialchars($numcode, ENT_QUOTES) . "' data-currency='" . htmlspecialchars($currency, ENT_QUOTES) . "' data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i>
                    </button>
                   </td>
                    <td><a href='masterdata.php?part=countries&cid=$cid&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                  </tr>";
        } // end of while loop     
    }
}
/*
  AddCountry: Add new country into tbcountries table
*/
function AddCountry($alphacode, $numcode,$cname, $desc,$currency, $con){
    
    $alphacode = pg_escape_string($con, $alphacode); // Escape the alpha code
    $numcode = pg_escape_string($con, $numcode); // Escape the numeric code
    $cname = pg_escape_string($con, $cname); // Escape the country name
    $desc = pg_escape_string($con, $desc); // Escape the description
    $currency = pg_escape_string($con, $currency); // Escape the currency
    
    // Check if the country name already exists
    $sqlcountry = "SELECT title FROM tbcountries WHERE title='$cname'";
    $result = pg_query($con, $sqlcountry) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Country name already exists. Please choose a different country name.');</script>";
        return "yes"; // Indicate that the country already exists
    } else {
        // Insert new country
        $sqladdcountry = "INSERT INTO \"tbcountries\" (\"alphacode\", \"numcode\", \"title\", \"desc\", \"currency\", \"enabled\") VALUES ('".$alphacode."','".$numcode."','".$cname."','".$desc."', '".$currency."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdcountry) or die(pg_last_error($con));
        if ($result) {
            // Redirect back to the table
           // echo "<script>alert('Country added successfully.');</script>";
            echo "<script>window.location.href = 'masterdata.php?part=countries';</script>";
        } else {
            echo "<script>alert('Error adding country: " . pg_last_error($con) . "');</script>";
        }
    }    
}
/*
  UpdateCountry: Update country from tbcountries table
*/
function UpdateCountry($cid, $alphacode, $numcode, $cname, $desc, $currency, $con) {
    // Escape all inputs
    $cid = pg_escape_string($con, $cid);
    $alphacode = pg_escape_string($con, $alphacode);
    $numcode = pg_escape_string($con, $numcode);
    $cname = pg_escape_string($con, $cname);
    $desc = pg_escape_string($con, $desc);
    $currency = pg_escape_string($con, $currency);

    // Update the country information
    $sqlupdatecountry = "UPDATE tbcountries SET alphacode='$alphacode', numcode='$numcode', title='$cname', \"desc\"='$desc', currency='$currency' WHERE id='$cid'";
    $result = pg_query($con, $sqlupdatecountry) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
       // echo "<script>alert('Country updated successfully.');</script>";
        echo "<script>window.location.href = 'masterdata.php?part=countries';</script>";
    } else {
        echo "<script>alert('Error updating country: " . pg_last_error($con) . "');</script>";
    }
}
/*
 DeleteCountry: Delete country from tbcountries table
*/
function DeleteCountry($cid, $con) {
    $sqlcountry = "DELETE FROM tbcountries WHERE id='$cid'";
    $result = pg_query($con, $sqlcountry) or die(pg_last_error($con));
    if ($result) {
        echo "<script>alert('Country deleted successfully.');</script>";
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=countries';</script>";
    } else {
        echo "<script>alert('Error deleting country: " . pg_last_error($con) . "');</script>";
    }
} 

/*
  CountryInfo: Get country information from tbcountries table
*/
function CountryInfo($cid, $con) {
    // Validate country ID is not empty and is numeric
    if (empty($cid) || !is_numeric($cid)) {
        return null;
    }
    
    $cid = (int)$cid; // Cast to integer for safety
    $sql = "SELECT * FROM tbcountries WHERE id = '$cid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // Return null if no country found
    }
}

/*
 DistrictList: Select from tbdistricts table
*/
function DistrictList($userid, $con) {
    $sql = "SELECT d.id, d.dname, d.pid, p.pname FROM tbdistricts d LEFT JOIN tbprovinces p ON d.pid = p.id ORDER BY p.pname ASC, d.dname ASC";
    $result = pg_query($con, $sql) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $did   = $row['id'];
            $dname = $row['dname'];
            $pid   = $row['pid'];
            $pname = $row['pname'];
            print "<tr>
                    <td>$i</td>
                    <td>" . htmlspecialchars($pname, ENT_QUOTES) . "</td>
                    <td>" . htmlspecialchars($dname, ENT_QUOTES) . "</td>
                    <td>
                      <button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addDistrictModal'
                        data-did='$did'
                        data-dname='" . htmlspecialchars($dname, ENT_QUOTES) . "'
                        data-pid='$pid'>
                        <i class='bi bi-pencil-square table-icon'></i>
                      </button>
                    </td>
                    <td><a href='masterdata.php?part=districts&did=$did&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                  </tr>";
        }
    }
}
/*
  AddDistrict: Add new district into tbdistricts table
*/
function AddDistrict($pid, $dname, $con) {
    $pid   = pg_escape_string($con, $pid);
    $dname = pg_escape_string($con, $dname);

    $sqlcheck = "SELECT dname FROM tbdistricts WHERE dname='$dname' AND pid='$pid'";
    $result = pg_query($con, $sqlcheck) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('District name already exists for this province.');</script>";
        return;
    }
    $sqladd = "INSERT INTO tbdistricts (pid, dname) VALUES ('$pid', '$dname')";
    $result = pg_query($con, $sqladd) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=districts';</script>";
    } else {
        echo "<script>alert('Error adding district: " . pg_last_error($con) . "');</script>";
    }
}
/*
  UpdateDistrict: Update district in tbdistricts table
*/
function UpdateDistrict($did, $pid, $dname, $con) {
    $did   = pg_escape_string($con, $did);
    $pid   = pg_escape_string($con, $pid);
    $dname = pg_escape_string($con, $dname);

    $sqlupdate = "UPDATE tbdistricts SET pid='$pid', dname='$dname' WHERE id='$did'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=districts';</script>";
    } else {
        echo "<script>alert('Error updating district: " . pg_last_error($con) . "');</script>";
    }
}
/*
  DeleteDistrict: Delete district from tbdistricts table
*/
function DeleteDistrict($did, $con) {
    $did = pg_escape_string($con, $did);
    $sql = "DELETE FROM tbdistricts WHERE id='$did'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result) {
        echo "<script>alert('District deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting district: " . pg_last_error($con) . "');</script>";
    }
}

/*
 ProvinceList: Select from tbprovinces table
*/
function ProvinceList($userid, $con) {
    $sql = "SELECT id, pname FROM tbprovinces ORDER BY pname ASC";
    $result = pg_query($con, $sql) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $pid = $row['id'];
            $pname = $row['pname'];
            print "<tr>
                    <td>$i</td>
                                        <td>" . htmlspecialchars($pname, ENT_QUOTES) . "</td>
                                        <td>
                                            <button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addProvinceModal'
                                                data-provid='$pid'
                                                data-pname='" . htmlspecialchars($pname, ENT_QUOTES) . "'>
                                                <i class='bi bi-pencil-square table-icon'></i>
                                            </button>
                                        </td>
                  </tr>";
        }
    }
}
/*
  AddProvince: Add new province into tbprovinces table
*/
function AddProvince($pname, $con) {
    $pname = pg_escape_string($con, $pname);

    $sqlcheck = "SELECT pname FROM tbprovinces WHERE pname='$pname'";
    $result = pg_query($con, $sqlcheck) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Province name already exists.');</script>";
        return;
    }
    $sqladd = "INSERT INTO tbprovinces (pname) VALUES ('$pname')";
    $result = pg_query($con, $sqladd) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=provinces';</script>";
    } else {
        echo "<script>alert('Error adding province: " . pg_last_error($con) . "');</script>";
    }
}
/*
  UpdateProvince: Update province in tbprovinces table
*/
function UpdateProvince($pid, $pname, $con) {
    $pid = pg_escape_string($con, $pid);
    $pname = pg_escape_string($con, $pname);

    $sqlupdate = "UPDATE tbprovinces SET pname='$pname' WHERE id='$pid'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=provinces';</script>";
    } else {
        echo "<script>alert('Error updating province: " . pg_last_error($con) . "');</script>";
    }
}


/*
  SelectCountryCurrency: Select country currency from tbcountries table
*/
function SelectCurrency($currency, $con) {
    $sql = "SELECT DISTINCT country, currency, code FROM tbcurrency ORDER BY country ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($currency !== null && $currency == $row['code']) ? 'selected' : '';
            echo "<option value=\"{$row['code']}\" $selected>{$row['country']} ({$row['currency']})</option>";
        }
    }
}

/*
  PestList: List all pest from tbpest table
*/
function PestList($con) {
    $sqlpest = "SELECT * FROM tbpest ORDER BY id ASC";
    $result = pg_query($con, $sqlpest) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $name = $row['pestname'];
            $scientific_name = $row['scientificname'];
            $category = $row['category'];
            print "<tr>
                    <td>$i</td>
                    <td>$name</td>
                    <td>$scientific_name</td>
                    <td>$category</td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addPestModal' 
                         data-pestid='$id' 
                         data-pname='" . htmlspecialchars($name, ENT_QUOTES) . "' 
                         data-scientificname='" . htmlspecialchars($scientific_name, ENT_QUOTES) . "'
                         data-category='" . htmlspecialchars($category, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>
                    </td>
                    <td><a href='masterdata.php?part=pest&pestid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>   
                    </tr>"; 
        } // end of while loop
    }
}

/*
 AddPest: Add new pest into tbpest table
*/
function AddPest($pname, $scientificname, $category, $userid, $con) {
    // Escape all inputs
    $pname = pg_escape_string($con, $pname);
    $scientificname = pg_escape_string($con, $scientificname);
    $category = pg_escape_string($con, $category);

    // Check if the pest name already exists
    $sqlpest = "SELECT pestname FROM tbpest WHERE pestname='$pname'";
    $result = pg_query($con, $sqlpest) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Pest name already exists. Please choose a different pest name.');</script>";
        return "yes"; // Indicate that the pest already exists
    } else {
        // Reset the sequence to avoid duplicate key errors
        $reset_seq = "SELECT setval('tbpest_id_seq', COALESCE((SELECT MAX(id)+1 FROM tbpest), 1), false)";
        pg_query($con, $reset_seq);
        
        // Insert new pest
        $sqladdpest = "INSERT INTO \"tbpest\" (\"pestname\", \"scientificname\", \"category\") 
                       VALUES ('".$pname."', '".$scientificname."', '".$category."') RETURNING id";
        $result = pg_query($con, $sqladdpest) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=pest&uid=".$userid."';</script>";
            exit();
        } else {
            echo "<script>alert('Error adding pest: " . pg_last_error($con) . "');</script>";
        }
    }
}

/*
 UpdatePest: Update pest from tbpest table
*/
function UpdatePest($id, $pname, $scientificname, $category,  $userid, $con) {
    // Escape all inputs
    $id = pg_escape_string($con, $id);
    $pname = pg_escape_string($con, $pname);
    $scientificname = pg_escape_string($con, $scientificname);
    $category = pg_escape_string($con, $category);

    // Update pest
    $sqlupdate = "UPDATE \"tbpest\" SET \"pestname\"='$pname', \"scientificname\"='$scientificname', \"category\"='$category' WHERE \"id\"='$id'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=pest&pestid=".$id."&uid=".$userid."&success=pest_updated&name=".urlencode($pname)."';</script>";
    } else {
        echo "<script>alert('Error updating pest: " . pg_last_error($con) . "');</script>";
    }
}
/*
 PestInfo: Get pest information from tbpest table
*/
function PestInfo($pestid, $con) {
    // Validate pestid is not empty and is numeric
    if (empty($pestid) || !is_numeric($pestid)) {
        return null;
    }
    $sql = "SELECT * FROM tbpest WHERE id = '$pestid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // Return null if no pest found
    }
}

/*
  PestSelectionList: Get list of pests from tbpest table for selection modal
*/
function PestSelectionList($con) {
    $pest_sql = "SELECT * FROM tbpest ORDER BY pestname ASC";
                $pest_result = pg_query($con, $pest_sql);
                $pest_counter = 0;
                
                if ($pest_result && pg_num_rows($pest_result) > 0) {
                  while ($pest_row = pg_fetch_assoc($pest_result)) {
                    $pest_counter++;
                    $pest_id = $pest_row['id'] ?? '';
                    $pest_name = htmlspecialchars($pest_row['pestname'] ?? '');
                    $common_name = htmlspecialchars($pest_row['pestname'] ?? '');
                    $scientific_name = htmlspecialchars($pest_row['scientificname'] ?? '');
                    $category = htmlspecialchars($pest_row['category'] ?? '');
                                        
                    echo "<tr data-pest-type='" . strtolower($category) . "'>";
                    echo "<td><em>" . $scientific_name . "</em></td>";
                    echo "<td>" . $pest_name . "</td>";
                    echo "<td><span>" . ucfirst($category) . "</span></td>";
                    echo "<td>";
                    // Test with actual function call
                    $pest_id_int = (int)$pest_id;
                    $pest_name_safe = htmlspecialchars($pest_name, ENT_QUOTES, 'UTF-8');
                    
                    echo "<button type='button' class='btn btn-sm btn-danger' onclick='selectPest($pest_id_int, \"$pest_name_safe\", \"$pest_name_safe\", \"$scientific_name\", \"$category\");'>";
                    echo "Select";
                    echo "</button>";
                    echo "</td>";
                    echo "</tr>";
                  }
                  return $pest_counter;
                } else {
                  echo "<tr><td colspan='4' class='text-center text-muted'>No pest records found in database</td></tr>";
                }
}

/*
  PestDetectedSave: Save detected pest into tbpest_detected table
*/
function PestDetectedSave($appid,$pestid, $infestation, $alivestatus, $riskcategory, $inspectionresult, $con) {
    // Escape all inputs
    $appid = pg_escape_string($con, $appid);
    $pestid = pg_escape_string($con, $pestid);
    $infestation = pg_escape_string($con, $infestation);
    $alivestatus = pg_escape_string($con, $alivestatus);
    $riskcategory = pg_escape_string($con, $riskcategory);
    $inspectionresult = pg_escape_string($con, $inspectionresult);

    // Insert detected pest
    $sqladdpest = "INSERT INTO \"tbpest_detected\" (\"application_id\", \"pestid\", \"infestation_level\", \"alive_status\", \"risk_category\", \"result_measure\") 
                   VALUES ('".$appid."', '".$pestid."', '".$infestation."', '".$alivestatus."', '".$riskcategory."', '".$inspectionresult."') RETURNING id";
    $result = pg_query($con, $sqladdpest) or die(pg_last_error($con));
    if ($result) {
        return true; // Indicate success
    } else {
        echo "<script>alert('Error adding detected pest: " . pg_last_error($con) . "');</script>";
        return false; // Indicate failure
    }
}

/*
 PestDetectedUpdate: Update detected pest from tbpest_detected table
*/
function PestDetectedUpdate($pestdetect_id,$pestid, $infestation, $alivestatus, $riskcategory, $inspectionresult, $con) {
    // Escape all inputs
    $pestdetect_id = pg_escape_string($con, $pestdetect_id);
    $pestid = pg_escape_string($con, $pestid);
    $infestation = pg_escape_string($con, $infestation);
    $alivestatus = pg_escape_string($con, $alivestatus);
    $riskcategory = pg_escape_string($con, $riskcategory);
    $inspectionresult = pg_escape_string($con, $inspectionresult);

    // Update detected pest
    $sqlupdate = "UPDATE \"tbpest_detected\" SET 
                    \"pestid\"='$pestid', 
                    \"infestation_level\"='$infestation', 
                    \"alive_status\"='$alivestatus', 
                    \"risk_category\"='$riskcategory', 
                    \"result_measure\"='$inspectionresult' 
                  WHERE \"id\"='$pestdetect_id'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
    if ($result) {
        return true; // Indicate success
    } else {
        echo "<script>alert('Error updating detected pest: " . pg_last_error($con) . "');</script>";
        return false; // Indicate failure
    }
}

/*
  PestDetectedInspectionUpdate: Update inspection result of detected pest from tbpest_detected table
*/
function PestDetectedInspectionUpdate($appid, $con) {
    // Escape all inputs
    $appid = pg_escape_string($con, $appid);
   $sqlpestdetected = "SELECT * FROM tbpest_detected WHERE application_id = '$appid'";
    $result = pg_query($con, $sqlpestdetected) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $sqlinspectionupdate = "UPDATE \"tbinspection\" SET 
                                \"pest_detected\"='1' 
                              WHERE \"application_id\"='$appid'";
        $result1 = pg_query($con, $sqlinspectionupdate) or die(pg_last_error($con));
        if ($result1) {
            return true; // Indicate success
        } else {
            echo "<script>alert('Error updating inspection pest detected: " . pg_last_error($con) . "');</script>";
            return false; // Indicate failure
        }
    } 
}

/*
 PesDetectedInfo: Get pest information from tbpest table
*/
function PestDetectedInfo($appid, $con) {
    $sql = "SELECT * FROM tbpest_detected WHERE application_id = '$appid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // Return null if no pest found
    }
}

/* 
  ProductList: List all product  from tbproduct table
*/
function ProductList($userid, $con) {
    $sqlproduct = "SELECT * FROM tbproduct ORDER BY id ASC";
    $result = pg_query($con, $sqlproduct) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $code = $row['code'];   
            $pname = $row['name'];
            $scientname = $row['name_scientific'];
            $desc = $row['desc'];
            $hscode = $row['hscode'];
            $producgroup = $row['productgroup'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$pname</td>
                    <td>$scientname</td>
                    <td>$hscode</td>
                    <td>$producgroup</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleProductCheckboxChange(this)'>
                      </div>        
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addProductModal' 
                         data-pid='$id' 
                         data-pname='" . htmlspecialchars($pname, ENT_QUOTES) . "' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "'
                         data-scientname='" . htmlspecialchars($scientname, ENT_QUOTES) . "'
                         data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "' 
                         data-hscode='" . htmlspecialchars($hscode, ENT_QUOTES) . "'
                         data-productgroup='" . htmlspecialchars($producgroup, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>
                    </td>
                    <td><a href='masterdata.php?part=product&uid=$userid&pid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>   
                    </tr>"; 
        } // end of while loop
    }
}  
/*
 AddProduct: Add new product into tbproduct table
*/
function AddProduct($code, $pname, $scientname, $desc, $hscode, $productgroup, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $pname = pg_escape_string($con, $pname);
    $scientname = pg_escape_string($con, $scientname);
    $desc = pg_escape_string($con, $desc);
    $hscode = pg_escape_string($con, $hscode);
    $productgroup = pg_escape_string($con, $productgroup);

    // Check if the product code already exists
    $sqlproduct = "SELECT code FROM tbproduct WHERE code='$code'";
    $result = pg_query($con, $sqlproduct) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Product code already exists. Please choose a different product code.');</script>";
        return "yes"; // Indicate that the product already exists
    } else {
        // Insert new product
        $sqladdproduct = "INSERT INTO \"tbproduct\" (\"code\", \"name\", \"name_scientific\", \"desc\", \"hscode\", \"productgroup\", \"enabled\") 
                          VALUES ('".$code."', '".$pname."', '".$scientname."', '".$desc."', '".$hscode."', '".$productgroup."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdproduct) or die(pg_last_error($con));
        if ($result) {
            // Redirect back to the table
            echo "<script>window.location.href = 'masterdata.php?part=product';</script>";
        } else {
            echo "<script>alert('Error adding product: " . pg_last_error($con) . "');</script>";
        }
    }
}    

/*
 UpdateProduct: Update product from tbproduct table
*/
function UpdateProduct($pid, $code, $pname, $scientname, $desc, $hscode, $productgroup, $con) {
    // Escape all inputs
    $pid = pg_escape_string($con, $pid); // Get product ID from POST data
    $code = pg_escape_string($con, $code);
    $pname = pg_escape_string($con, $pname);
    $scientname = pg_escape_string($con, $scientname);
    $desc = pg_escape_string($con, $desc);
    $hscode = pg_escape_string($con, $hscode);
    $productgroup = pg_escape_string($con, $productgroup);

    // Update the product information
    $sqlupdateproduct = "UPDATE tbproduct SET code='$code', name='$pname', name_scientific='$scientname', \"desc\"='$desc', hscode='$hscode', productgroup='$productgroup' WHERE id='$pid'";
    $result = pg_query($con, $sqlupdateproduct) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=product';</script>";
    } else {
        echo "<script>alert('Error updating product: " . pg_last_error($con) . "');</script>";
    }
}
/*
 DeleteProduct: Delete product from tbproduct table
*/
function DeleteProduct($pid, $con) {
    $sqlproduct = "DELETE FROM tbproduct WHERE id='$pid'";
    $result = pg_query($con, $sqlproduct) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=product';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . pg_last_error($con) . "');</script>";
    }
}
/*
  ProductInfo: Get product information from tbproduct table
*/
function ProductInfo($pid, $con) {
    // Validate that $pid is not empty and is numeric
    if (empty($pid) || !is_numeric($pid)) {
        return null;
    }
    
    // Cast to integer to ensure it's a valid ID
    $pid = (int)$pid;
    $sqlproduct = "SELECT * FROM tbproduct WHERE id=$pid";
    $result = pg_query($con, $sqlproduct) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    }
    return null;
}

/*
  ProductId: Get product ID from tbproduct table by name and scientific name
*/
function ProductId($name, $scientific_name, $con) {
    $name = pg_escape_string($con, $name);
    $scientific_name = pg_escape_string($con, $scientific_name);
    $sqlproduct = "SELECT id FROM tbproduct WHERE name='$name' AND name_scientific='$scientific_name'";
    $result = pg_query($con, $sqlproduct) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id'];
    }
    return null;
}

/*
 ProductgroupList: Show list of product groups from tbproductgroup table
*/
function ProductgroupList($con) {
    $sqlgroup = "SELECT * FROM tbproduct_group ORDER BY id ASC";
    $result = pg_query($con, $sqlgroup) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $gid = $row['id'];
            $gname = $row['title'];
            $gdesc = $row['description'];
            $gstatus = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$gname</td>
                    <td>$gdesc</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$gid' " . ($gstatus === 'yes' ? 'checked' : '') . " onchange='handleProductGroupCheckboxChange(this)'>
                      </div>    
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addProductGroupModal' 
                         data-pgroupid='$gid' 
                         data-gname='" . htmlspecialchars($gname, ENT_QUOTES) . "' 
                         data-gdesc='" . htmlspecialchars($gdesc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>   
                    </td>
                    <td>
                     <a href='masterdata.php?part=productgroup&gid=$gid&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a>
                    </td>
                    </tr>";
        }
    }
}

/*
 SelectProductgroup: Get list of product group for select option in product form
*/     
function SelectProductgroup($pgid, $con) {
    // Check if the product group ID is set
    $sql = "SELECT id, title FROM tbproduct_group ORDER BY title ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($pgid !== null && $pgid == $row['id']) ? 'selected' : '';
            echo "<option value=\"{$row['id']}\" $selected>{$row['title']}</option>";
        }
    }
}    

/*
 AddProductgroup: Add new product group into tbproductgroup table
*/
function AddProductgroup($gname, $gdesc, $con) {
    $gname = pg_escape_string($con, $gname);
    $gdesc = pg_escape_string($con, $gdesc);
    
    // Check if the product group name already exists
    $sqlgroup = "SELECT title FROM tbproduct_group WHERE title='$gname'";
    $result = pg_query($con, $sqlgroup) or die(pg_last_error());
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Product group name already exists. Please choose a different product group name.');</script>";
        return "yes"; // Indicate that the product group already exists
    } else {
        // Insert new product group
        $sqladdgroup = "INSERT INTO \"tbproduct_group\" (\"title\", \"description\", \"enabled\") VALUES ('".$gname."', '".$gdesc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdgroup) or die(pg_last_error());
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=productgroup';</script>";
        } else {
            echo "<script>alert('Error adding product group: " . pg_last_error($con) . "');</script>";
        }
    }
}   

/*
 UpdateProductgroup: Update product group from tbproductgroup table
*/
function UpdateProductgroup($gid, $gname, $gdesc, $con) {
    // Escape all inputs
    $gid = pg_escape_string($con, $gid);
    $gname = pg_escape_string($con, $gname);
    $gdesc = pg_escape_string($con, $gdesc);

    // Update the product group information
    $sqlupdategroup = "UPDATE tbproduct_group SET title='".$gname."', description='".$gdesc."' WHERE id='$gid'";
    $result = pg_query($con, $sqlupdategroup) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=productgroup';</script>";
    } else {
        echo "<script>alert('Error updating product group: " . pg_last_error($con) . "');</script>";
    }
}
/*
 DeleteProductgroup: Delete product group from tbproductgroup table
*/
function DeleteProductgroup($gid, $con) {
    $sqlgroup = "DELETE FROM tbproduct_group WHERE id='$gid'";
    $result = pg_query($con, $sqlgroup) or die(pg_last_error($con));
    if ($result) {
        echo "<script>alert('Product group deleted successfully.');</script>";
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=productgroup';</script>";
    } else {
        echo "<script>alert('Error deleting product group: " . pg_last_error($con) . "');</script>";
    }
}

/*
 ProductunitList: Show list of product units from tbproductunit table
*/
function ProductunitList($con) {
    $sqlunit = "SELECT * FROM tbproduct_unit ORDER BY id ASC";
    $result = pg_query($con, $sqlunit) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $uid = $row['id'];
            $code = $row['code'];
            $symb = $row['symb'];
            $title = $row['title'];
            $ustatus = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$symb</td>
                    <td>$title</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$uid' " . ($ustatus === 'yes' ? 'checked' : '') . " onchange='handleProductUnitCheckboxChange(this)'>
                      </div>    
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addProductUnitModal' 
                         data-punitid='$uid' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "' 
                         data-symb='" . htmlspecialchars($symb, ENT_QUOTES) . "'
                         data-title='" . htmlspecialchars($title, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>   
                    </td>
                    <td>
                     <a href='masterdata.php?part=productunit&uid=$uid&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a>    
                    </td>
                  </tr>";   
        } // end of while loop
    }   
}

/*
    AddProductunit: Add new product unit into tbproductunit table
*/
function AddProductunit($code, $symb, $title, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $symb = pg_escape_string($con, $symb);
    $title = pg_escape_string($con, $title);

    // Check if the product unit code already exists
    $sqlunit = "SELECT code FROM tbproduct_unit WHERE code='$code'";
    $result = pg_query($con, $sqlunit) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Product unit code already exists. Please choose a different product unit code.');</script>";
        return "yes"; // Indicate that the product unit already exists
    } else {
        // Insert new product unit
        $sqladdunit = "INSERT INTO \"tbproduct_unit\" (\"code\", \"symb\", \"title\", \"enabled\") 
                       VALUES ('".$code."', '".$symb."', '".$title."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdunit) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=productunit';</script>";
        } else {
            echo "<script>alert('Error adding product unit: " . pg_last_error($con) . "');</script>";
        }
    }
} 
/*
 UpdateProductunit: Update product unit from tbproductunit table 
*/ 
function UpdateProductunit($uid, $code, $symb, $title, $con) {
    // Escape all inputs
   $uid = pg_escape_string($con, $uid); // Get product unit ID from POST data
    $code = pg_escape_string($con, $code);
    $symb = pg_escape_string($con, $symb);
    $title = pg_escape_string($con, $title);

    // Update the product unit information
    $sqlupdateunit = "UPDATE tbproduct_unit SET code='$code', symb='$symb', title='$title' WHERE id='$uid'";
    $result = pg_query($con, $sqlupdateunit) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=productunit';</script>";
    } else {
        echo "<script>alert('Error updating product unit: " . pg_last_error($con) . "');</script>";
    }
}

/*
 DeleteProductunit: Delete product unit from tbproductunit table 
*/
function DeleteProductunit($uid, $con) {
    $sqlunit = "DELETE FROM tbproduct_unit WHERE id='$uid'";
    $result = pg_query($con, $sqlunit) or die(pg_last_error($con));
    if ($result) {
       // echo "<script>alert('Product unit deleted successfully.');</script>";
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=productunit';</script>";
    } else {
        echo "<script>alert('Error deleting product unit: " . pg_last_error($con) . "');</script>";
    }
}

/*
 ProductUnitName: Get product unit name from tbproductunit table
*/
function ProductUnitName($uid, $con) {
    // Validate unit ID is not empty and is numeric
    if (empty($uid) || !is_numeric($uid)) {
        return '';
    }
    
    $uid = (int)$uid; // Cast to integer for safety
    $sqlunit = "SELECT symb FROM tbproduct_unit WHERE id='$uid'";
    $result = pg_query($con, $sqlunit) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_array($result);
        return $row['symb'];
    }
    return '';
}

/*
 Conveyancelist: Show list of conveyance from tbconveyance table
*/
function Conveyancelist($uid, $con) {
    $uidParam = !empty($uid) ? '&uid=' . urlencode($uid) : '';
    $sqlconveyance = "SELECT * FROM tbconveyance ORDER BY id ASC";
    $result = pg_query($con, $sqlconveyance) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $code = $row['code'];
            $conveyance = $row['conveytype'];
            $desc = $row['description'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$conveyance</td>
                    <td>$desc</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleConveyanceCheckboxChange(this)'>
                      </div>    
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addConveyenceModal' 
                         data-cid='$id' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "' 
                         data-cvtype='" . htmlspecialchars($conveyance, ENT_QUOTES) . "'
                         data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>   
                    </td>
                    <td><a href='masterdata.php?part=conveyance&cid=$id&del=yes$uidParam' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}

/*
 AddConveyance: Add new conveyance into tbconveyance table
*/
function AddConveyance($uid, $code, $conveytype, $desc, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $conveytype = pg_escape_string($con, $conveytype);
    $desc = pg_escape_string($con, $desc);

    // Check if the conveyance code already exists
    $sqlconveyance = "SELECT code FROM tbconveyance WHERE code='$code'";
    $result = pg_query($con, $sqlconveyance) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Conveyance code already exists. Please choose a different conveyance code.');</script>";
        return "yes"; // Indicate that the conveyance already exists
    } else {
        // Insert new conveyance
        $sqladdconveyance = "INSERT INTO \"tbconveyance\" (\"code\", \"conveytype\", \"description\", \"enabled\") 
                             VALUES ('".$code."', '".$conveytype."', '".$desc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdconveyance) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=conveyance&uid=" . urlencode($uid) . "';</script>";
        } else {
            echo "<script>alert('Error adding conveyance: " . pg_last_error($con) . "');</script>";
        }
    }
}  
/*
 UpdateConveyance: Update conveyance from tbconveyance table 
*/
function UpdateConveyance($cid, $code, $conveytype, $desc, $con) {
    // Escape all inputs
    $cid = pg_escape_string($con, $cid); // Get conveyance ID from POST data
    $code = pg_escape_string($con, $code);
    $conveytype = pg_escape_string($con, $conveytype);
    $desc = pg_escape_string($con, $desc);

    // Update the conveyance information
    $sqlupdateconveyance = "UPDATE tbconveyance SET code='$code', conveytype='$conveytype', description='$desc' WHERE id='$cid'";
    $result = pg_query($con, $sqlupdateconveyance) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=conveyance';</script>";
    } else {
        echo "<script>alert('Error updating conveyance: " . pg_last_error($con) . "');</script>";
    }
} 

/*
 DeleteConveyance: Delete conveyance from tbconveyance table 
*/
function DeleteConveyance($cid, $con) {
    $sqlconveyance = "DELETE FROM tbconveyance WHERE id='$cid'";
    $result = pg_query($con, $sqlconveyance) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=conveyance';</script>";
    } else {
        echo "<script>alert('Error deleting conveyance: " . pg_last_error($con) . "');</script>";
    }
}

/*
 ConveyanceName: Get conveyance name from tbconveyance table
*/
function ConveyanceType($id, $con) {
    $sql = "SELECT conveytype FROM tbconveyance WHERE id='$id'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['conveytype'];
    }
    return null;
}

/*
 InspectionMethodList: Show list of inspection methods from tbinspectionmethod table
*/
function InspectionMethodList($userid, $con) {
    $sqlmethod = "SELECT * FROM tbinspection_method ORDER BY id ASC";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $code = $row['code'];
            $method = $row['title'];
            $desc = $row['description'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$method</td>
                    <td>$desc</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleInspectionMethodCheckboxChange(this)'>
                      </div>
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addInspectionMethodModal' 
                         data-imid='$id' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "' 
                         data-name='" . htmlspecialchars($method, ENT_QUOTES) . "'
                         data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>   
                    </td>
                    <td><a href='masterdata.php?part=inspectionmethod&mid=$id&del=yes&uid=$userid' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}

/*
 AddInspectionMethod: Add new inspection method into tbinspectionmethod table 
*/
function AddInspectionMethod($code, $method, $desc, $userid, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $method = pg_escape_string($con, $method);
    $desc = pg_escape_string($con, $desc);

    // Check if the inspection method code already exists
    $sqlmethod = "SELECT code FROM tbinspection_method WHERE code='$code'";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Inspection method code already exists. Please choose a different inspection method code.');</script>";
        return "yes"; // Indicate that the inspection method already exists
    } else {
        // Insert new inspection method
        $sqladdmethod = "INSERT INTO \"tbinspection_method\" (\"code\", \"title\", \"description\", \"enabled\") 
                         VALUES ('".$code."', '".$method."', '".$desc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdmethod) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=inspectionmethod&uid=" . $userid . "';</script>";
        } else {
            echo "<script>alert('Error adding inspection method: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateInspectionMethod: Update inspection method from tbinspectionmethod table 
*/
function UpdateInspectionMethod($mid, $code, $method, $desc, $userid, $con) {
    // Escape all inputs
    $mid = pg_escape_string($con, $mid); // Get inspection method ID from POST data
    $code = pg_escape_string($con, $code);
    $method = pg_escape_string($con, $method);
    $desc = pg_escape_string($con, $desc);

    // Update the inspection method information
    $sqlupdatemethod = "UPDATE tbinspection_method SET code='$code', title='$method', description='$desc' WHERE id='$mid'";
    $result = pg_query($con, $sqlupdatemethod) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=inspectionmethod&uid=" . $userid . "';</script>";
    } else {
        echo "<script>alert('Error updating inspection method: " . pg_last_error($con) . "');</script>";
    }
}

/*
 DeleteInspectionMethod: Delete inspection method from tbinspectionmethod table 
*/
function DeleteInspectionMethod($mid, $userid, $con) {
    $sqlmethod = "DELETE FROM tbinspection_method WHERE id='$mid'";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=inspectionmethod&uid=" . $userid . "';</script>";
    } else {
        echo "<script>alert('Error deleting inspection method: " . pg_last_error($con) . "');</script>";
    }
}

/*
  SelectInspectionMethod: Show list of inspection methods from tbinspectionmethod table
*/
function SelectInspectionMethod($selectedMethodId, $con) {
    $sqlmethod = "SELECT * FROM tbinspection_method ORDER BY id ASC";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $id = $row['id'];
            $code = $row['code'];
            $method = $row['title'];
            $desc = $row['description'];
            $selected = ($id == $selectedMethodId) ? 'selected' : '';
            echo "<option value='$id' $selected>$method</option>";
        }
    }
}

/*
  Inspection_TreatmentList_items($guid, $con, $userid): Show list of treatment methods from tbtreatmentmethod table for items form
*/
function Inspection_TreatmentList($guid, $con, $userid) {

   if (empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid group ID provided.');</script>";
        return;
    }

    $sqlmethod = "SELECT id, application_id, inspection_date, (SELECT title FROM tbinspection_method WHERE tbinspection_method.id = tbinspection.inspection_method) AS inspection_method, treatment_date, 
     (SELECT title FROM tbtreatment_method WHERE tbtreatment_method.id = tbinspection.treatment_method) AS treatment_method,
     (SELECT application_date FROM tbapplication WHERE tbapplication.id = tbinspection.application_id) AS application_date,
     (SELECT company_id FROM tbapplication WHERE tbapplication.id = tbinspection.application_id) AS company_id, 
     (SELECT pestid FROM tbpest_detected WHERE tbinspection.application_id = tbpest_detected.application_id ORDER BY id DESC LIMIT 1) AS pestid FROM tbinspection WHERE (SELECT guid FROM tbapplication 
     WHERE tbapplication.id = tbinspection.application_id) = '$guid' AND pest_detected ='1' and treat_ability='1' ORDER BY application_id DESC";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $id = $row['id'];
            $appid = $row['application_id'];
            
            // Get application date
            $appdate = htmlspecialchars($row['application_date'], ENT_QUOTES);
            $appdate = date('d/m/Y', strtotime($appdate));
           
            // Get exporter name
            $comid = htmlspecialchars($row['company_id'], ENT_QUOTES);
            $rows = EntityExportInfo($comid, $con);
            $exporter = $rows['title'] ?? 'Unknown';

            $inspection_date = htmlspecialchars($row['inspection_date'], ENT_QUOTES);
            $inspection_date = date('d/m/Y', strtotime($inspection_date));
            $inspection_method = $row['inspection_method'];
            $treatment_date = htmlspecialchars($row['treatment_date'], ENT_QUOTES);
            $treatment_date = date('d/m/Y', strtotime($treatment_date));
            $treatment_method = $row['treatment_method'];
            $pestid = $row['pestid'] ?? '';

            // Get pest detected and treatability status
            $pestname = 'Unknown Pest';
            if (!empty($pestid)) {
                $pestInfo = PestInfo($pestid, $con);
                $pestname = $pestInfo['scientificname'] ?? 'Unknown Pest';
            }
            $uid_param = $userid ? "&uid=$userid" : "";

            print "<tr>
                    <td>$appdate</td>
                    <td>$exporter</td>
                    <td>$inspection_date</td>
                    <td>$inspection_method</td>
                    <td>$pestname</td>
                    <td>$treatment_date</td>
                    <td>$treatment_method</td>
                    <td><a href='transaction.php?part=inspection&appid=$appid$uid_param&inspect=View/Edit'>View/Edit</a></td>
                    </tr>";
        } // end of while loop
    }
}
/*
  TreatmentMethodList($con): Show list of treatment methods from tbtreatmentmethod table
*/
function TreatmentMethodList($con) {
    $sqlmethod = "SELECT * FROM tbtreatment_method ORDER BY id ASC";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $code = $row['code'];
            $method = $row['title'];
            $desc = $row['description'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$method</td>
                    <td>$desc</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleTreatmentMethodCheckboxChange(this)'>
                      </div>
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addTreatmentMethodModal' 
                         data-tmid='$id' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "' 
                         data-name='" . htmlspecialchars($method, ENT_QUOTES) . "'
                         data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>   
                    </td>
                    <td><a href='masterdata.php?part=treatmentmethod&tmid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}
/*
 AddTreatmentMethod: Add new treatment method into tbtreatmentmethod table 
*/
function AddTreatmentMethod($huid,$code, $method, $desc, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $method = pg_escape_string($con, $method);
    $desc = pg_escape_string($con, $desc);

    // Check if the treatment method code already exists
    $sqlmethod = "SELECT code FROM tbtreatment_method WHERE code='$code'";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Treatment method code already exists. Please choose a different treatment method code.');</script>";
        return "yes"; // Indicate that the treatment method already exists
    } else {
        // Insert new treatment method
        $sqladdmethod = "INSERT INTO \"tbtreatment_method\" (\"code\", \"title\", \"description\", \"enabled\") 
                         VALUES ('".$code."', '".$method."', '".$desc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdmethod) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=treatmentmethod&uid=$huid';</script>";
        } else {
            echo "<script>alert('Error adding treatment method: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateTreatmentMethod: Update treatment method from tbtreatmentmethod table 
*/
function UpdateTreatmentMethod($huid, $tmid, $code, $method, $desc, $con) {
    // Escape all inputs
    $tmid = pg_escape_string($con, $tmid); // Get treatment method ID from POST data
    $code = pg_escape_string($con, $code);
    $method = pg_escape_string($con, $method);
    $desc = pg_escape_string($con, $desc);

    // Update the treatment method information
    $sqlupdatemethod = "UPDATE tbtreatment_method SET code='$code', title='$method', description='$desc' WHERE id='$tmid'";
    $result = pg_query($con, $sqlupdatemethod) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=treatmentmethod&uid=$huid';</script>";
    } else {
        echo "<script>alert('Error updating treatment method: " . pg_last_error($con) . "');</script>";
    }
}

/*
 DeleteTreatmentMethod: Delete treatment method from tbtreatmentmethod table 
*/
function DeleteTreatmentMethod($tmid, $con) {
    $sqlmethod = "DELETE FROM tbtreatment_method WHERE id='$tmid'";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=treatmentmethod';</script>";
    } else {
        echo "<script>alert('Error deleting treatment method: " . pg_last_error($con) . "');</script>";
    }
}

/*
    SelectTreatmentMethod: Show list of treatment methods from tbtreatmentmethod table
*/
function SelectTreatmentMethod($selectedId, $con) {
    $sqlmethod = "SELECT id, title FROM tbtreatment_method WHERE enabled='yes' ORDER BY title ASC";
    $result = pg_query($con, $sqlmethod) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $id = $row['id'];
            $title = $row['title'];
            $selected = ($id == $selectedId) ? 'selected' : '';
            echo "<option value='$id' $selected>$title</option>";
        }
    }
}

/*
 TreatmentMethodInfo: Get treatment method information from tbtreatmentmethod table
*/
function TreatmentMethodInfo($id, $con) {
    // Validate ID is not empty and is numeric
    if (empty($id) || !is_numeric($id)) {
        return null;
    }
    
    $sql = "SELECT * FROM tbtreatment_method WHERE id = '$id'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    }
    return null;
}

/*
 EntityTypeList: Show list of entity types from tbentitytype table
*/
function EntityTypeList($con) {
    $sqltype = "SELECT * FROM tbentity_type ORDER BY id ASC";
    $result = pg_query($con, $sqltype) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $code = $row['code'];
            $type = $row['title'];
            $desc = $row['description'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$type</td>
                    <td>$desc</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleEntityTypeCheckboxChange(this)'>
                      </div>
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addEntityTypeModal' 
                         data-etid='$id' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "' 
                         data-name='" . htmlspecialchars($type, ENT_QUOTES) . "'
                         data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>
                    </td>
                    <td><a href='masterdata.php?part=entitytype&etid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}
/*
 AddEntityType: Add new entity type into tbentitytype table 
*/
function AddEntityType($code, $type, $desc, $userid, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $type = pg_escape_string($con, $type);
    $desc = pg_escape_string($con, $desc);

    // Check if the entity type code already exists
    $sqltype = "SELECT code FROM tbentity_type WHERE code='$code'";
    $result = pg_query($con, $sqltype) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Entity type code already exists. Please choose a different entity type code.');</script>";
        return "yes"; // Indicate that the entity type already exists
    } else {
        // Insert new entity type
        $sqladdtype = "INSERT INTO \"tbentity_type\" (\"code\", \"title\", \"description\", \"enabled\") 
                       VALUES ('".$code."', '".$type."', '".$desc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdtype) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=entitytype&uid=$userid';</script>";
        } else {
            echo "<script>alert('Error adding entity type: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateEntityType: Update entity type from tbentitytype table 
*/
function UpdateEntityType($etid, $code, $type, $desc, $userid, $con) {
    // Escape all inputs
    $etid = pg_escape_string($con, $etid); // Get entity type ID from POST data
    $code = pg_escape_string($con, $code);
    $type = pg_escape_string($con, $type);
    $desc = pg_escape_string($con, $desc);

    // Update the entity type information
    $sqlupdatetype = "UPDATE tbentity_type SET code='$code', title='$type', description='$desc' WHERE id='$etid'";
    $result = pg_query($con, $sqlupdatetype) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=entitytype&uid=$userid';</script>";
    } else {
        echo "<script>alert('Error updating entity type: " . pg_last_error($con) . "');</script>";
    }
}
/*
 DeleteEntityType: Delete entity type from tbentitytype table 
*/
function DeleteEntityType($etid, $con) {
    $sqltype = "DELETE FROM tbentity_type WHERE id='$etid'";
    $result = pg_query($con, $sqltype) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=entitytype';</script>";
    } else {
        echo "<script>alert('Error deleting entity type: " . pg_last_error($con) . "');</script>";
    }
}

/*
 EntityExportList($con): Show list of entities from tbentity table
*/
function EntityExportList($con, $guid, $userid) {   
    // Additional validation to ensure guid is valid
    if (empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid group ID. Please log in again.');</script>";
        return;
    }

    $sqle = "SELECT * FROM tbentity_export WHERE created_guid='$guid' ORDER BY id DESC";
    $result = pg_query($con, $sqle) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $title = $row['title'];
            $address = $row['address'];
            $province = Provincename($row['province'], $con);
            //$district = $row['district'];
            $phone = $row['phone'];
            $email = $row['email'];
            $contactperson = $row['contact_name'];

            $uid = $userid;
            print "<tr>
                    <td>$i</td>
                    <td>$title</td>
                    <td>$address</td>
                    <td>$contactperson</td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td>$province</td>
                    <td><a href='entity.php?part=entity&frm=editEntity_export&id=$id&uid=$uid' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    <td align='center'><a href='transaction.php?part=application&id=$id&uid=$uid' class='btn btn-danger btn-sm'><i class='bi bi-caret-right-square-fill table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}


/*
 AddEntityExport: Add new entity export into tbentity_export table
 
*/
function AddEntityExport($bstype, $enttype, $title, $address, $zipcode, $pid, $did, $phone, $email, $contactperson, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $created_date, $guid, $con) {
    // Escape all inputs
    $bstype = pg_escape_string($con, $bstype);
    $enttype = pg_escape_string($con, $enttype);
    $title = pg_escape_string($con, $title);
    $address = pg_escape_string($con, $address);
    $zipcode = pg_escape_string($con, $zipcode);
    $province = pg_escape_string($con, $pid);
    $district = pg_escape_string($con, $did);
    $country = pg_escape_string($con, '123'); // Assuming country_id is always 123 for Lao PDR
    $phone = pg_escape_string($con, $phone);
    $email = pg_escape_string($con, $email);
    $contactperson = pg_escape_string($con, $contactperson);
   
    $isregister = pg_escape_string($con, $isregister);
    $regdate1 = pg_escape_string($con, $regdate1);
    $regdate2 = pg_escape_string($con, $regdate2);
    $checkreg = pg_escape_string($con, $checkreg);
    $gap = pg_escape_string($con, $gap);
    $license_export = pg_escape_string($con, $license_export);
    $created_date = pg_escape_string($con, $created_date);
    $guid = pg_escape_string($con, $guid);

    // Insert new entity export
    $sqladdentity = "INSERT INTO \"tbentity_export\" (\"business_type\", \"entity_type\", \"title\", \"address\", \"zipcode\", \"province\",  \"district\", \"country_id\", \"phone\", \"email\", \"contact_name\", \"registered\", \"registered_date_from\", \"registered_date_to\", \"check_list_registered\", \"license_export\", \"gap\", \"datetime_created\", \"created_guid\") 
                     VALUES ('$bstype', '$enttype', '".$title."', '".$address."', '".$zipcode."', '".$province."', '".$district."', '".$country."', '".$phone."', '".$email."', '".$contactperson."', '".$isregister."', '".$regdate1."', '".$regdate2."', '".$checkreg."', '".$license_export."', '".$gap."', '".$created_date."', '".$guid."') RETURNING id";
    $result = pg_query($con, $sqladdentity) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'entity.php?entity=export';</script>";
    } else {
        echo "<script>alert('Error adding entity export: " . pg_last_error($con) . "');</script>";
    }
} 

/*
  EntityExportInfo: Get entity export by ID
*/
function EntityExportInfo($id, $con) {
    // Validate ID is not empty and is numeric
    if (empty($id) || !is_numeric($id)) {
        return null;
    }
    
    $id = (int)$id; // Cast to integer for safety
    $sql = "SELECT * FROM tbentity_export WHERE id=$id";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // No entity found with the given ID
    }
}

/*
  UpdateEntityExport: Update entity export by ID
*/
 function UpdateEntityExport($id, $bstype, $enttype, $title, $address, $zipcode, $pid, $did, $phone, $email, $contactperson, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $userid, $con) {
    // Escape all inputs
    $id = pg_escape_string($con, $id);
    $bstype = pg_escape_string($con, $bstype);
    $enttype = pg_escape_string($con, $enttype);
    $title = pg_escape_string($con, $title);
    $address = pg_escape_string($con, $address);
    $zipcode = pg_escape_string($con, $zipcode);
    $province = pg_escape_string($con, $pid);
    $district = pg_escape_string($con, $did);
    $country = pg_escape_string($con, '123'); // Assuming country_id is always 123 for Lao PDR
    $phone = pg_escape_string($con, $phone);
    $email = pg_escape_string($con, $email);
    $contactperson = pg_escape_string($con, $contactperson);
   
    // Handle registration details
   /*
    if ($isregister === 'yes') {
        // If registered
        if (empty($regdate1) || empty($regdate2)) {
            echo "<script>alert('Please provide both registration dates.');</script>";
            return;
        }
        // Escape registration dates
        $regdate1 = pg_escape_string($con, date('Y-m-d', strtotime($regdate1)));
        $regdate2 = pg_escape_string($con, date('Y-m-d', strtotime($regdate2)));
        // Check if the registration dates are valid
        if ($regdate1 > date('Y-m-d') || ($regdate2 && ($regdate2 < date('Y-m-d') || strtotime($regdate2) < strtotime($regdate1)))) {
            echo "<script>alert('Invalid registration dates. Please check the dates.');</script>";
            return;
        }
    } else {
        // If not registered
        if (!empty($regdate1) || !empty($regdate2)) {
            echo "<script>alert('Registration dates should be empty if not registered.');</script>";
            return;
        }
    }
*/
    $isregister = pg_escape_string($con, $isregister);
    
    // Handle date formatting - remove existing quotes and reformat properly
    $regdate1_clean = str_replace("'", "", $regdate1);
    $regdate2_clean = str_replace("'", "", $regdate2);
    
    // Format dates properly for PostgreSQL
    if ($regdate1_clean === '1990-01-01' || empty($regdate1_clean)) {
        $regdate1_formatted = 'NULL';
    } else {
        $regdate1_formatted = "'" . pg_escape_string($con, $regdate1_clean) . "'";
    }
    
    if ($regdate2_clean === '1990-01-01' || empty($regdate2_clean)) {
        $regdate2_formatted = 'NULL';
    } else {
        $regdate2_formatted = "'" . pg_escape_string($con, $regdate2_clean) . "'";
    }
    
    $checkreg = pg_escape_string($con, $checkreg);
    $gap = pg_escape_string($con, $gap);
    $license_export = pg_escape_string($con, $license_export);
   
    // Update the entity export information
    $sqlupdateentity = "UPDATE tbentity_export SET business_type='$bstype', entity_type='$enttype', title='$title', address='$address', zipcode='$zipcode', province='$province', district='$district', phone='$phone', email='$email', contact_name='$contactperson', registered='$isregister', registered_date_from=$regdate1_formatted, registered_date_to=$regdate2_formatted, check_list_registered='$checkreg', license_export='$license_export', gap='$gap' WHERE id='$id'";

    // Enhanced debugging
    echo "<!-- Debug UpdateEntityExport: 
    ID: $id
    Business Type: $bstype 
    Entity Type: $enttype
    Title: $title
    Address: $address
    Phone: $phone
    Email: $email
    Registration: $isregister
    Reg Date From: $regdate1_formatted
    Reg Date To: $regdate2_formatted
    SQL: $sqlupdateentity 
    -->";
    
    echo "<script>console.log('UpdateEntityExport SQL: $sqlupdateentity');</script>";
    
    $result = pg_query($con, $sqlupdateentity);
    if ($result) {
        $affected_rows = pg_affected_rows($result);
        echo "<!-- Debug: Affected rows: $affected_rows -->";
        if ($affected_rows > 0) {
            $uid_param = $userid ? "?entity=export&uid=$userid" : "?entity=export";
            echo "<script>
                alert('Entity export updated successfully! Affected rows: $affected_rows');
                window.location.href = 'entity.php$uid_param';
            </script>";
        } else {
            echo "<script>alert('Update executed but no rows were affected. Please check if the entity ID exists.');</script>";
        }
    } else {
        $error = pg_last_error($con);
        echo "<script>alert('Error updating entity export: $error');</script>";
        echo "<!-- PostgreSQL Error: $error -->";
    }
}

/*
 EntityImportList($con): Show list of entities from tbentity_import table
*/
function EntityImportList($con, $userid = null) {
   // $guid = $_SESSION['groupid']; // already defined in entity.php

    $sqle = "SELECT * FROM tbentity_import ORDER BY id DESC";
    $result = pg_query($con, $sqle) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $title = $row['title'];
            $address = $row['address'];
            $countryname = CountryInfo($row['country_id'], $con)['title'];
            //$district = $row['district'];
            $phone = $row['phone'];
            $email = $row['email'];
            $contactperson = $row['contact_name'];

            $uid_param = $userid ? "&uid=$userid" : "";
            print "<tr>
                    <td>$i</td>
                    <td>$countryname</td>
                    <td>$title</td>
                    <td>$address</td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td>$contactperson</td>
                    <td><a href='entity.php?part=entity&frm=editEntity_import&id=$id$uid_param' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}


/*
 AddEntityImport: Add new entity import into tbentity_import table
*/
function AddEntityImport($bstype, $enttype, $title, $address, $zipcode, $pid, $did, $countryid, $phone, $email, $contactperson, $created_date, $guid, $con) {
    // Escape all inputs
    $bstype = pg_escape_string($con, $bstype);
    $enttype = pg_escape_string($con, $enttype);
    $title = pg_escape_string($con, $title);
    $address = pg_escape_string($con, $address);
    $zipcode = pg_escape_string($con, $zipcode);
    $province = pg_escape_string($con, $pid);
    $district = pg_escape_string($con, $did);
    $country = pg_escape_string($con, $countryid);
    $phone = pg_escape_string($con, $phone);
    $email = pg_escape_string($con, $email);
    $contactperson = pg_escape_string($con, $contactperson);
    $created_date = pg_escape_string($con, $created_date);
    $guid = pg_escape_string($con, $guid);

    // Insert new entity import
    $sqladdentity = "INSERT INTO \"tbentity_import\" (\"business_type\", \"entity_type\", \"title\", \"address\", \"zipcode\", \"province\",  \"district\", \"country_id\", \"phone\", \"email\", \"contact_name\", \"datetime_created\", \"created_guid\") 
                     VALUES ('$bstype', '$enttype', '".$title."', '".$address."', '".$zipcode."', '".$province."', '".$district."', '".$country."', '".$phone."', '".$email."', '".$contactperson."', '".$created_date."', '".$guid."') RETURNING id";
    $result = pg_query($con, $sqladdentity) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'entity.php?entity=import';</script>";
    } else {
        echo "<script>alert('Error adding entity import: " . pg_last_error($con) . "');</script>";
    }
}

/*
 UpdateEntityImport: Update entity import by ID
*/
function UpdateEntityImport($id, $bstype, $enttype, $title, $address, $zipcode, $pid, $did, $countryid, $phone, $email, $contactperson, $con) {
    // Escape all inputs
    $id = pg_escape_string($con, $id);
    $bstype = pg_escape_string($con, $bstype);
    $enttype = pg_escape_string($con, $enttype);
    $title = pg_escape_string($con, $title);
    $address = pg_escape_string($con, $address);
    $zipcode = pg_escape_string($con, $zipcode);
    $province = pg_escape_string($con, $pid);
    $district = pg_escape_string($con, $did);
    $country = pg_escape_string($con, $countryid);
    $phone = pg_escape_string($con, $phone);
    $email = pg_escape_string($con, $email);
    $contactperson = pg_escape_string($con, $contactperson);

    // Update entity import
    $sqlupdate = "UPDATE \"tbentity_import\" SET \"business_type\"='$bstype', \"entity_type\"='$enttype', \"title\"='$title', \"address\"='$address', \"zipcode\"='$zipcode', \"province\"='$province', \"district\"='$district', \"country_id\"='$country', \"phone\"='$phone', \"email\"='$email', \"contact_name\"='$contactperson' WHERE id='$id'";
    $result = pg_query($con, $sqlupdate) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'entity.php?entity=import';</script>";
    } else {
        echo "<script>alert('Error updating entity import: " . pg_last_error($con) . "');</script>";
    }
}

/*
   EntityImportInfo: Get entity import by ID
*/
function EntityImportInfo($id, $con) {
    // Validate ID is not empty and is numeric
    if (empty($id) || !is_numeric($id)) {
        return null;
    }
    
    $id = (int)$id; // Cast to integer for safety
    $sql = "SELECT * FROM tbentity_import WHERE id=$id";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_array($result);
    }
    return null;
}

/*
 ModuleList($con): Show list of modules from tbmodules table
*/
function ModuleList($con) {
    $sqlmodule = "SELECT * FROM tbmodules ORDER BY id ASC";
    $result = pg_query($con, $sqlmodule) or die(pg_last_error());
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_array($result)) {
            $i++;
            $id = $row['id'];
            $code = $row['code'];
            $name = $row['title'];
            $desc = $row['desc'];
            $status = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$code</td>
                    <td>$name</td>
                    <td>$desc</td>
                    <td>
                      <div class='form-check form-switch'>
                        <input class='form-check-input' role='switch' type='checkbox' id='$id' " . ($status === 'yes' ? 'checked' : '') . " onchange='handleModuleCheckboxChange(this)'>
                      </div>
                    </td>
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addModuleModal' 
                         data-mid='$id' 
                         data-code='" . htmlspecialchars($code, ENT_QUOTES) . "' 
                         data-name='" . htmlspecialchars($name, ENT_QUOTES) . "'
                         data-desc='" . htmlspecialchars($desc, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>
                    </td>
                    <td><a href='masterdata.php?part=modules&mid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}
/*
 AddModule: Add new module into tbmodules table 
*/
function AddModule($code, $name, $desc, $con) {
    // Escape all inputs
    $code = pg_escape_string($con, $code);
    $name = pg_escape_string($con, $name);
    $desc = pg_escape_string($con, $desc);

    // Check if the module code already exists
    $sqlmodule = "SELECT code FROM tbmodules WHERE code='$code'";
    $result = pg_query($con, $sqlmodule) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        echo "<script>alert('Module code already exists. Please choose a different module code.');</script>";
        return "yes"; // Indicate that the module already exists
    } else {
        // Insert new module
        $sqladdmodule = "INSERT INTO \"tbmodules\" (\"code\", \"title\", \"desc\", \"enabled\") 
                          VALUES ('".$code."', '".$name."', '".$desc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdmodule) or die(pg_last_error($con));
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=modules';</script>";
        } else {
            echo "<script>alert('Error adding module: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateModule: Update module from tbmodules table 
*/
function UpdateModule($mid, $code, $name, $desc, $con) {
    // Escape all inputs
    $mid = pg_escape_string($con, $mid); // Get module ID from POST data
    $code = pg_escape_string($con, $code);
    $name = pg_escape_string($con, $name);
    $desc = pg_escape_string($con, $desc);

    // Update the module information
    $sqlupdatemodule = "UPDATE tbmodules SET code='$code', title='$name', \"desc\"='$desc' WHERE id='$mid'";
    $result = pg_query($con, $sqlupdatemodule) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=modules';</script>";
    } else {
        echo "<script>alert('Error updating module: " . pg_last_error($con) . "');</script>";
    }
}

/*
 DeleteModule: Delete module from tbmodules table 
*/
function DeleteModule($mid, $con) {
    $sqlmodule = "DELETE FROM tbmodules WHERE id='$mid'";
    $result = pg_query($con, $sqlmodule) or die(pg_last_error($con));
    if ($result) {
        // Redirect back to the table
        echo "<script>alert('Module deleted successfully.');</script>";
        // Redirect back to the table
        echo "<script>window.location.href = 'masterdata.php?part=modules';</script>";
    } else {
        echo "<script>alert('Error deleting module: " . pg_last_error($con) . "');</script>";
    }
}

/*
 ModuleName: Get module name by id
*/
function ModuleName($mid, $con) {
    $sqlmodule = "SELECT title FROM tbmodules WHERE id='$mid'";
    $result = pg_query($con, $sqlmodule) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['title'];
    } else {
        return "Unknown Module";
    }
}

/*
  ApplicationNo: Generate application number: 00000+id+2 digits of current year + 2 digits of provinces
  In case of certificate, that is issused by pass-border, application number will be added with location code
  Example: 0000012401/LA
  For DOA, last two digits of year will be placed with 00
*/
function ApplicationNo($exporter_id, $uid, $con) {
    // Add user ID into tbapplication table first to get running number-id
    $sqlappuser = "INSERT INTO tbapplication (uid, application_no, application_date, company_id, reg_no, export_point, contact_person, address_person, phone, country_import, import_point, certificate_type, multi_item, print_support, commodity_id, name_oncertificate, name_scientific, commodity_description, quantity_net, quantity_gross, unit_id, marks_item, place_origin, conveyance_id, conveyance_sign, address_exporter, address_importer, purpose, place_quarantine, place_treatment, date_certificate, guid, place_quarantine_other, place_treatment_other, importerid) 
                    VALUES ('$uid', '', NULL, NULL, '', NULL, '', '', '', NULL, NULL, '', '', '', NULL, '', '', '', NULL, NULL, NULL, '', NULL, NULL, '', '', '', '', NULL, NULL, NULL, NULL, '', '', NULL) RETURNING id";
    $result = pg_query($con, $sqlappuser) or die(pg_last_error($con));
    if ($result) {
        $row = pg_fetch_assoc($result);
        $id = $row['id']; // Get the last inserted ID - Application ID (id - auto_increment)
        //$appno = "00000".$id;
        // date("y") will return the last two digits of the current year
        list ($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $loct_id, $status) = Updateuser_values($uid,$con);
        $rowl= Locationvariables($loct_id, $con);
        $loct_code = $rowl['lid']; // Get location code from Locationvariables function
        $loct_type = $rowl['location_type']; 
        $pid = $rowl['pid'];
        
      //  echo "<script>alert('Location Code: $loct_code, Location Type: $loct_type, Province ID: $pid');</script>";
       
        if(strlen($pid) === 1) {
            $pid = '0'.$pid; // Ensure province code is always two digits
        }
         // 1- DOA and 2 - PAFO
        if ($loct_type === "1") {  // 1 - DOA
            $province_code = '00'; // if DOA's user, use 00 for province code
        } else if( $loct_type === "2") {  // 2 - PAFO
            $province_code = $pid; // NOT CORRECT -if PAFO's user, use 01 for province code
        } else if ($loct_type === "3") { // 3 - PASS-BORDER
            $province_code = $pid."/".$loct_code; // if PASS-BORDER's user
        } 
        // Generate FULL APPLICATION NUMBER - $appno
        // $id - Application ID (id - auto_increment) itself
        $appno = str_pad($id, 6, "0", STR_PAD_LEFT)."/".date("y")."/".$province_code; // Get only 6 digits,
        return array($id, $appno); // Append current year (last two digits) and province code (01 for Vientiane Capital)
        //$currentYear = date("y");
    } else {
        echo "<script>alert('Error inserting application user: " . pg_last_error($con) . "');</script>";
        return;
    }    
}

/*
  ApplicationUpdate: Update application information by application ID
*/
function ApplicationUpdate($app_id, $data, $con) {
   
    $nfield = count($data);
   
    $rgno = $data['reg_no'] ?? '';
    $exportpoint = $data['export_point'] ?? '';
    $contactperson = $data['contact_person'] ?? '';
    $addressperson = $data['address_person'] ?? '';
    $phone = $data['phone'] ?? '';
    $countryimport = $data['country_import'] ?? '';
    $importpoint = $data['import_point'] ?? '';
    $certificatetype = $data['certificate_type'] ?? '';
    $multiitem = $data['multi_item'] ?? '';
    $printsupport = $data['print_support'] ?? '';
    $commodityid = $data['commodity_id'] ?? '';
    $nameoncertificate = $data['name_oncertificate'] ?? '';
    $namescientific = $data['name_scientific'] ?? '';
    $commoditydescription = $data['commodity_description'] ?? '';
    $quantitynet = $data['quantity_net'] ?? '';
    $quantitygross = $data['quantity_gross'] ?? '';
    $unitid = $data['unit_id'] ?? '';
    $marksitem = $data['marks_item'] ?? '';
    $placeorigin = $data['place_origin'] ?? '';
    $conveyanceid = $data['conveyance_id'] ?? '';
    $conveyancesign = $data['conveyance_sign'] ?? '';
    $addressexporter = $data['address_exporter'] ?? '';
    $addressimporter = $data['address_importer'] ?? '';
    $purpose = $data['purpose'] ?? '';
    $placequarantine = $data['place_quarantine'] ?? '';
    $placetreatment = $data['place_treatment'] ?? '';
    $datecertificate = $data['date_certificate'] ?? '';
    $placequarantineother = $data['place_quarantine_other'] ?? '';
    $placetreatmentother = $data['place_treatment_other'] ?? '';
    $importerid = $data['importerid'] ?? '';
/*
  phone = " . (empty($phone) ? "NULL" : "'" . pg_escape_string($con, $phone) . "'") . ",
                    country_import = " . (empty($countryimport) ? "NULL" : "'" . pg_escape_string($con, $countryimport) . "'") . ",
                    import_point = " . (empty($importpoint) ? "NULL" : "'" . pg_escape_string($con, $importpoint) . "'") . ",
                    certificate_type = " . (empty($certificatetype) ? "NULL" : "'" . pg_escape_string($con, $certificatetype) . "'") . ",
                    multi_item = " . (empty($multiitem) ? "NULL" : "'" . pg_escape_string($con, $multiitem) . "'") . ",
                    print_support = " . (empty($printsupport) ? "NULL" : "'" . pg_escape_string($con, $printsupport) . "'") . ",
                    commodity_id = " . (empty($commodityid) ? "NULL" : "'" . pg_escape_string($con, $commodityid) . "'") . ",
                    name_scientific = " . (empty($namescientific) ? "NULL" : " '" . pg_escape_string($con, $namescientific) . "'") . ",
                    commodity_description = " . (empty($commoditydescription) ? "NULL" : " '" . pg_escape_string($con, $commoditydescription) . "'") . ",
                    quantity_net = " . (empty($quantitynet) ? "NULL" : " '" . pg_escape_string($con, $quantitynet) . "'") . ",
                    quantity_gross = " . (empty($quantitygross) ? "NULL" : " '" . pg_escape_string($con, $quantitygross) . "'") . ",
                    unit_id = " . (empty($unitid) ? "NULL" : " '" . pg_escape_string($con, $unitid) . "'") . ",
                    marks_item = " . (empty($marksitem) ? "NULL" : " '" . pg_escape_string($con, $marksitem) . "'") . ",
                    place_origin = " . (empty($placeorigin) ? "NULL" : " '" . pg_escape_string($con, $placeorigin) . "'") . ",
                    conveyance_id = " . (empty($conveyanceid) ? "NULL" : " '" . pg_escape_string($con, $conveyanceid) . "'") . ",
                    conveyance_sign = " . (empty($conveyancesign) ? "NULL" : " '" . pg_escape_string($con, $conveyancesign) . "'") . ",
                    address_exporter = " . (empty($addressexporter) ? "NULL" : " '" . pg_escape_string($con, $addressexporter) . "'") . ",
                    address_importer = " . (empty($addressimporter) ? "NULL" : " '" . pg_escape_string($con, $addressimporter) . "'") . ",
                    purpose = " . (empty($purpose) ? "NULL" : " '" . pg_escape_string($con, $purpose) . "'") . ",
                    place_quarantine = " . (empty($placequarantine) ? "NULL" : " '" . pg_escape_string($con, $placequarantine) . "'") . ",
                    place_treatment = " . (empty($placetreatment) ? "NULL" : " '" . pg_escape_string($con, $placetreatment) . "'") . ",
                    date_certificate = " . (empty($datecertificate) ? "NULL" : " '" . pg_escape_string($con, $datecertificate) . "'") . ",
                    place_quarantine_other = " . (empty($placequarantineother) ? "NULL" : " '" . pg_escape_string($con, $placequarantineother) . "'") . ",
                    place_treatment_other = " . (empty($placetreatmentother) ? "NULL" : " '" . pg_escape_string($con, $placetreatmentother) . "'") . ",
                    importerid = " . (empty($importerid) ? "NULL" : " '" . pg_escape_string($con, $importerid) . "'") . "
*/
   // echo "<script>alert('Processing field for application_Updated: $rgno, $exportpoint, $contactperson, $addressperson, $phone, $countryimport, $importpoint, $conveyanceid');</script>";
    $sqlupdate = "UPDATE tbapplication SET 
                    reg_no = " . (empty($rgno) ? "NULL" : "'" . pg_escape_string($con, $rgno) . "'") . ",
                    export_point = " . (empty($exportpoint) ? "NULL" : "'" . pg_escape_string($con, $exportpoint) . "'") . ",
                    contact_person = " . (empty($contactperson) ? "NULL" : "'" . pg_escape_string($con, $contactperson) . "'") . ",
                    address_person = " . (empty($addressperson) ? "NULL" : "'" . pg_escape_string($con, $addressperson) . "'") . ",
                    phone = " . (empty($phone) ? "NULL" : "'" . pg_escape_string($con, $phone) . "'") . ",
                    country_import = " . (empty($countryimport) ? "NULL" : "'" . pg_escape_string($con, $countryimport) . "'") . ",
                    import_point = " . (empty($importpoint) ? "NULL" : "'" . pg_escape_string($con, $importpoint) . "'") . ",
                    certificate_type = " . (empty($certificatetype) ? "NULL" : "'" . pg_escape_string($con, $certificatetype) . "'") . ",
                    multi_item = " . (empty($multiitem) ? "NULL" : "'" . pg_escape_string($con, $multiitem) . "'") . ",
                    print_support = " . (empty($printsupport) ? "NULL" : "'" . pg_escape_string($con, $printsupport) . "'") . ",
                    commodity_id = " . (empty($commodityid) ? "NULL" : "'" . pg_escape_string($con, $commodityid) . "'") . ", 
                    name_oncertificate = " . (empty($nameoncertificate) ? "NULL" : " '" . pg_escape_string($con, $nameoncertificate) . "'") . ",
                    name_scientific = " . (empty($namescientific) ? "NULL" : " '" . pg_escape_string($con, $namescientific) . "'") . ",
                    commodity_description = " . (empty($commoditydescription) ? "NULL" : " '" . pg_escape_string($con, $commoditydescription) . "'") . ",
                    quantity_net = " . (empty($quantitynet) ? "NULL" : " '" . pg_escape_string($con, $quantitynet) . "'") . ",
                    quantity_gross = " . (empty($quantitygross) ? "NULL" : " '" . pg_escape_string($con, $quantitygross) . "'") . ",
                    unit_id = " . (empty($unitid) ? "NULL" : " '" . pg_escape_string($con, $unitid) . "'") . ",
                    marks_item = " . (empty($marksitem) ? "NULL" : " '" . pg_escape_string($con, $marksitem) . "'") . ",
                    place_origin = " . (empty($placeorigin) ? "NULL" : " '" . pg_escape_string($con, $placeorigin) . "'") . ",
                    conveyance_id = " . (empty($conveyanceid) ? "NULL" : " '" . pg_escape_string($con, $conveyanceid) . "'") . ",
                    conveyance_sign = " . (empty($conveyancesign) ? "NULL" : " '" . pg_escape_string($con, $conveyancesign) . "'") . ",
                    address_exporter = " . (empty($addressexporter) ? "NULL" : " '" . pg_escape_string($con, $addressexporter) . "'") . ",
                    address_importer = " . (empty($addressimporter) ? "NULL" : " '" . pg_escape_string($con, $addressimporter) . "'") . ",
                    purpose = " . (empty($purpose) ? "NULL" : " '" . pg_escape_string($con, $purpose) . "'") . ",
                    place_quarantine = " . (empty($placequarantine) ? "NULL" : " '" . pg_escape_string($con, $placequarantine) . "'") . ",
                    place_treatment = " . (empty($placetreatment) ? "NULL" : " '" . pg_escape_string($con, $placetreatment) . "'") . ",
                    date_certificate = " . (empty($datecertificate) ? "NULL" : " '" . pg_escape_string($con, $datecertificate) . "'") . ",
                    place_quarantine_other = " . (empty($placequarantineother) ? "NULL" : " '" . pg_escape_string($con, $placequarantineother) . "'") . ",
                    place_treatment_other = " . (empty($placetreatmentother) ? "NULL" : " '" . pg_escape_string($con, $placetreatmentother) . "'") . ",
                    importerid = " . (empty($importerid) ? "NULL" : " '" . pg_escape_string($con, $importerid) . "'") . "
                    WHERE id = '" . pg_escape_string($con, $app_id) . "'";

    $result = pg_query($con, $sqlupdate);
    if ($result) {
        $affected_rows = pg_affected_rows($result); 
        if ($affected_rows > 0) { 
            return $result;
        } else {  
            return false;
        }
    } else {
        $error = pg_last_error($con);
        return false;
    }
    /*
    // Escape application ID
    $sql = "UPDATE tbapplication SET ";
    $sets = [];
    
    foreach ($data as $key => $value) {
        $i++;
        
        if (is_null($value) || ($value === '' && in_array($key, ['reg_no', 'export_point', 'contact_person', 'address_person', 'phone', 'country_import', 'import_point', 'certificate_type', 'multi_item', 'print_support', 'commodity_id', 'name_oncertificate', 'name_scientific', 'commodity_description', 'quantity_net', 'quantity_gross', 'unit_id', 'marks_item', 'place_origin', 'conveyance_id', 'conveyance_sign', 'address_exporter', 'address_importer', 'purpose', 'place_quarantine', 'place_treatment', 'date_certificate', 'place_quarantine_other', 'place_treatment_other', 'importerid']))) {
            $sets[] = "$key = NULL";
        } else {
            $sets[] = "$key = '" . pg_escape_string($con, $value) . "'";
        }
    }
    $sql .= implode(", ", $sets);
    $sql .= " WHERE id = '" . pg_escape_string($con, $app_id) . "'";
    
    $result = pg_query($con, $sql);
    if ($result) {
        $affected_rows = pg_affected_rows($result); 
        if ($affected_rows > 0) { 
            return $result;
        } else {  
            return false;
        }
    } else {
        $error = pg_last_error($con);
        return false;
    }
    */
 
}

/*
  ApplicantInfo: Get application information by application ID
*/
function ApplicantInfo_Export($app_id, $con) {
    $sqlapp = "SELECT * FROM tbentity_export WHERE id='$app_id'";
    $result = pg_query($con, $sqlapp) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null;
    }
}

/*
  DeleteApplication: Delete application by application ID
*/
function DeleteApplication($app_id, $con) {
    $sqldel = "DELETE FROM tbapplication WHERE id='$app_id'";
    $result = pg_query($con, $sqldel) or die(pg_last_error($con));
    return $result;
}

/*
  ApplicationProductList: Product list for application-Modal form to be added into main application form
*/
function ApplicationProductList($con) {
    $sql = "SELECT * FROM tbproduct ORDER BY name ASC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $cid = htmlspecialchars($row['id'], ENT_QUOTES);
            $cname = htmlspecialchars($row['name'], ENT_QUOTES);
            $cname_scientific = htmlspecialchars($row['name_scientific'], ENT_QUOTES);
            $cdesc = htmlspecialchars($row['desc'], ENT_QUOTES);
            print "<tr>
                    <td>".$cname."</td>
                    <td>".$cname_scientific."</td>
                    <td>".$cdesc."</td>
                    <td><button type='button' name='$cid' id='$cid' class='btn btn-sm btn-danger' onclick='passCommodity(\"$cid\",\"$cname\", \"$cname_scientific\", \"$cdesc\")'>Select</button></td>
                 </tr>";
                 $i++;
        }
    }
}

/*
    ApplicationMultipleProductList: Product list for application with multiple items
*/
  function ApplicationMultipleProductList($con) {  // for searching multiple commodities
    $sql = "SELECT * FROM tbproduct ORDER BY name ASC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $cid = htmlspecialchars($row['id'], ENT_QUOTES);
            $cname = htmlspecialchars($row['name'], ENT_QUOTES);
            $cname_scientific = htmlspecialchars($row['name_scientific'], ENT_QUOTES);
            $cdesc = htmlspecialchars($row['desc'], ENT_QUOTES);
            print "<tr>
                    <td>".$cname."</td>
                    <td>".$cname_scientific."</td>
                    <td>".$cdesc."</td>
                    <td><button type='button' name='$cid' id='$cid' class='btn btn-sm btn-danger' onclick='passMulitpleCommodity(\"$cid\",\"$cname\", \"$cname_scientific\", \"$cdesc\")'>Select</button></td>
                 </tr>";
                 $i++;
        }
    }
}

/*
  MultipleProductInfo: Get multiple commodity information by commodity ID
*/
function MultipleProductList($app_id, $con) {
    // Guard against empty or invalid application IDs to avoid SQL errors
    if ($app_id === null || $app_id === '' || !is_numeric($app_id)) {
        return;
    }
    $safe_app_id = pg_escape_string($con, $app_id);
    $sql = "SELECT * FROM tbmultiple_product WHERE application_id='" . $safe_app_id . "'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $i = 0;
        while ($row = pg_fetch_assoc($result)) {
            $i++;
            $dbid = htmlspecialchars($row['id'], ENT_QUOTES);
            $productid = htmlspecialchars($row['product_id'], ENT_QUOTES);
            $productName = ProductInfo($productid, $con)['name'];
            $scientificName = ProductInfo($productid, $con)['name_scientific'];
            $number_desc = htmlspecialchars($row['number_description'], ENT_QUOTES);
            $quantitynet = htmlspecialchars($row['quantity_net'], ENT_QUOTES);
            $quantitygross = htmlspecialchars($row['quantity_gross'], ENT_QUOTES);
            $unitid = htmlspecialchars($row['unit_id'], ENT_QUOTES);
            $unitName = ProductUnitName($unitid, $con);

            print "<tr data-db-id='".$dbid."' data-product-id='".$productid."' data-unit-id='".$unitid."'>
                    <td>".$i."</td>
                    <td>".$productName."</td>
                    <td>".$scientificName."</td>
                    <td>".$number_desc."</td>
                    <td>".$quantitynet."</td>
                    <td>".$quantitygross."</td>
                    <td>".$unitName."</td>
                    <td>
                     <button type='button' class='btn btn-sm btn-warning' onclick='editProductFromDb(this)'>
                      <i class='bi bi-pencil'></i>
                    </button>
                    <button type='button' class='btn btn-sm btn-danger' onclick='deleteProductFromDb(this)'>
                      <i class='bi bi-trash'></i>
                    </button>
                    </td>
                 </tr>";
        }
    } else {
        return null;
    }
}

/*
    MultipleProductdataTable: Render tbmultiple_product rows as a data table for application form
*/
function MultipleProductdataTable($appid, $productid, $guid) {
        global $con;

        if (!isset($con) || !$con) {
                return;
        }

        if (empty($appid) || !is_numeric($appid)) {
                return;
        }

        $safe_appid = pg_escape_string($con, (string)$appid);
        $safe_guid = (isset($guid) && is_numeric($guid)) ? pg_escape_string($con, (string)$guid) : null;
        $safe_productid = (isset($productid) && is_numeric($productid)) ? pg_escape_string($con, (string)$productid) : null;

        $sql = "SELECT mp.id, mp.application_id, mp.product_id, mp.number_description, mp.quantity_net, mp.quantity_gross, mp.unit_id
                        FROM tbmultiple_product mp
                        INNER JOIN tbapplication app ON app.id = mp.application_id
                        WHERE mp.application_id = '$safe_appid'";

        if ($safe_guid !== null) {
                $sql .= " AND app.guid = '$safe_guid'";
        }

        if ($safe_productid !== null) {
                $sql .= " ORDER BY CASE WHEN mp.product_id = '$safe_productid' THEN 0 ELSE 1 END, mp.id ASC";
        } else {
                $sql .= " ORDER BY mp.id ASC";
        }

        $result = pg_query($con, $sql) or die(pg_last_error($con));
        if (pg_num_rows($result) <= 0) {
                return;
        }

                print "<style>
                                .multiple-product-table-wrapper .datatable-bottom{display:none !important;}
                                .multiple-product-table-wrapper .datatable-table th,
                                .multiple-product-table-wrapper .datatable-table td,
                                .multiple-product-table-wrapper .datatable-sorter{
                                        font-family: var(--bs-body-font-family);
                                        font-size: 1rem;
                                        font-weight: 400;
                                        color: var(--bs-body-color);
                                }
                                .multiple-product-table-wrapper .datatable-table thead th,
                                .multiple-product-table-wrapper .datatable-table thead th .datatable-sorter{
                                    font-weight: 700;
                                    background-color: var(--bs-info-bg-subtle) !important;
                                }
                            </style>
                    <div class='row mb-3 multiple-product-table-wrapper' id='multipleProductDataTableWrapper'>
                        <div class='col-sm-10 offset-sm-2'>
                            <div class='table-responsive'>
                                <table class='table datatable table-striped table-bordered table-sm multiple-product-datatable datatable-no-controls'>
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Product</th>
                                            <th>Number & Description</th>
                                            <th>Net Quantity</th>
                                            <th>Gross Quantity</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
        $i = 0;
        while ($row = pg_fetch_assoc($result)) {
            $i++;
                $id = htmlspecialchars($row['id'], ENT_QUOTES);
                $application_id = htmlspecialchars($row['application_id'], ENT_QUOTES);
                $product_id = htmlspecialchars($row['product_id'], ENT_QUOTES);
                $product_name = ProductInfo($product_id, $con)['name'] ?? 'Unknown';
                $number_description = htmlspecialchars($row['number_description'], ENT_QUOTES);
                $quantity_net = htmlspecialchars($row['quantity_net'], ENT_QUOTES);
                $quantity_gross = htmlspecialchars($row['quantity_gross'], ENT_QUOTES);
                $unit_id = htmlspecialchars($row['unit_id'], ENT_QUOTES);
                $unit_name = ProductUnitName($unit_id, $con);

                print "<tr>
                                <td align='center'>$i</td>
                                <td>$product_name</td>
                                <td>$number_description</td>
                                <td align='center'>$quantity_net</td>
                                <td align='center'>$quantity_gross</td>
                                <td align='center'>$unit_name</td>
                             </tr>";
        }

        print "      </tbody>
                                </table>
                            </div>
                        </div>
                    </div>";
}

/*
 ApplicationList: Show list of applications and their status from tbapplication
*/
function ApplicationList($guid, $con, $lang, $userid = null) {
    // Validate guid parameter - must be numeric and not empty
    if (empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid group ID provided.');</script>";
        return;
    }

    $lang_param = "&lang=$lang";

    $sql = "SELECT * FROM tbapplication WHERE guid = '$guid' ORDER BY id DESC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $id = htmlspecialchars($row['id'], ENT_QUOTES);
            $appno = htmlspecialchars($row['application_no'], ENT_QUOTES);
             $comid = htmlspecialchars($row['company_id'], ENT_QUOTES);
             $rows = EntityExportInfo($comid, $con);
            $exporter = $rows['title'] ?? '';  // Exporter's name
            $appdate = htmlspecialchars($row['application_date'], ENT_QUOTES);   
            $appdate = date('d/m/Y', strtotime($appdate));  // Format date for display
            $checkinspect = InspectionCheck($id, $con); // Check if inspection already exists for this application
            if ($checkinspect == false) { // return true or false - application is found in tbinspection
                $inspection_status = "Add";
                $certificate_status = "no inspection";
            } else {
                $inspection_status = "View/Edit";
                $checkcertificate = CertificateCheck($id, $con);
                if ($checkcertificate == true) { // return true or false - application is found in tbcertificate
                    $certificate_status = "View/Edit";
                } else {
                    $certificate_status = "Add";
                }
            }
             // Certificate status - to be implemented later
            if ($certificate_status == "Add" || $certificate_status == "View/Edit") {
                $uid_param = $userid ? "&uid=$userid" : "";
                
                $certificate_link = "<a href='transaction.php?part=certificate&appid=$id&certify=$certificate_status$uid_param$lang_param'>$certificate_status</a>";
            } else {
                $certificate_link = "<span class='text-muted'>Not ready</span>";
            }
            // Check if the certificate is printed
            $certificate_printed = CertificateStatus($id, $con);
            $certificate_final = $certificate_printed['current_status'] ?? 'Ongoing';
            
            // Create link for certificate status if not Ongoing
            if ($certificate_final !== 'Ongoing') {
                $certificate_final_display = "<a href='#' onclick='viewCertificateStatus($id); return false;' style='cursor: pointer;'><span>$certificate_final</span></a>";
            } else {
                $certificate_final_display = "<span>$certificate_final</span>";
            }

            $uid_param = $userid ? "&uid=$userid" : "";
            print "<tr>
                    <td>$appno</td>
                    <td>$exporter</td>
                    <td>$appdate</td>
                    <td><a href='transaction.php?part=application&appid_edit=$id$uid_param$lang_param'>View/Edit</a></td>
                    <td><span><a href='transaction.php?part=inspection&appid=$id&inspect=$inspection_status$uid_param$lang_param'>$inspection_status</a></span></td>
                    <td>$certificate_link</td>
                    <td>$certificate_final_display</td>
                   </tr>";
        }
    }
}

/*
 ApplicationInfo: Get application information by application ID
*/
function ApplicationInfo($app_id, $con) {
    // Validate ID is not empty and is numeric
    if (empty($app_id) || !is_numeric($app_id)) {
        return null;
    }
    
    $app_id = (int)$app_id; // Cast to integer for safety
    $sql = "SELECT * FROM tbapplication WHERE id = $app_id";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null;
    }
}

/*
 ApplicationAttachmentList: Get uploaded attachment list for a given application ID
*/
function ApplicationAttachmentList($app_id, $con) {
    if (empty($app_id) || !is_numeric($app_id)) {
        return [];
    }

    $tableCheckSql = "SELECT to_regclass('public.tbapplication_uploads') AS table_name";
    $tableCheckResult = pg_query($con, $tableCheckSql);
    if (!$tableCheckResult) {
        return [];
    }

    $tableRow = pg_fetch_assoc($tableCheckResult);
    if (empty($tableRow['table_name'])) {
        return [];
    }

    $safeAppId = pg_escape_string($con, (string)$app_id);
    $sql = "SELECT id, original_filename, file_path, mime_type, file_size, uploaded_at
            FROM tbapplication_uploads
            WHERE application_id = '$safeAppId'
            ORDER BY uploaded_at DESC, id DESC";
    $result = pg_query($con, $sql);
    if (!$result || pg_num_rows($result) <= 0) {
        return [];
    }

    $attachments = [];
    while ($row = pg_fetch_assoc($result)) {
        $attachments[] = $row;
    }

    return $attachments;
}

/*
    ApplicationList_items: Show list of applications with multiple items
*/
function ApplicationList_items($guid, $con, $userid = null) {
    // Validate guid parameter - must be numeric and not empty
    if (empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid group ID provided.');</script>";
        return;
    }

    $sql = "SELECT id, application_no, application_date, company_id, country_import, commodity_id, quantity_net, quantity_gross, unit_id, importerid 
            FROM tbapplication WHERE guid = '$guid' ORDER BY application_date DESC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $id = htmlspecialchars($row['id'], ENT_QUOTES);
            $appno = htmlspecialchars($row['application_no'], ENT_QUOTES);
            $comid = htmlspecialchars($row['company_id'], ENT_QUOTES);
            $rows = EntityExportInfo($comid, $con);
            $exporter = $rows['title'] ?? '';  // Exporter's name
            $appdate = htmlspecialchars($row['application_date'], ENT_QUOTES);   
            $appdate = date('d/m/Y', strtotime($appdate));  // Format date for display
           
            // Get country name
            $country_info = CountryInfo($row['country_import'], $con);
            $country_import = $country_info['title'] ?? 'Unknown';
            
            // Get importer name
            $importerid = $row['importerid'];
            $importer_info = CertificateImporterInfo($importerid, $con);
            $importer = $importer_info['title'] ?? '';
            
            // Combine importer name and country
          /*
            if (!empty($importer_name)) {
                $importer = htmlspecialchars($importer_name . ", " . $country_import, ENT_QUOTES);
            } else {
                $importer = htmlspecialchars($country_import, ENT_QUOTES);
            }
         */   
            $commodity_id = htmlspecialchars($row['commodity_id'], ENT_QUOTES);
            $commodity_name = ProductInfo($commodity_id, $con)['name'] ?? 'Unknown';
            $quantity_net = htmlspecialchars($row['quantity_net'], ENT_QUOTES);
            $quantity_gross = htmlspecialchars($row['quantity_gross'], ENT_QUOTES);
            $unitid = htmlspecialchars($row['unit_id'], ENT_QUOTES);
            $unitName = ProductUnitName($unitid, $con);
            $community_name = $commodity_name . " (" .$quantity_net. " & " .$quantity_gross. " " . $unitName . ")";

            $uid_param = $userid ? "&uid=$userid" : "";
            print "<tr>
                    <td>$appdate</td>
                    <td>$exporter</td>
                    <td>$importer</td>
                     <td>$country_import</td>
                    <td>$community_name</td>
                    <td><a href='transaction.php?part=application&appid_edit=$id$uid_param'>View/Edit</a></td>
                   </tr>";
        }
    }
}
/*
 InspectionAdd: Add data on inspection results into tbinspection
 */
function InspectionAdd($data, $con) {
    // $data should be an associative array: column => value
    $columns = [];
    $values = [];
    foreach ($data as $key => $value) {
        $columns[] = "\"$key\"";
        if (is_null($value)) {
            $values[] = "NULL";
        } else {
            $values[] = "'" . pg_escape_string($con, $value) . "'";
        }
    }
    $sql = "INSERT INTO \"tbinspection\" (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ") RETURNING id";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result) {
        $row = pg_fetch_assoc($result);
        return $row['id'];
    } else {
        return false;
    }
}

/*
 InspectionUpdate: Update inspection information by inspection ID
*/
function InspectionUpdate($app_id, $data, $con) {
    $sets = [];
    foreach ($data as $key => $value) {
        if (is_null($value) || $value === '') {
            $sets[] = "\"$key\" = NULL";
        } else {
            $sets[] = "\"$key\" = '" . pg_escape_string($con, $value) . "'";
        }
    }
    $sql = "UPDATE tbinspection SET " . implode(", ", $sets) . " WHERE application_id = '" . pg_escape_string($con, $app_id) . "'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    return $result;
}

/*
  InspectionCheck: Check if inspection already exists for a given application ID
*/
function InspectionCheck($app_id, $con) {
    $sql = "SELECT * FROM tbinspection WHERE application_id = '$app_id'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

/*
  InspectionInfo: Get inspection information by application ID
*/
function InspectionInfo($app_id, $con) {
    $sql = "SELECT * FROM tbinspection WHERE application_id = '$app_id'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row;
    } else {
        return null;
    }
}

/*
 InspectionList_items: Show list of inspections with their status from tbinspection
*/
function InspectionList_items($guid, $con, $userid = null) {
    // Validate guid parameter - must be numeric and not empty
    if (empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid group ID provided.');</script>";
        return;
    }

    // Query tbinspection joined with tbapplication to filter by guid
    $sql = "SELECT id, application_id, inspection_date, sample_collected_by, inspected_by, lot_number, 
                   (SELECT application_date FROM tbapplication WHERE tbapplication.id = tbinspection.application_id) AS application_date,
                   (SELECT company_id FROM tbapplication WHERE tbapplication.id = tbinspection.application_id) AS company_id
            FROM tbinspection
            WHERE (SELECT guid FROM tbapplication WHERE tbapplication.id = tbinspection.application_id) = '$guid' 
            ORDER BY inspection_date DESC";
    
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $id = htmlspecialchars($row['id'], ENT_QUOTES);
            $application_id = htmlspecialchars($row['application_id'], ENT_QUOTES);
            
            // Get application date
            $appdate = htmlspecialchars($row['application_date'], ENT_QUOTES);
            $appdate = date('d/m/Y', strtotime($appdate));

            // Sample collected by - to be implemented later
            $sample_collectedby = htmlspecialchars($row['sample_collected_by'], ENT_QUOTES);
            
            // Get exporter name
            $comid = htmlspecialchars($row['company_id'], ENT_QUOTES);
            $rows = EntityExportInfo($comid, $con);
            $exporter = $rows['title'] ?? 'Unknown';
            
            // Get inspection date
            $inspection_date = $row['inspection_date'] ? date('d/m/Y', strtotime($row['inspection_date'])) : 'N/A';
            
            // Get inspector name
            $inspected_by = htmlspecialchars($row['inspected_by'], ENT_QUOTES);

            // Lot number - to be implemented later
            $lot_number = htmlspecialchars($row['lot_number'], ENT_QUOTES);

            // Pest detected - yes/no
           // $pest_detected = $row['pest_detected'] === '1' ? 'Yes' : 'No';
           // PestDetectedInfo($application_id, $con);
            
            // Lab required - yes/no
           // $lab_required = $row['lab_required'] === '1' ? 'Yes' : 'No';
            
            $uid_param = $userid ? "&uid=$userid" : "";
            
            print "<tr>
                    <td>$appdate</td>
                    <td>$exporter</td>
                    <td>$inspection_date</td>
                    <td>$sample_collectedby</td>
                    <td>$inspected_by</td>
                    <td>$lot_number</td>
                    <td><a href='transaction.php?part=inspection&appid=$application_id$uid_param&inspect=View/Edit'>View/Edit</a></td>
                   </tr>";
        }
    }
}

/*
  function CertificateNo($application_id, $uid, $con)
*/
function CertificateNo($application_id, $uid, $guid, $con) {
    // Validate parameters
    if (empty($application_id) || !is_numeric($application_id) || 
        empty($uid) || !is_numeric($uid) || 
        empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid parameters for certificate generation.');</script>";
        return array(null, null);
    }
    
    // Add user ID into tbcertificate table first to get running number-id
    $date_issued = date('Y-m-d');
    $cstatus = 'registered'; // certificate status - to be defined later
    $sql = "INSERT INTO tbcertificate (
    application_id,
    certificate_no,
    carbonpaper_id,
    approved_by,
    position_approved,
    place_issued,
    consignment_value,
    value_currency,
    additional_scientificname,
    additional_declaration,
    created_uid,
    updated_uid,
    gid,
    date_issued,
    certificate_status,
    enabled
) VALUES (
    '$application_id', '', '', NULL, '', '', NULL, '', '', '', '$uid', NULL, '$guid', '$date_issued', '$cstatus', 'yes'
) RETURNING id";

    $result = pg_query($con, $sql) or die(pg_last_error($con));


    if ($result) {
        list ($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $loct_id, $status) = Updateuser_values($uid,$con);
        $location_vars = Locationvariables($loct_id, $con);
        $loct_code = $location_vars['lid']; // Get location code from Locationvariables function
        $loct_type = $location_vars['location_type']; 
        $pid = $location_vars['pid'];
        
      //  echo "<script>alert('Location Code: $loct_code, Location Type: $loct_type, Province ID: $pid');</script>";
       
        if(strlen($pid) === 1) {
            $pid = '0'.$pid; // Ensure province code is always two digits
        }
         // 1- DOA and 2 - PAFO
        if ($loct_type === "1") {  // 1 - DOA
            $province_code = '00'; // if DOA's user, use 00 for province code
        } else if( $loct_type === "2") {  // 2 - PAFO
            $province_code = $pid; // NOT CORRECT -if PAFO's user, use 01 for province code
        } else if ($loct_type === "3") { // 3 - PASS-BORDER
            $province_code = $pid."/".$loct_code; // if PASS-BORDER's user
        } 

        $row = pg_fetch_assoc($result);
        $id = $row['id']; // Get the last inserted ID - Certificate ID (id - auto_increment)
        $certno = str_pad($id, 6, "0", STR_PAD_LEFT)."/".date("y")."/".$province_code; // Generate certificate number - 6 digits with leading zeros
        
        
        $sqlupdate = "UPDATE tbcertificate SET certificate_no='$certno' WHERE id='$id'";
        $resultupdate = pg_query($con, $sqlupdate) or die(pg_last_error($con));
        if ($resultupdate) {
            return array($id, $certno); // Return certificate ID and certificate number
        } else {
            echo "<script>alert('Error updating certificate number: " . pg_last_error($con) . "');</script>";
            return false;
        }
    } else {
        echo "<script>alert('Error inserting certificate: " . pg_last_error($con) . "');</script>";
        return;
    }
}

/*
 CertificateUpdate: Add data on certificate results into tbcertificate
*/
function CertificateUpdate($cert_id, $data, $con) { // Use certificate ID to update certificate information
    // Escape application ID
    $sql = "UPDATE tbcertificate SET ";
    $sets = [];
    foreach ($data as $key => $value) {
        if (is_null($value) || $value === '') {
            $sets[] = "\"$key\" = NULL";
        } else {
            $sets[] = "\"$key\" = '" . pg_escape_string($con, $value) . "'";
        }
    }
    $sql .= implode(", ", $sets);
    $sql .= " WHERE id = '" . pg_escape_string($con, $cert_id) . "'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    return $result;
}

/*
 CertificateCheck: Check if certificate already exists for a given application ID
*/
function CertificateCheck($app_id, $con) {
    $sql = "SELECT * FROM tbcertificate WHERE application_id = '$app_id'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

/*
  CertificateInfo: Get certificate information by application ID
*/
function CertificateInfo($app_id, $con) { // Use application ID to get certificate information
    $sql = "SELECT * FROM tbcertificate WHERE application_id = '$app_id'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row;
    } else {
        return null;
    }
}

/*
 CertificateImporterList: Show list of importers from tbentity_import table
*/
function CertificateImporterList($con) {
    $sql = "SELECT * FROM tbentity_import";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $impid = htmlspecialchars($row['id'], ENT_QUOTES);
            $impname = htmlspecialchars($row['title'], ENT_QUOTES);
            $impaddress = htmlspecialchars($row['address'], ENT_QUOTES);
            $impzipcode = htmlspecialchars($row['zipcode'], ENT_QUOTES);
            $impcountry = CountryInfo($row['country_id'], $con)['title'] ?? 'Unknown';  // Get country name
            print "<tr>
                    <td>".$impname."</td>
                    <td>".$impaddress."</td>
                    <td>".$impzipcode."</td>
                    <td>".$impcountry."</td>
                    <td><button type='button' name='$impid' id='$impid' class='btn btn-sm btn-danger' onclick='passImporter(\"$impid\",\"$impname\", \"$impaddress\", \"$impzipcode\", \"$impcountry\")'>Select</button></td>
                 </tr>";
                 $i++;
        }
    }
}

/*
 CertificateSupportingDocumentList (Multiple product): Show list of supporting documents from tbsupporting_document table
*/
function CertificateSupportingDocumentList($appid, $con) {
    $sql = "SELECT * FROM tbmultiple_product WHERE application_id = '$appid' ORDER BY id ASC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $itemid = htmlspecialchars($row['id'], ENT_QUOTES);
            $prodid = htmlspecialchars($row['product_id'], ENT_QUOTES);
            $productName = ProductInfo($prodid, $con)['name'] ?? 'NA';
            $scientificName = ProductInfo($prodid, $con)['name_scientific'] ?? 'NA';
            $nquantity = htmlspecialchars($row['quantity_net'], ENT_QUOTES);
            $gquantity = htmlspecialchars($row['quantity_gross'], ENT_QUOTES);
            $ndescription = htmlspecialchars($row['number_description'], ENT_QUOTES);
                print "<tr>
                    <td style=\"border: none;\">" . $productName . "</td>
                    <td style=\"border: none; font-style: italic;\"><em>" . $scientificName . "</em></td>
                    <td style=\"border: none;\">W.G:" . $gquantity . "<br>W.N:" . $nquantity . "</td>
                    <td style=\"border: none;\">" . $ndescription . "</td>
                 </tr>";
                 $i++;
        }
    }
}

/*
 CertificateImporterInfo: Get importer information by importer ID
*/
function CertificateImporterInfo($importer_id, $con) {

     if (empty($importer_id) || !is_numeric($importer_id)) {
        return null;
    }
    $importer_id = (int)$importer_id; // Cast to integer to ensure it's a valid ID
    $sql = "SELECT * FROM tbentity_import WHERE id = '$importer_id'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null;
    }
}

/*
  CertificateApprovedBy: SELECT -Get list of approvers from tbapprovers table
*/
function CertificateApprovedBy($con, $groupId, $selectedId = null) {
    $sql = "SELECT id, name, surname, gid FROM tbapprovers WHERE enabled = 'yes' AND gid = '$groupId' ORDER BY name ASC";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($selectedId !== null && $selectedId == $row['id']) ? 'selected' : '';
            $fullName = trim($row['name'] . ' ' . $row['surname']);
            echo "<option value=\"{$row['id']}\" $selected>$fullName</option>";
        }
    } else {
        // Debug: Show if no approvers found
        echo "<option value=\"\" disabled>No approvers available</option>";
    }
}

/*
  CertificateStatus: Get certificate status from tbcertificate_print_log table
*/
function CertificateStatus($appid, $con) {
    $sql = "SELECT * FROM tbcertificate_print_log WHERE application_id = '$appid' ORDER BY updated_at DESC LIMIT 1";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row;
    } else {
        return null;
    }
}

/*
 CertificateList: Show list of certificates and their status from tbcertificate
*/
 function CertificateList($guid, $con, $userid){
    // Validate guid parameter - must be numeric and not empty
    if (empty($guid) || !is_numeric($guid)) {
        echo "<script>alert('Invalid group ID provided.');</script>";
        return;
    }

    $sql = "SELECT id, application_id, certificate_no, carbonpaper_id, consignment_value, datetime_created, date_issued, 
            (SELECT application_date FROM tbapplication WHERE tbapplication.id = tbcertificate.application_id) AS application_date,
            (SELECT company_id FROM tbapplication WHERE tbapplication.id = tbcertificate.application_id) AS company_id 
            FROM tbcertificate WHERE (SELECT guid FROM tbapplication WHERE tbapplication.id = tbcertificate.application_id) = '$guid'  ORDER BY id DESC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $id = htmlspecialchars($row['id'], ENT_QUOTES);
            $app_id = htmlspecialchars($row['application_id'], ENT_QUOTES);
            $appdate = htmlspecialchars($row['application_date'], ENT_QUOTES);   
            $appdate = date('d/m/Y', strtotime($appdate));  // Format
            $cert_no = htmlspecialchars($row['certificate_no'], ENT_QUOTES);
            $carbonpaper_id = htmlspecialchars($row['carbonpaper_id'], ENT_QUOTES);
            $consignment_value = htmlspecialchars($row['consignment_value'], ENT_QUOTES);
            $exporter_id = htmlspecialchars($row['company_id'], ENT_QUOTES);
            $exporter = EntityExportInfo($exporter_id, $con)['title'] ?? 'Unknown';  // Exporter's name
            $date_created = htmlspecialchars($row['datetime_created'], ENT_QUOTES);   
            $date_created = date('d/m/Y', strtotime($date_created));  // Format date for display
            $date_issued = htmlspecialchars($row['date_issued'], ENT_QUOTES);   
            $date_issued = date('d/m/Y', strtotime($date_issued));  // Format date for display
            
            $uid_param = $userid ? "&uid=$userid" : "";
            print "<tr>
                    <td>$appdate</td>
                    <td>$exporter</td>
                    <td>$cert_no</td>
                    <td>$carbonpaper_id</td>
                    <td>$consignment_value</td>
                    <td>$date_created</td>
                    <td>$date_issued</td>
                    <td><a href='transaction.php?part=certificate&appid=$app_id&certify=View/Edit$uid_param'>View/Edit</a></td>
                   </tr>";
        }
    }
 }

/*
 ProvinceEntitySummaryLastSixMonths: Show province-level summary for the last 6 months
 - Counts distinct export entities from tbapplication.company_id
 - Counts distinct import entities from tbapplication.importerid
 - Sums tbcertificate.consignment_value linked by application_id
*/
function ProvinceEntitySummaryLastSixMonths($con) {
    $sql = "SELECT 
                COALESCE(p.pname, 'N/A') AS province_name,
                COUNT(DISTINCT a.company_id) AS export_count,
                COUNT(DISTINCT a.importerid) AS import_count,
                COALESCE(
                    SUM(
                        COALESCE(
                            NULLIF(regexp_replace(COALESCE(c.consignment_value::text, ''), '[^0-9.-]', '', 'g'), ''),
                            '0'
                        )::numeric
                    ),
                    0
                ) AS total_value
            FROM tbapplication a
            INNER JOIN tbentity_export ee ON ee.id = a.company_id
            LEFT JOIN tbprovinces p ON p.id = ee.province
            LEFT JOIN tbcertificate c ON c.application_id = a.id
                WHERE a.application_date >= (CURRENT_DATE - INTERVAL '6 months')
            GROUP BY p.pname
            ORDER BY p.pname ASC";

            $result = pg_query($con, $sql);
    if (!$result) {
        echo "<tr><td colspan='5' class='text-center text-danger'>Error loading summary data.</td></tr>";
        return;
    }

    if (pg_num_rows($result) === 0) {
        echo "<tr><td colspan='5' class='text-center'>No data found for the last 6 months.</td></tr>";
        return;
    }

    $i = 0;
    $total_export = 0;
    $total_import = 0;
    $total_value = 0;

    while ($row = pg_fetch_assoc($result)) {
        $i++;
        $province_name = htmlspecialchars($row['province_name'], ENT_QUOTES);
        $export_count = (int)$row['export_count'];
        $import_count = (int)$row['import_count'];
        $value_num = (float)$row['total_value'];

        $total_export += $export_count;
        $total_import += $import_count;
        $total_value += $value_num;

        $value_display = number_format($value_num, 0);

        echo "<tr>
                <td>{$i}</td>
                <td>{$province_name}</td>
                <td>{$export_count}</td>
                <td>{$import_count}</td>
                <td>{$value_display}</td>
              </tr>";
    }

    echo "<tr>
            <td colspan='2' class='text-end fw-bold'>Total</td>
            <td class='fw-bold'>" . number_format($total_export) . "</td>
            <td class='fw-bold'>" . number_format($total_import) . "</td>
                        <td class='fw-bold'>" . number_format($total_value, 0) . "</td>
          </tr>";
}

/*
 ProvinceMonthlyValueMatrixLastSixMonths: Show province rows with monthly values for last 6 months and total
*/
function ProvinceMonthlyValueMatrixLastSixMonths($con) {
    $month_keys = [];
    for ($i = 5; $i >= 0; $i--) {
        $month_keys[] = date('Y-m', strtotime("-{$i} months"));
    }

    $sql = "SELECT
                COALESCE(p.pname, 'N/A') AS province_name,
                to_char(date_trunc('month', a.application_date), 'YYYY-MM') AS month_key,
                COALESCE(
                    SUM(
                        COALESCE(
                            NULLIF(regexp_replace(COALESCE(c.consignment_value::text, ''), '[^0-9.-]', '', 'g'), ''),
                            '0'
                        )::numeric
                    ),
                    0
                ) AS month_value
            FROM tbapplication a
            INNER JOIN tbentity_export ee ON ee.id = a.company_id
            LEFT JOIN tbprovinces p ON p.id = ee.province
            LEFT JOIN tbcertificate c ON c.application_id = a.id
            WHERE a.application_date >= (date_trunc('month', CURRENT_DATE) - INTERVAL '5 months')
              AND a.application_date < (date_trunc('month', CURRENT_DATE) + INTERVAL '1 month')
            GROUP BY p.pname, date_trunc('month', a.application_date)
            ORDER BY p.pname ASC, month_key ASC";

    $result = pg_query($con, $sql);
    if (!$result) {
        echo "<tr><td colspan='9' class='text-center text-danger'>Error loading monthly summary data.</td></tr>";
        return;
    }

    if (pg_num_rows($result) === 0) {
        echo "<tr><td colspan='9' class='text-center'>No data found for the last 6 months.</td></tr>";
        return;
    }

    $province_map = [];
    while ($row = pg_fetch_assoc($result)) {
        $province = $row['province_name'];
        $month_key = $row['month_key'];
        $value = (float)$row['month_value'];

        if (!isset($province_map[$province])) {
            $province_map[$province] = array_fill_keys($month_keys, 0);
        }

        if (isset($province_map[$province][$month_key])) {
            $province_map[$province][$month_key] = $value;
        }
    }

    $i = 0;
    $month_totals = array_fill_keys($month_keys, 0);
    $grand_total = 0;
    foreach ($province_map as $province => $values_by_month) {
        $i++;
        $province_safe = htmlspecialchars($province, ENT_QUOTES);
        $row_total = 0;

        echo "<tr>";
        echo "<td>{$i}</td>";
        echo "<td>{$province_safe}</td>";

        foreach ($month_keys as $month_key) {
            $val = (float)$values_by_month[$month_key];
            $row_total += $val;
            $month_totals[$month_key] += $val;
            $display = $val > 0 ? number_format($val, 0) : '';
            echo "<td>{$display}</td>";
        }

        $grand_total += $row_total;
        echo "<td>" . number_format($row_total, 0) . "</td>";
        echo "</tr>";
    }

    echo "<tr>";
    echo "<td colspan='2' class='text-end fw-bold'>Total</td>";
    foreach ($month_keys as $month_key) {
        $month_total_display = $month_totals[$month_key] > 0 ? number_format($month_totals[$month_key], 0) : '';
        echo "<td class='fw-bold'>{$month_total_display}</td>";
    }
    echo "<td class='fw-bold'>" . number_format($grand_total, 0) . "</td>";
    echo "</tr>";
}

/*
 ProductMonthlyValueMatrixLastSixMonths: Show product rows with monthly values for last 6 months and total
*/
function ProductMonthlyValueMatrixLastSixMonths($con) {
    $month_keys = [];
    for ($i = 5; $i >= 0; $i--) {
        $month_keys[] = date('Y-m', strtotime("-{$i} months"));
    }

    $sql = "SELECT
                COALESCE(p.name, 'N/A') AS product_name,
                to_char(date_trunc('month', a.application_date), 'YYYY-MM') AS month_key,
                COALESCE(
                    SUM(
                        COALESCE(
                            NULLIF(regexp_replace(COALESCE(c.consignment_value::text, ''), '[^0-9.-]', '', 'g'), ''),
                            '0'
                        )::numeric
                    ),
                    0
                ) AS month_value
            FROM tbapplication a
            INNER JOIN tbproduct p ON p.id = a.commodity_id
            LEFT JOIN tbcertificate c ON c.application_id = a.id
            WHERE a.application_date >= (date_trunc('month', CURRENT_DATE) - INTERVAL '5 months')
              AND a.application_date < (date_trunc('month', CURRENT_DATE) + INTERVAL '1 month')
            GROUP BY p.name, date_trunc('month', a.application_date)
            ORDER BY p.name ASC, month_key ASC";

    $result = pg_query($con, $sql);
    if (!$result) {
        echo "<tr><td colspan='9' class='text-center text-danger'>Error loading product monthly summary data.</td></tr>";
        return;
    }

    if (pg_num_rows($result) === 0) {
        echo "<tr><td colspan='9' class='text-center'>No data found for the last 6 months.</td></tr>";
        return;
    }

    $product_map = [];
    while ($row = pg_fetch_assoc($result)) {
        $product = $row['product_name'];
        $month_key = $row['month_key'];
        $value = (float)$row['month_value'];

        if (!isset($product_map[$product])) {
            $product_map[$product] = array_fill_keys($month_keys, 0);
        }

        if (isset($product_map[$product][$month_key])) {
            $product_map[$product][$month_key] = $value;
        }
    }

    $i = 0;
    $month_totals = array_fill_keys($month_keys, 0);
    $grand_total = 0;

    foreach ($product_map as $product => $values_by_month) {
        $i++;
        $product_safe = htmlspecialchars($product, ENT_QUOTES);
        $row_total = 0;

        echo "<tr>";
        echo "<td>{$i}</td>";
        echo "<td>{$product_safe}</td>";

        foreach ($month_keys as $month_key) {
            $val = (float)$values_by_month[$month_key];
            $row_total += $val;
            $month_totals[$month_key] += $val;
            $display = $val > 0 ? number_format($val, 0) : '';
            echo "<td>{$display}</td>";
        }

        $grand_total += $row_total;
        echo "<td>" . number_format($row_total, 0) . "</td>";
        echo "</tr>";
    }

    echo "<tr>";
    echo "<td colspan='2' class='text-end fw-bold'>Total</td>";
    foreach ($month_keys as $month_key) {
        $month_total_display = $month_totals[$month_key] > 0 ? number_format($month_totals[$month_key], 0) : '';
        echo "<td class='fw-bold'>{$month_total_display}</td>";
    }
    echo "<td class='fw-bold'>" . number_format($grand_total, 0) . "</td>";
    echo "</tr>";
}

/*
  GenerateCertificatePDF: Generate PDF from certificate view
*/
function GenerateCertificatePDF($appid, $uid, $gid, $con) {
    // Include TCPDF
    require_once __DIR__ . '/../vendor/autoload.php';
    
    try {
        // Get certificate data
        $app_info = ApplicationInfo($appid, $con);
        $cert_info = CertificateInfo($appid, $con);
        
        if (!$app_info || !$cert_info) {
            return ['success' => false, 'error' => 'Application or certificate not found'];
        }
        
        // Generate filename
        $certificate_no = $cert_info['certificate_no'] ?? 'CERT_' . $appid;
        // Sanitize filename by replacing invalid characters
        $safe_cert_no = preg_replace('/[\/\\:*?"<>|]/', '_', $certificate_no);
        $filename = 'certificate_' . $safe_cert_no . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $filepath = __DIR__ . '/../certificate_sources/' . $filename;
        
        // Get the certificate HTML content
        ob_start();
        include(__DIR__ . '/../certificate_view_pdf.php');
        $html = ob_get_clean();
        
        // Create TCPDF instance
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Phytosanitary Certificate System');
        $pdf->SetAuthor('Ministry of Agriculture and Forestry');
        $pdf->SetTitle('Phytosanitary Certificate - ' . $certificate_no);
        $pdf->SetSubject('Phytosanitary Certificate');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins (minimal for exact positioning)
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        
        // Add a page
        $pdf->AddPage();
        
        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Save PDF file
        $pdf->Output($filepath, 'F');
        
        // Save record to database
        $db_result = SaveCertificateSource($appid, $cert_info['id'] ?? null, $uid, $gid, $filename, $con);
        
        if ($db_result['success']) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'filelink' => 'certificate_sources/' . $filename,
                'db_id' => $db_result['id']
            ];
        } else {
            // Delete file if database save failed
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            return ['success' => false, 'error' => 'Failed to save to database: ' . $db_result['error']];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'PDF Generation failed: ' . $e->getMessage()];
    }
}

/*
  SaveCertificateSource: Save certificate source record to database
*/
function SaveCertificateSource($application_id, $certificate_id, $uid, $gid, $filename, $con) {
    try {
        $application_id = (int)$application_id;
        $certificate_id = $certificate_id ? (int)$certificate_id : null;
        $uid = (int)$uid;
        $gid = (int)$gid;
        $filename = pg_escape_string($con, $filename);
        
        $cert_id_part = $certificate_id ? $certificate_id : 'NULL';
        
        $sql = "INSERT INTO tbcertificate_sources (application_id, certificate_id, uid, gid, filelink, enabled) 
                VALUES ($application_id, $cert_id_part, $uid, $gid, '$filename', 'yes') 
                RETURNING id";
        
        $result = pg_query($con, $sql);
        
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            return ['success' => true, 'id' => $row['id']];
        } else {
            return ['success' => false, 'error' => pg_last_error($con)];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/*
  GetCertificateSourceInfo: Get certificate source information by application ID
*/
function GetCertificateSourceInfo($application_id, $con) {
    try {
        $application_id = pg_escape_string($con, $application_id);
        
        $sql = "SELECT * FROM tbcertificate_sources 
                WHERE application_id = '$application_id' AND enabled = 'yes' 
                ORDER BY created_at DESC LIMIT 1";
        
        $result = pg_query($con, $sql);
        
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        } else {
            return null;
        }
        
    } catch (Exception $e) {
        return null;
    }
}

/*
  GetAllCertificateSources: Get all certificate sources for an application
*/
function GetAllCertificateSources($application_id, $con) {
    try {
        $application_id = pg_escape_string($con, $application_id);
        
        $sql = "SELECT * FROM tbcertificate_sources 
                WHERE application_id = '$application_id' AND enabled = 'yes' 
                ORDER BY created_at DESC";
        
        $result = pg_query($con, $sql);
        $sources = [];
        
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $sources[] = $row;
            }
        }
        
        return $sources;
        
    } catch (Exception $e) {
        return [];
    }
}

/*
 Approverslist: Get list of approvers from tbapprovers table
*/
 function Approverslist($gid, $con) {
    $sql = "SELECT * FROM tbapprovers WHERE enabled='yes' AND gid = '" . pg_escape_string($con, $gid) . "' ORDER BY id DESC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    $i = 0;
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $i++;
            $aid = htmlspecialchars($row['id'], ENT_QUOTES);
            $aname = htmlspecialchars($row['name'], ENT_QUOTES);
            $asurname = htmlspecialchars($row['surname'], ENT_QUOTES);
            $arole = htmlspecialchars($row['roles'], ENT_QUOTES);
            $aposition = htmlspecialchars($row['position'], ENT_QUOTES);
            $aworkplace = htmlspecialchars($row['workplace'], ENT_QUOTES);
           
            print "<tr>
                    <td align='center'>".$i."</td>
                    <td>".$aname." ".$asurname."</td>
                    <td>".$arole."</td>
                    <td>".$aposition."</td>
                    <td>".$aworkplace."</td>
                    <td><button type='button' name='$aid' id='$aid' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addApproverModal' 
  data-id='$aid' data-name='$aname' data-surname='$asurname' data-role='$arole' data-position='$aposition' data-workplace='$aworkplace'><i class='bi bi-pencil-square table-icon'></i></button></td>
                    <td><a href='masterdata.php?part=approvers&aid=$aid&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                 </tr>";
        }
    }
}

/*
  AddApprovers: Add new approver into tbapprovers table
*/
function AddApprover($name, $surname, $roles, $position, $workplace,$uid,$gid, $con) {
    $sql = "INSERT INTO tbapprovers (name, surname, roles, position, workplace, uid, gid, enabled) 
            VALUES (
                '" . pg_escape_string($con, $name) . "',
                '" . pg_escape_string($con, $surname) . "',
                '" . pg_escape_string($con, $roles) . "',
                '" . pg_escape_string($con, $position) . "',
                '" . pg_escape_string($con, $workplace) . "',
                '" . pg_escape_string($con, $uid) . "',
                '" . pg_escape_string($con, $gid) . "',
                'yes'
            )";
       
    $result = pg_query($con, $sql);
    return $result;
}

/*
  UpdateApprover: Update existing approver in tbapprovers table
*/
function UpdateApprover($id, $name, $surname, $roles, $position, $workplace, $con) {
    $sql = "UPDATE tbapprovers SET 
                name = '" . pg_escape_string($con, $name) . "',
                surname = '" . pg_escape_string($con, $surname) . "',
                roles = '" . pg_escape_string($con, $roles) . "',
                position = '" . pg_escape_string($con, $position) . "',
                workplace = '" . pg_escape_string($con, $workplace) . "'
            WHERE id = '" . pg_escape_string($con, $id) . "'";
    $result = pg_query($con, $sql);
    return $result;
}

/*
  ApproverInfo: Get data about approver from tbapprovers table
*/
function ApproverInfo($id, $con) {
    // Guard against empty or non-numeric IDs which cause PostgreSQL integer parsing errors
    if ($id === null || $id === '' || !is_numeric($id)) {
        return null;
    }
    $safe_id = pg_escape_string($con, $id);
    $sql = "SELECT * FROM tbapprovers WHERE id = '" . $safe_id . "'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null;
    }
}

/*
  ChartDocTracking: Get document tracking data for chart
*/
function ChartDocTracking($guid, $con) {
    
    // Get ALL data (not just current month) - uncomment the queries below for all-time data
    $sqlApplication = "SELECT COUNT(*) AS total_applications FROM tbapplication WHERE guid = '" . pg_escape_string($con, $guid) . "'";
    $sqlInspection = "SELECT COUNT(*) AS total_inspections FROM tbinspection i INNER JOIN tbapplication a ON i.application_id = a.id WHERE a.guid = '" . pg_escape_string($con, $guid) . "'";
    $sqlCertificate = "SELECT COUNT(*) AS total_certificates FROM tbcertificate WHERE gid = '" . pg_escape_string($con, $guid) . "'";
    
    // For current month only, use these queries instead:
    /*
    $sqlApplication = "SELECT COUNT(*) AS total_applications 
                      FROM tbapplication 
                      WHERE EXTRACT(MONTH FROM application_date) = EXTRACT(MONTH FROM CURRENT_DATE)
                      AND EXTRACT(YEAR FROM application_date) = EXTRACT(YEAR FROM CURRENT_DATE)";

    $sqlInspection = "SELECT COUNT(*) AS total_inspections 
                     FROM tbinspection 
                     WHERE EXTRACT(MONTH FROM inspection_date) = EXTRACT(MONTH FROM CURRENT_DATE)
                     AND EXTRACT(YEAR FROM inspection_date) = EXTRACT(YEAR FROM CURRENT_DATE)";
    
    $sqlCertificate = "SELECT COUNT(*) AS total_certificates 
                      FROM tbcertificate 
                      WHERE EXTRACT(MONTH FROM date_issued) = EXTRACT(MONTH FROM CURRENT_DATE)
                      AND EXTRACT(YEAR FROM date_issued) = EXTRACT(YEAR FROM CURRENT_DATE)";
    */

    // Execute queries and fetch results
    $resultApplication = pg_query($con, $sqlApplication);
    $resultInspection = pg_query($con, $sqlInspection);
    $resultCertificate = pg_query($con, $sqlCertificate);

    // Check for query errors
    if (!$resultApplication || !$resultInspection || !$resultCertificate) {
        error_log("ChartDocTracking Query Error: " . pg_last_error($con));
        return [
            'success' => false,
            'error' => 'Database query failed: ' . pg_last_error($con)
        ];
    }

    // Fetch the counts
    $rowApplication = pg_fetch_assoc($resultApplication);
    $rowInspection = pg_fetch_assoc($resultInspection);
    $rowCertificate = pg_fetch_assoc($resultCertificate);

    // Extract values from the results
    $applicationCount = $rowApplication['total_applications'] ?? 0;
    $inspectionCount = $rowInspection['total_inspections'] ?? 0;
    $certificateCount = $rowCertificate['total_certificates'] ?? 0;
    
    // Log the results
    error_log("ChartDocTracking Results - App: $applicationCount, Insp: $inspectionCount, Cert: $certificateCount");
    
    // If all counts are zero, get total counts for debugging
    if ($applicationCount == 0 && $inspectionCount == 0 && $certificateCount == 0) {
        $totalApp = pg_fetch_assoc(pg_query($con, "SELECT COUNT(*) as total FROM tbapplication WHERE guid = '" . pg_escape_string($con, $guid) . "'"))['total'] ?? 0;
        $totalInsp = pg_fetch_assoc(pg_query($con, "SELECT COUNT(*) as total FROM tbinspection"))['total'] ?? 0;
        $totalCert = pg_fetch_assoc(pg_query($con, "SELECT COUNT(*) as total FROM tbcertificate WHERE gid = '" . pg_escape_string($con, $guid) . "'"))['total'] ?? 0;
        error_log("ChartDocTracking - No data this month. Total records - App: $totalApp, Insp: $totalInsp, Cert: $totalCert");
    }

    // Free result memory
    pg_free_result($resultApplication);
    pg_free_result($resultInspection);
    pg_free_result($resultCertificate);

    return [
        'success' => true,
        'data' => [
            'application' => (int)$applicationCount,
            'inspection' => (int)$inspectionCount,
            'certificate' => (int)$certificateCount
        ]
    ];
}

/*
  CertificateStatusInfo: Get certificate print log history
  Returns array of print log records for a given application ID
*/
function CertificateStatusInfo($appid, $con) {
    // Validate input
    if (empty($appid) || !is_numeric($appid)) {
        return null;
    }
    
    // Query to get print logs with user information
    $sql = "SELECT 
                cpl.id,
                cpl.application_id,
                cpl.certificate_id,
                cpl.current_status,
                cpl.original_carbonpaper_id,
                cpl.current_carbonpaper_id,
                cpl.updated_at as print_timestamp,
                cpl.print_count,
                cpl.updated_by,
                u.id as user_id,
                u.name as user_name,
                u.surname as user_surname,
                u.email as user_email
            FROM tbcertificate_print_log cpl
            LEFT JOIN tbusers u ON cpl.updated_by = u.id
            WHERE cpl.application_id = $1
            ORDER BY cpl.id ASC";
    
    $result = pg_query_params($con, $sql, array($appid));
    
    if (!$result) {
        error_log("CertificateStatusInfo - Query failed: " . pg_last_error($con));
        return null;
    }
    
    $logs = array();
    while ($row = pg_fetch_assoc($result)) {
        $logs[] = $row;
    }
    
    pg_free_result($result);
    
    return $logs;
}

/*
    MonthlyPestDetectedChartData: Get monthly pest detection counts for last 3 months
    Returns month labels and line-series data grouped by pest scientific name
*/
function MonthlyPestDetectedChartData($guid, $con) {
    if (empty($guid) || !is_numeric($guid)) {
        return null;
    }

    $monthKeys = [];
    $monthLabels = [];
    for ($i = 2; $i >= 0; $i--) {
        $dt = strtotime("first day of -{$i} month");
        $monthKeys[] = date('Y-m', $dt);
        $monthLabels[] = date('M', $dt);
    }

    $sql = "SELECT
                TO_CHAR(DATE_TRUNC('month', i.inspection_date), 'YYYY-MM') AS month_key,
                COALESCE(NULLIF(TRIM(p.scientificname), ''), 'Unknown Pest') AS pest_name,
                COUNT(tpd.pestid)::int AS pest_count
            FROM tbinspection i
            INNER JOIN tbapplication a ON a.id = i.application_id
            INNER JOIN tbpest_detected tpd ON tpd.application_id = i.application_id
            LEFT JOIN tbpest p ON p.id = tpd.pestid
            WHERE i.inspection_date IS NOT NULL
              AND a.uid = $1
              AND i.inspection_date >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '2 months'
              AND i.inspection_date < DATE_TRUNC('month', CURRENT_DATE) + INTERVAL '1 month'
            GROUP BY month_key, pest_name
            ORDER BY pest_name, month_key";

    $result = pg_query_params($con, $sql, array($guid));
    if (!$result) {
        error_log("MonthlyPestDetectedChartData Query Error: " . pg_last_error($con));
        return [
            'success' => false,
            'error' => 'Database query failed: ' . pg_last_error($con),
            'months' => $monthLabels,
            'series' => [],
            'year' => (int)date('Y')
        ];
    }

    $seriesMap = [];
    while ($row = pg_fetch_assoc($result)) {
        $monthKey = $row['month_key'];
        $pestName = $row['pest_name'];
        $pestCount = (int)$row['pest_count'];

        if (!isset($seriesMap[$pestName])) {
            $seriesMap[$pestName] = array_fill(0, count($monthKeys), 0);
        }

        $monthIndex = array_search($monthKey, $monthKeys, true);
        if ($monthIndex !== false) {
            $seriesMap[$pestName][$monthIndex] = $pestCount;
        }
    }
    pg_free_result($result);

    $seriesData = [];
    foreach ($seriesMap as $pestName => $counts) {
        $seriesData[] = [
            'name' => $pestName,
            'data' => $counts
        ];
    }

    return [
        'success' => true,
        'months' => $monthLabels,
        'series' => $seriesData,
        'year' => (int)date('Y')
    ];
}

/*
    MonthlyPestCategoryChartData: Get monthly pest detection counts by category for last 3 months
    Returns month labels and bar-series data grouped by pest category
*/
function MonthlyPestCategoryChartData($guid, $con) {
        if (empty($guid) || !is_numeric($guid)) {
                return null;
        }

    $monthKeys = [];
    $monthLabels = [];
    for ($i = 2; $i >= 0; $i--) {
        $dt = strtotime("first day of -{$i} month");
        $monthKeys[] = date('Y-m', $dt);
        $monthLabels[] = date('M', $dt);
    }

        $sql = "SELECT
                                TO_CHAR(DATE_TRUNC('month', i.inspection_date), 'YYYY-MM') AS month_key,
                                COALESCE(NULLIF(TRIM(p.category), ''), 'Unknown Category') AS pest_category,
                                COUNT(tpd.pestid)::int AS pest_count
                        FROM tbinspection i
                        INNER JOIN tbapplication a ON a.id = i.application_id
                        INNER JOIN tbpest_detected tpd ON tpd.application_id = i.application_id
                        LEFT JOIN tbpest p ON p.id = tpd.pestid
                        WHERE i.inspection_date IS NOT NULL
                            AND a.uid = $1
                            AND i.inspection_date >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '2 months'
                            AND i.inspection_date < DATE_TRUNC('month', CURRENT_DATE) + INTERVAL '1 month'
                        GROUP BY month_key, pest_category
                        ORDER BY pest_category, month_key";

        $result = pg_query_params($con, $sql, array($guid));
    if (!$result) {
        error_log("MonthlyPestCategoryChartData Query Error: " . pg_last_error($con));
        return [
            'success' => false,
            'error' => 'Database query failed: ' . pg_last_error($con),
            'months' => $monthLabels,
            'series' => [],
            'year' => (int)date('Y')
        ];
    }

    $seriesMap = [];
    while ($row = pg_fetch_assoc($result)) {
        $monthKey = $row['month_key'];
        $pestCategory = $row['pest_category'];
        $pestCount = (int)$row['pest_count'];

        if (!isset($seriesMap[$pestCategory])) {
            $seriesMap[$pestCategory] = array_fill(0, count($monthKeys), 0);
        }

        $monthIndex = array_search($monthKey, $monthKeys, true);
        if ($monthIndex !== false) {
            $seriesMap[$pestCategory][$monthIndex] = $pestCount;
        }
    }
    pg_free_result($result);

    $seriesData = [];
    foreach ($seriesMap as $pestCategory => $counts) {
        $seriesData[] = [
            'name' => $pestCategory,
            'data' => $counts
        ];
    }

    return [
        'success' => true,
        'months' => $monthLabels,
        'series' => $seriesData,
        'year' => (int)date('Y')
    ];
}

?>

