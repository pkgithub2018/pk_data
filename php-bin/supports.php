<?php
/*
  Grouplist: Show list of users group from tbusersgroup table
*/
function Grouplist($con){
    $sqlgroup="SELECT * FROM tbusergroup ORDER BY id ASC";
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
  Addnewuser: Add new users into tbusers table
*/

function Addusers($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $location, $con) {
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
            echo "<script>window.location.href = 'users.php?part=userslist';</script>";
        } else {
            echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 Deleteuser: Delete users from tbusers
*/
function Deleteuser($uid,$con){
    $sql = "DELETE FROM tbusers WHERE id='$uid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'users.php?part=userslist';</script>";
    } else {
        echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
    }
}
/*
  Userlist: List all users from tbusers table
*/
function Userlist($con){
    $sqluserlist="SELECT * FROM tbusers ORDER BY id DESC"; // Order by ID in descending order
    $result = pg_query($con,$sqluserlist) or die(pg_last_error());
    $i = 0;
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
                    <td><a href='users.php?frm=userupdate&uid=$uid' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    <td><a href='users.php?frm=userdelete&uid=$uid' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>  
                  </tr>";
       } // end of while loop     
    }
}
/*
  Userdata: Get user data from tbusers table
*/
function Userdata($uid, $con) {
    $sql = "SELECT * FROM tbusers WHERE id='$uid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));

    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_array($result);
    }
    return null;
}

/*
  Updateuser_values: Update user from tbusers table
*/
function Updateuser_values($uid,$con){
  
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

function UpdateuserSubmit($uid, $name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $location, $con) {
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
                echo "<script>window.location.href='users.php?part=userslist';</script>";
            } else {
                echo "<script>alert('Error updating user: " . pg_last_error($con) . "');</script>";
            }
        } else {
            // No changes detected
            echo "<script>alert('No changes detected.');window.location.href='users.php?part=userslist';</script>";
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
function GroupPermitList($con) {
    $sqlpermit = "SELECT * FROM tbgrouppermits ORDER BY id ASC";
    $result = pg_query($con, $sqlpermit) or die(pg_last_error());
    $i = 0;
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
                      <a href='users.php?part=upermits&id=$id&epermit=edit' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a>
                    </td>
                    <td><a href='users.php?part=upermits&id=$id&dpermit=del' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
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
    $sql = "SELECT * FROM tbcountries WHERE id = '$cid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null; // Return null if no country found
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
            $selected = ($currency!== null && $currency == $row['currency']) ? 'selected' : '';
            echo "<option value=\"{$row['code']}\" $selected>{$row['country']} ({$row['currency']})</option>";
        }
    }
}

