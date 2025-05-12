<?php
/*
  Grouplist: Show list of users group from tbusersgroup table
*/
function Grouplist($con){
    $sqlgroup="SELECT * FROM tbusergroup ORDER BY id ASC";
    $result = pg_query($con,$sqlgroup) or die(pg_last_error());
    $i = 0;
    if(pg_num_rows($result) > 0) {
       while($row = pg_fetch_array($result)){
            $i++;
            $gid = $row['id'];
            $gname = $row['title'];
            $gdesc = $row['desc'];
            $gstatus = $row['enabled'];
            // Add checkbox for $gstatus
            /*
            $checkbox = ($gstatus === 'yes') 
            ? "<div class='form-check form-switch'>
                  <input class='form-check-input' role='switch' type='checkbox' id='flexSwitchCheckChecked$i' checked disabled>
                </div>"
            : "<div class='form-check form-switch'>
                  <input class='form-check-input' role='switch' type='checkbox' id='flexSwitchCheckChecked$i' disabled>
              </div>";
            */
            print "<tr>
                    <td>$i</td>
                    <td>$gname</td>
                    <td>$gdesc</td>
                    <td>";
                      if($gstatus==='yes'){
                        print "<div class='form-check form-switch'>
                                  <input class='form-check-input' role='switch' type='checkbox' id='flexSwitchCheckChecked$i'>
                              </div>";
                      }
            print "</td>
                    <td><a href='forms-usgroup.php?ug=$gid' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    <td><a href='deleteuser.php?ug=$gid' class='btn btn-danger btn-sm'><i class='bi bi-trash table-icon'></i></a></td>  
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
          // Prepare the message
          $message = "Group added successfully. Group ID: " . $last_id;
          echo "<script>alert('" . $message . "');</script>";
        } else {
          // Handle error if insertion fails
          echo "<script>alert('Error: " . pg_last_error($con) . "');</script>";
        } 
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
function Addusers($name,$surname,$sex,$uname,$psw,$phone,$email,$workplace,$utype,$status,$con){
 	// Check if the username already exists
 	$sqluser = "SELECT username FROM tbusers WHERE username='".$uname."'";
 	$result = mysqli_query($con,$sqluser) or die(mysqli_connect_error());
    $exuser = "";
    if(mysqli_num_rows($result) > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        $exuser = "yes";
        return $exuser;
    } else {
	
 	$sqladduser="INSERT INTO `tbusers`(`name`,`surname`,`sex`,`username`, `passw`, `phone`, `email`, `workplace`, `usertype`,`status`) 
    VALUES('".$name."','".$surname."','".$sex."','".$uname."','".$psw."','".$phone."','".$email."','".$workplace."','".$utype."','$status')"; 
 	   if(mysqli_query($con,$sqladduser)){ // return true;
              // Get the last inserted ID
              $last_id = mysqli_insert_id($con);
              // Prepare the message
              $message = "User added successfully. User ID: " . $last_id;
              echo "<script>alert('" . $message . "');</script>";
         } else {
              // Handle error if insertion fails
              echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
        } 
       
    }
}
/*
  Userlist: List all users from tbusers table
*/
function Userlist($con){
    $sqluserlist="SELECT * FROM tbusers ORDER BY id ASC";
    $result = pg_query($con,$sqluserlist) or die(pg_last_error());
    $i = 0;
    if(pg_num_rows($result) > 0) {
       
       while($row = pg_fetch_array($result)){
            $i++;
            $uid = $row['id'];
            $name = $row['name'];
            $surname = $row['surname'];
            //$position = $row['position']; 
            $unit = $row['unit'];
            $phone = $row['phone'];
            $email = $row['email'];
            $lastlogin = $row['last_login']; 
            $usergroup = $row['group_id']; 
            $groupadmin = $row['group_admin'];
            $location = $row['location_id'];
            print "<tr>
                    <td>$i</td>
                    <td>$name</td>
                    <td>$surname</td>
                    <td>$unit</td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td>$lastlogin</td>  
                    <td>$usergroup</td>
                    <td>$groupadmin</td>
                    <td>$location</td>
                    <td><a href='forms-usregister.php?us=$uid' class='btn btn-primary'><i class='bi bi-pencil-square table-icon'></i></a></td>
                    <td><a href='deleteuser.php?uid=$uid' class='btn btn-danger'><i class='bi bi-x-square table-icon'></i></a></td>  
                  </tr>";
       } // end of while loop     
    }
}
/*
  Updateuser: Update user from tbusers table
*/
function Updateuser($uname,$con){
  $uid = htmlspecialchars($_GET['uid']); // Sanitize the input
  $sql = "SELECT * FROM tbusers WHERE username = '$uname'";
  $result = mysqli_query($con, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
      $user = mysqli_fetch_assoc($result);

      // Pre-fill the form fields with user data
      $name = $user['name'];
      $surname = $user['surname'];
      $uname = $user['username'];
      $sex = $user['sex'];
      $psw = $user['passw'];
      $phone = $user['phone'];
      $email = $user['email'];
      $workplace = $user['workplace'];
      $utype = $user['usertype'];
      $status = $user['status'];
      return array($name, $surname, $uname, $sex, $psw, $phone, $email, $workplace, $utype, $status);
  } else {
      echo "<script>alert('User not found.');</script>";
  }
}

/*
  Updateuser-submit: Submit the updates on users from data form into tbusers table
*/
function UpdateuserSubmit($name,$surname,$sex,$uname,$psw,$phone,$email,$workplace,$utype,$status,$con){
    $sqlolduser = "SELECT username FROM tbusers WHERE username='".$uname."'";
    $result = mysqli_query($con,$sqlolduser) or die(mysqli_connect_error());    
    $exuser = "";
    if(mysqli_num_rows($result) > 0) {
        //echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        
    } 
}
?>