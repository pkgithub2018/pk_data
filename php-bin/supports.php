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
                  <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addGroupModal' data-gid='$gid' data-gname='" . htmlspecialchars($gname, ENT_QUOTES) . "' data-gdesc='" . htmlspecialchars($gdesc, ENT_QUOTES) . "''>
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

function Addusers($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $location, $con) {
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
            ('$name', '$surname', '$sex', '$psw', '$position', '$unit', '$phone', '$email', '$lastlogin', '$groupid', 'no', '$location', '$status') RETURNING id";
        $result = pg_query($con, $sqladduser);
        if ($result) {
            $last_id = pg_fetch_result($result, 0, 'id');
            //$message = "User added successfully. User ID: " . $last_id;
           // echo "<script>alert('" . $message . "');</script>";
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
      $location = $user['location_id'];
      $status = $user['enabled'];
      return array($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $location, $status);
  } else {
      echo "<script>alert('User not found.');</script>";
  }
}

/*
  Updateuser-submit: Submit the updates on users from data form into tbusers table
*/

function UpdateuserSubmit($uid, $name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $location, $con) {
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
function Locationupdate($id,$nameeng, $namelao, $loctype,$pid, $did, $con) {
   // NOT UPDATE FOR LOCATION ID
   $sql = "UPDATE tblocations SET 
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
    $sql = "SELECT name_lao FROM tblocations WHERE id = '$locid'";
    $result = pg_query($con, $sql) or die(pg_last_error($con));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['name_lao'];
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
                    <td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#addProductModal' data-pid='$id' data-pname='" . htmlspecialchars($pname, ENT_QUOTES) . "' data-code='" . htmlspecialchars($code, ENT_QUOTES) . "'>
                      <i class='bi bi-pencil-square table-icon'></i></button>
                    </td>
                    <td><a href='masterdata.php?part=products&pid=$id&del=yes' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>   
                    </tr>"; 
        } // end of while loop
    }
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
            $gdesc = $row['desc'];
            $gstatus = $row['enabled'];
            print "<tr>
                    <td>$i</td>
                    <td>$gname</td>
                    <td>$gdesc</td>
                    <td>
                    </tr>";
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
        $sqladdgroup = "INSERT INTO \"tbproduct_group\" (\"title\", \"desc\", \"enabled\") VALUES ('".$gname."', '".$gdesc."', 'yes') RETURNING id";
        $result = pg_query($con, $sqladdgroup) or die(pg_last_error());
        if ($result) {
            echo "<script>window.location.href = 'masterdata.php?part=productgroups';</script>";
        } else {
            echo "<script>alert('Error adding product group: " . pg_last_error($con) . "');</script>";
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
?> 