/* 
  ProductList: List all product  from tbproduct table
*/
function ProductList($con) {
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
                    <td><a href='masterdata.php?part=product&pid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>   
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
function Conveyancelist($con) {
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
                    <td><a href='masterdata.php?part=conveyance&cid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}

/*
 AddConveyance: Add new conveyance into tbconveyance table
*/
function AddConveyance($code, $conveytype, $desc, $con) {
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
            echo "<script>window.location.href = 'masterdata.php?part=conveyance';</script>";
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
 InspectionMethodList: Show list of inspection methods from tbinspectionmethod table
*/
function InspectionMethodList($con) {
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
                    <td><a href='masterdata.php?part=inspectionmethod&mid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>
                    </tr>";
        } // end of while loop
    }
}

/*
 AddInspectionMethod: Add new inspection method into tbinspectionmethod table 
*/
function AddInspectionMethod($code, $method, $desc, $con) {
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
            echo "<script>window.location.href = 'masterdata.php?part=inspectionmethod';</script>";
        } else {
            echo "<script>alert('Error adding inspection method: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateInspectionMethod: Update inspection method from tbinspectionmethod table 
*/
function UpdateInspectionMethod($mid, $code, $method, $desc, $con) {
    // Escape all inputs
    $mid = pg_escape_string($con, $mid); // Get inspection method ID from POST data
    $code = pg_escape_string($con, $code);
    $method = pg_escape_string($con, $method);
    $desc = pg_escape_string($con, $desc);

    // Update the inspection method information
    $sqlupdatemethod = "UPDATE tbinspection_method SET code='$code', title='$method', description='$desc' WHERE id='$mid'";
    $result = pg_query($con, $sqlupdatemethod) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=inspectionmethod';</script>";
    } else {
        echo "<script>alert('Error updating inspection method: " . pg_last_error($con) . "');</script>";
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
function AddTreatmentMethod($code, $method, $desc, $con) {
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
            echo "<script>window.location.href = 'masterdata.php?part=treatmentmethod';</script>";
        } else {
            echo "<script>alert('Error adding treatment method: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateTreatmentMethod: Update treatment method from tbtreatmentmethod table 
*/
function UpdateTreatmentMethod($tmid, $code, $method, $desc, $con) {
    // Escape all inputs
    $tmid = pg_escape_string($con, $tmid); // Get treatment method ID from POST data
    $code = pg_escape_string($con, $code);
    $method = pg_escape_string($con, $method);
    $desc = pg_escape_string($con, $desc);

    // Update the treatment method information
    $sqlupdatemethod = "UPDATE tbtreatment_method SET code='$code', title='$method', description='$desc' WHERE id='$tmid'";
    $result = pg_query($con, $sqlupdatemethod) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=treatmentmethod';</script>";
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
function AddEntityType($code, $type, $desc, $con) {
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
            echo "<script>window.location.href = 'masterdata.php?part=entitytype';</script>";
        } else {
            echo "<script>alert('Error adding entity type: " . pg_last_error($con) . "');</script>";
        }
    }
}
/*
 UpdateEntityType: Update entity type from tbentitytype table 
*/
function UpdateEntityType($etid, $code, $type, $desc, $con) {
    // Escape all inputs
    $etid = pg_escape_string($con, $etid); // Get entity type ID from POST data
    $code = pg_escape_string($con, $code);
    $type = pg_escape_string($con, $type);
    $desc = pg_escape_string($con, $desc);

    // Update the entity type information
    $sqlupdatetype = "UPDATE tbentity_type SET code='$code', title='$type', description='$desc' WHERE id='$etid'";
    $result = pg_query($con, $sqlupdatetype) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'masterdata.php?part=entitytype';</script>";
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
function EntityExportList($con) {
    $guid = $_SESSION['groupid']; // already defined in entity.php

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

            print "<tr>
                    <td>$i</td>
                    <td>$title</td>
                    <td>$address</td>
                    <td>$contactperson</td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td>$province</td>
                    <td><a href='entity.php?part=entity&frm=editEntity_export&id=$id' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    <td align='center'><a href='transaction.php?part=application&id=$id' class='btn btn-danger btn-sm'><i class='bi bi-caret-right-square-fill table-icon'></i></a></td>
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
  GetEntityExport: Get entity export by ID
*/
function GetEntityExport($id, $con) {
    $sql = "SELECT * FROM tbentity_export WHERE id='$id'";
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
 function UpdateEntityExport($id, $bstype, $enttype, $title, $address, $zipcode, $pid, $did, $phone, $email, $contactperson, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $con) {
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
    $regdate1 = pg_escape_string($con, $regdate1);
    $regdate2 = pg_escape_string($con, $regdate2);
    $checkreg = pg_escape_string($con, $checkreg);
    $gap = pg_escape_string($con, $gap);
    $license_export = pg_escape_string($con, $license_export);
   
    // Update the entity export information
    $sqlupdateentity = "UPDATE tbentity_export SET business_type='$bstype', entity_type='$enttype', title='$title', address='$address', zipcode='$zipcode', province='$province', district='$district', phone='$phone', email='$email', contact_name='$contactperson', registered='$isregister', registered_date_from='$regdate1', registered_date_to='$regdate2', check_list_registered='$checkreg', license_export='$license_export', gap='$gap' WHERE id='$id'";

    $result = pg_query($con, $sqlupdateentity) or die(pg_last_error($con));
    if ($result) {
        echo "<script>window.location.href = 'entity.php?entity=export';</script>";
    } else {
        echo "<script>alert('Error updating entity export: " . pg_last_error($con) . "');</script>";
    }
}

/*
 EntityImportList($con): Show list of entities from tbentity_import table
*/
function EntityImportList($con) {
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

            print "<tr>
                    <td>$i</td>
                    <td>$countryname</td>
                    <td>$title</td>
                    <td>$address</td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td>$contactperson</td>
                    <td><a href='entity.php?part=entity&frm=editEntity_import&id=$id' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
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
    $id = pg_escape_string($con, $id);
    $sql = "SELECT * FROM tbentity_import WHERE id='$id'";
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
    $sqlappuser = "INSERT INTO tbapplication (uid, application_no, application_date, company_id, reg_no, export_point, contact_person, address_person, phone, country_import, import_point, certificate_type, multi_item, print_support, commodity_id, name_oncertificate, name_scientific, commodity_description, quantity_net, quantity_gross, unit_id, marks_item, place_origin, conveyance_id, conveyance_sign, address_exporter, address_importer, purpose, place_quarantine, place_treatment, date_certificate) 
                    VALUES ('$uid', '', NULL, NULL, '', NULL, '', '', '', NULL, NULL, '', '', '', NULL, '', '', '', NULL, NULL, NULL, '', NULL, NULL, '', '', '', '', NULL, NULL, NULL) RETURNING id";
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
    // Escape application ID
    $sql = "UPDATE tbapplication SET ";
    $sets = [];
    foreach ($data as $key => $value) {
        if (is_null($value)) {
            $sets[] = "$key = NULL";
        } else {
            $sets[] = "$key = '" . pg_escape_string($value) . "'";
        }
    }
    $sql .= implode(", ", $sets);
    $sql .= " WHERE id = '" . pg_escape_string($app_id) . "'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    return $result;
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
 ApplicationList: Show list of applications and their status from tbapplication
*/
function ApplicationList($guid, $con) {

    $sql = "SELECT * FROM tbapplication WHERE guid = '$guid' ORDER BY id DESC";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $id = htmlspecialchars($row['id'], ENT_QUOTES);
            $appno = htmlspecialchars($row['application_no'], ENT_QUOTES);
             $comid = htmlspecialchars($row['company_id'], ENT_QUOTES);
             $rows = GetEntityExport($comid, $con);
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
                $certificate_link = "<a href='transaction.php?part=certificate&appid=$id&certify=$certificate_status'>$certificate_status</a>";
            } else {
                $certificate_link = "<span class='text-muted'>Not ready</span>";
            }
          
            print "<tr>
                    <td>$appno</td>
                    <td>$exporter</td>
                    <td>$appdate</td>
                    <td><a href='transaction.php?part=application&appid_edit=$id'>View/Edit</a></td>
                    <td><span><a href='transaction.php?part=inspection&appid=$id&inspect=$inspection_status'>$inspection_status</a></span></td>
                    <td>$certificate_link</td>
                    <td><span>n/a</span></td>
                   </tr>";
        }
    }
}

/*
 ApplicationInfo: Get application information by application ID
*/
function ApplicationInfo($app_id, $con) {
    $sql = "SELECT * FROM tbapplication WHERE id = '$app_id'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if (pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    } else {
        return null;
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
  function CertificateNo($application_id, $uid, $con)
*/
function CertificateNo($application_id, $uid, $guid, $con) {
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
function CertificateInfo($app_id, $con) {
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
  CertificateApprovedBy: Get list of approvers from tbapprovers table
*/
function CertificateApprovedBy($con, $selectedId = null) {
    $sql = "SELECT id, name, surname FROM tbapprovers ORDER BY name ASC";
    $result = pg_query($con, $sql);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $selected = ($selectedId !== null && $selectedId == $row['id']) ? 'selected' : '';
            $fullName = trim($row['name'] . ' ' . $row['surname']);
            echo "<option value=\"{$row['id']}\" $selected>$fullName</option>";
        }
    }
}

?>

