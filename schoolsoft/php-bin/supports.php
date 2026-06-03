<?php 
 /* 
    Userinfo : To return data about users - return multiple variables
 */
  function Userinfo($usname,$psw,$con){
	$sqluser="SELECT * FROM tbusers WHERE username='".$usname."' AND passw='".$psw."'"; 
	$ruser=mysqli_query($con,$sqluser) or die(mysqli_connect_error());
	if(mysqli_num_rows($ruser)>0){
	  list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status, $rgdate, $lupdate)=mysqli_fetch_array($ruser);
	  return array($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status, $rgdate, $lupdate);
	}
  }
/*
  Userbyid: To get user's information by ID
*/
 function Userbyid($uid, $con){
   $sqluser="SELECT * FROM tbusers WHERE id='$uid'"; 
	$ruser=mysqli_query($con,$sqluser) or die(mysqli_connect_error());
	if(mysqli_num_rows($ruser)>0){
	  list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status, $rgdate, $lupdate)=mysqli_fetch_array($ruser);
	  return array($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status, $rgdate, $lupdate);
	} 
 }
/*
  Savelessons: To save record of lessons created/uploaded
*/
  function Savelessons($subid,$ltype,$teaid,$sqnum,$topic,$desc,$fname,$fpath,$fdate,$con){
	 $sqlinless="INSERT INTO `tblessons`(`subid`, `ltype`, `teacherid`, `lesqno`, `topic`, `description`, `filename`, `filepath`, `fileupdate`) VALUES('$subid','".$ltype."','$teaid','".$sqnum."','".$topic."','".$desc."','".$fname."','".$fpath."','$fdate')"; 
	 mysqli_query($con,$sqlinless) or die(mysqli_connect_error());
	 return "saved";
  }
/*
  Dellessons: To delete lessons from tblessons and document file itself
*/
 function Dellessons($lid,$fpath,$con){
	// Delete data about file
	$sqldel="DELETE FROM tblessons WHERE id='$lid'";
	mysqli_query($con,$sqldel) or die(mysqli_connect_error());
	// Delete document file itself
	 if(file_exists($fpath)){
	   $fdel=unlink($fpath);
	  }
	 return "fdel";
 }
/*
  Rlessontype: Return type of lesson 
*/
  function Rlessontype($lsid, $con){
	$sqlls = "SELECT ltype FROM tblessontype WHERE id='$lsid'";  
	$rls = mysqli_query($con, $sqlls) or die(mysqli_connect_error());
	list($lstype) = mysqli_fetch_array($rls);
	return $lstype;
  }
/*
  Checkuserinfo: Check if key data is missing before saving it
*/
 function Checkuserinfo($nlao,$neng,$snlao,$sneng,$dbirth,$sex,$phone,$utype,$con){
   if(empty($nlao) || empty($neng) || empty($snlao) || empty($sneng) || empty($dbirth) || empty($sex) || empty($phone) || empty($utype)){
	  return "emptyinput"; 
   } else {
	  return "input";
   }
 }
/*
 Checkuexist: Check if the user already exists
              It is assumpted that user who has the same name, surname, birth date and sex
*/
 function Checkuexist($nlao,$neng,$snlao,$sneng,$dbirth,$gender,$con){
  $neng = strtoupper($neng);
  $sneng = strtoupper($sneng);
	 
  $sqlextuser = "SELECT * FROM tbusers WHERE namelao='".$nlao."' 
                 AND UPPER(nameeng)='".$neng."' 
                 AND snamelao='".$snlao."' 
				 AND UPPER(snameeng)='".$sneng."' 
				 AND dbirth='$dbirth' 
				 AND gender='".$gender."'"; 
  $rexuser = mysqli_query($con,$sqlextuser) or die(mysqli_connect_error());
  if(mysqli_num_rows($rexuser)>0){
	return "exist";
  } else {
	return "notexist";
  }
 }
/*
  Saveusers: Save data on new users
*/
function Saveusers($nlao,$neng,$snlao,$sneng,$dbirth,$sex,$phone,$email,$cadd,$utype,$con){
 	// username format: nameengid (id - autoincrement and it will be used as username when name is already added)
 	// password: 2nameeng.2snameeng@id (first 2 digits of name and first 2 digits of last name and @id)
 	$passw_int=""; // Initial password
 	$f2name=strtolower(substr($neng,0,2));
 	$f2sname=strtolower(substr($sneng,0,4));
 	$passw_int=$f2name.$f2sname;
 	$cdate= date('Y-m-d');
	
 	$sqlinuser="INSERT INTO `tbusers`(`username`, `passw`, `namelao`, `nameeng`, `snamelao`, `snameeng`, `dbirth`, `gender`, `mphone`, `email`,`address`,`usertype`,`status`,`rdate`,`lvdate`) VALUES('".$neng."','".$passw_int."','".$nlao."','".$neng."','".$snlao."','".$sneng."','$dbirth','".$sex."','".$phone."','".$email."','".$cadd."','".$utype."','enable','$cdate','$cdate')"; 
 	mysqli_query($con,$sqlinuser) or die(mysqli_connect_error());
	// Get id from table after inserting and update it
	$sqlid="SELECT id FROM tbusers WHERE username='".$neng."' AND passw='".$passw_int."' ORDER BY id ASC";
	$rid=mysqli_query($con,$sqlid) or die(mysqli_connect_error());
	list($uid)=mysqli_fetch_array($rid);
	// Update 
	$uname=$neng.$uid;
	$uname=strtolower($uname);
	$passw=$passw_int."@".$uid;
	$sqlupdate="UPDATE tbusers SET username='".$uname."', passw='".$passw."' WHERE username='".$neng."' AND passw='".$passw_int."'";
	mysqli_query($con,$sqlupdate) or die(mysqli_connect_error());
}
/*
  Showusers: Show list of users after being registered. Also, search for particular users to be edited.
  Search can be done by typing username, filtering by group of users (teachers, System Administrator, ...) 
  and by date when the users are created
*/
 function Showusers($sname,$usertype, $con){
	// $usertype : USED FOR FILTERING. if school admin-ພ/ງ ບໍລິຫານ-ສາມັນ - ເລກ 6
	// the staff will see list of school students/users ONLY
   $con->set_charset("utf8");
   if(!empty($sname)){
	 $sqlusers="SELECT * FROM tbusers WHERE namelao LIKE '$sname%'"; 
	 echo "Searching: ".$sname;
   } else {
	 switch($usertype){
	  case "6": // School admin - ພ/ງ ບໍລິຫານ-ສາມັນ
		// Show all users- ນັກຮຽນສາມັນ ເລກ 4
		$sqlusers="SELECT id,username, passw, namelao, snamelao, dbirth, gender, mphone, usertype, status FROM tbusers WHERE usertype='4' AND status='enable' ORDER BY id DESC";
		break;
	  case "7": // ພ/ງ ບໍລິຫານ-ອະນຸບານ 
		// Show all users- ນັກຮຽນອະນຸບານ ເລກ 8
		$sqlusers="SELECT id,username, passw, namelao, snamelao, dbirth, gender, mphone, usertype, status FROM tbusers WHERE usertype='8' AND status='enable' ORDER BY id DESC";
		break;
	  case "5": // ພ/ງ ບໍລິຫານ Colleges
		// Show all users- College students N= 3
		$sqlusers="SELECT id,username, passw, namelao, snamelao, dbirth, gender, mphone, usertype, status FROM tbusers WHERE usertype='3' AND status='enable' ORDER BY id DESC";
		break;
	   case "2": // System Administrator
		$sqlusers="SELECT id,username, passw, namelao, snamelao, dbirth, gender, mphone, usertype, status FROM tbusers ORDER BY id DESC";
		break;
	  }  // End of switch
	
   }
   $rusers=mysqli_query($con,$sqlusers) or die(mysqli_connect_error());
   if(mysqli_num_rows($rusers)>0){
	  
	 print "<table style='width: 100%; font-size: 10pt'>"; 
	 print "<tr><th width='8%'>ລດ</th><th width='28%'>ຊື່ ແລະ ນາມສະກຸນ</th><th width='20%'>ຊື່ຜູ້ໃຊ້</th><th width='20%'>ລະຫັດຜ່ານ</th><th>ສະຖານະ</th><th>ລຶບ</th><th>ດັດແປງ</th></tr>";
	 print "</table>";
	 print "<div style='width:100%; height:450px; overflow-x: hidden; overflow-y: auto; border-bottom: 1px solid black'>"; //***** DIV - SCROLL BAR ****************
	 print "<table class='tbus'>";  
	 $sn=0;
	 while($rw=mysqli_fetch_array($rusers)){
		 $sn=$sn + 1;
		 $id=$rw["id"];
		 $uname=$rw["username"];
		 $pw=$rw["passw"];
		 $gender="";
		 if($rw["gender"]=="m"){
		   $gender="ທ. "; 
		 } else {
		   $gender="ນ. ";
		 } 
		 $nsname =$gender." ".$rw["namelao"]." ".$rw["snamelao"]; //Full name
		 $phone=$rw["mphone"];
		 $utype=$rw["usertype"];
		 $utype=Rusertype($utype,$con);
		 $ustatus=$rw["status"];
		 if($ustatus=="enable"){
			$ustatusl="ເປີດ"; 
		 } else {
			$ustatusl="ຢຸດນໍາໃຊ້";
		 }
	  // DOUBLE CLICK ON <a> tag to change status of users
	  print "<tr><td align='center' width='8%'>$sn</td><td width='28%'>$nsname</td><td align='left' width='20%'>$uname</td><td width='20%'>$pw</td><td align='center'><a onclick='return false' ondblclick='location=this.href' href='content.php?sad=reguser&subsadst=$ustatus&userid=$id' title='ທ່ານ ກໍາລັງຈະປ່ຽນສະຖານະຂອງຜູ້ໃຊ້' style='color: black; font-size:10pt'>$ustatusl</a></td><td align='center'><a href='content.php?sad=reguser&subsadup=userdelete&userid=$id' style='color: black'><i class='fa fa-trash-o'></i></a></td><td align='center'><a href='content.php?sad=reguser&subsadup=userupdate&userid=$id' style='color: black'><i class='fa fa-fw fa-refresh'></i></a></td></tr>";
	 }
	 print "</table>";
    print "</div>"; //*************End of DIV - SCROOL BAR  
   } else {
	  echo "<div>No data found </div>";
   }
 }
 /*
  Rusertype: Return usertype name
 */
 function Rusertype($utid,$con){
  $sqlutype="SELECT usertype FROM tbusertype WHERE id='".$utid."'";
  $rutype=mysqli_query($con,$sqlutype) or die(mysqli_connect_error());
  list($utname)=mysqli_fetch_array($rutype);
  return $utname;
 }
/*
  Guser: Return user's data points
*/
 function Guser($userid,$con){
   $sqlup="SELECT username, passw, namelao, nameeng, snamelao, snameeng, dbirth, gender, mphone, email, address, usertype FROM tbusers WHERE id='$userid'"; 
   $ruserup=mysqli_query($con,$sqlup) or die(mysqli_connect_error());
   list($username, $passw, $namelao, $nameeng, $snamelao, $snameeng, $dbirth, $gender, $mphone, $email, $address, $usertype)=mysqli_fetch_array($ruserup);
   return array($username, $passw, $namelao, $nameeng, $snamelao, $snameeng, $dbirth, $gender, $mphone, $email, $address, $usertype);
 }
/*
  Userupdate: Update the user's data
*/
function Userupdate($uid,$nlao,$neng,$snlao,$sneng,$dbirth,$sex,$phone,$email,$cadd,$utype,$con){
  $sqlupuser="UPDATE tbusers 
              SET namelao='".$nlao."', 
			      nameeng='".$neng."',
				  snamelao='".$snlao."', 
				  snameeng='".$sneng."', 
				  dbirth='$dbirth',
				  gender='".$sex."', 
				  mphone='".$phone."',
				  email='".$email."',
				  address='".$cadd."',
				  usertype='".$utype."'
			  WHERE id='$uid'";
  mysqli_query($con,$sqlupuser) or die(mysqli_connect_error());
}
/*
  Currentstudent: Return data on current students based 
*/
function Currentstudent($userid,$con){
  $sqlcst = "SELECT * FROM tbcstudents WHERE userid='$userid'";
  $rcst = mysqli_query($con,$sqlcst) or die(mysqli_connect_error());
  if(mysqli_num_rows($rcst)>0){ // Current students
	list($sid,$slevel,$starea,$sclass,$sayear) = mysqli_fetch_array($rcst); 
	return array($sid,$slevel,$starea,$sclass,$sayear); 
  } else { // New students
	return "stnew";
  }
}
/*
 Laotitle: Return user's title in lao
*/
function Laotitle($gender,$con){
  if($gender=="m"){
	return "ທ. "; 
  } else {
	return "ນ. ";   
  }
}

/*
 Rdegreename: Return name of degree
*/
function Rdegreename($dgid, $con){
  $sqldg = "SELECT degreename FROM tbdegree WHERE id='$dgid'";
  $rdg = mysqli_query($con,$sqldg) or die(mysqli_connect_error());
  list($dgname) = mysqli_fetch_array($rdg);
  return $dgname;
}

/*
 Subj: Insert data on subjects into tbsubjects
*/
function Subj($sublao,$subeng,$ncredit, $sdgid, $sareaid,$con){
  $sqlinsub = "SELECT * FROM tbsubjects WHERE sublao='".$sublao."' AND dgree='$sdgid' AND sarea='$sareaid'";
  $rsub = mysqli_query($con, $sqlinsub) or die(mysqli_connect_error());
  if(mysqli_num_rows($rsub)>0){
	return "exists";  
  } else {
	$sqlin = "INSERT INTO tbsubjects(sublao, subeng, credit, dgree, sarea) VALUES('".$sublao."', '".$subeng."', '$ncredit', '$sdgid', '$sareaid')";
	mysqli_query($con, $sqlin) or die(mysqli_connect_error());
	//return "added";
	  
	// Refresh page
     
	 echo "<script type='text/javascript'>window.location.href = 'content.php?sad=subject';</script>";
	 exit();
  }
}
/*
  Showsublist: Show list of subjects. With the list, subject can be edited and removed
*/
 function Showsublist($con){
	

  $sqlslist = "SELECT * FROM tbsubjects ORDER BY dgree, sarea ASC";
  $rs = mysqli_query($con, $sqlslist) or die(mysqli_connect_error());
  if(mysqli_num_rows($rs)>0){
	print "<table class='tbus' style='width: 100%; margin-top: 20px; border-bottom: 1px solid black; font-size: 11pt'>"; 
	print "<tr><th width='5%'>ລ/ດ</th><th width='20%'>ຊື່ວິຊາ (ລາວ)</th><th width='15%'>ຊື່ວິຊາ (ອັງກິດ)</th><th width='15%'>ຂັ້ນ</th><th width='15%'>ຂະແໜງ</th><th width='15%'>ຈນ ໜ່ວຍກິດ</th><th width='10%'>ລຶບອອກ</th><th align='center' width='10%'>ປັບປຸງ</th></tr>";
	$i = 0;
	while($r=mysqli_fetch_array($rs)){
	 $i = $i + 1;
		$sn = $r["id"];
		$slao = $r["sublao"];
		$seng = $r["subeng"];
		$nc = $r["credit"];
		$degree = $r["dgree"];
		$ndgree = Rdgree($degree, $con);
		$sareaid = $r["sarea"];
		$sarea = Rsarea($sareaid, $con);
		// Need to set style='color: black' for <a> tag, otherwise, it is not visible
		print "<tr><td align='center'>$i</td><td>$slao</td><td>$seng</td><td>$ndgree</td><td>$sarea</td><td align='center'>$nc</td><td align='center'><a href='content.php?sad=subject&subsadup=subjdelete&subid=$sn' style='color: black'><i class='fa fa-trash-o'></i></a></td><td align='center'><a href='' style='color: black'><i class='fa fa-fw fa-refresh'></i></a></td></tr>";
	}
	print "</table>";
  }
 }
/*
 Delsubj: Delete subject from tbsubjects in case the subject is not in use or connected to other tables
*/
function Delsubj($subjid, $con){
  $sqldels = "DELETE FROM tbsubjects WHERE id='$subjid'";
  mysqli_query($con,$sqldels) or die(mysqli_connect_error());
}
/*
 Savetchinfo: Save teacher's information in every September
*/
function Savetchinfo($tid, $tdgree, $tstarea, $tclass, $tlevel, $tknow, $tposition, $timfname, $timfpath, $con){
 $sqlintch = "INSERT INTO tbcteachers (userid, tdgree, tarea, tclbase, tdgreekn, tgraduate, tposition, imfilename, imfilepath) 
              VALUES ('$tid', '$tdgree', '$tstarea', '$tclass', '$tlevel', '$tknow', '$tposition', '".$timfname."', '".$timfpath."')";
 mysqli_query($con, $sqlintch) or die(mysqli_connect_error());
 // Refresh page
     echo "<script type='text/javascript'>window.location.href = 'content.php?sad=teacher';</script>";
	 exit();
}
/*
 Updatetchinfo: Update teacher's information
*/
function Updatetchinfo($tid, $dgree, $sarea, $sclass, $slevel, $sknow, $spotion, $extimg, $upimg, $upimgtmp, $con){
 if($extimg != $upimg){
	$upimgpath = "../images/users_images/".$upimg;
	move_uploaded_file($upimgtmp, $upimgpath); 
	
    $sqluptinfo = "UPDATE tbcteachers SET tdgree='$dgree', tarea='$sarea', tclbase='$sclass', tdgreekn='$slevel', tgraduate='$sknow', tposition='$spotion', imfilename='".$upimg."', imfilepath='".$upimgpath."' WHERE userid='$tid'"; 
 } else {
	 $sqluptinfo = "UPDATE tbcteachers SET tdgree='$dgree', tarea='$sarea', tclbase='$sclass', tdgreekn='$slevel', tgraduate='$sknow', tposition='$spotion' WHERE userid='$tid'"; 
 }
 
  mysqli_query($con, $sqluptinfo) or die(mysqli_connect_error());
  // Refresh page
     echo "<script type='text/javascript'>window.location.href = 'content.php?sad=teacher';</script>";
	 exit();
}

/*
 Rteachinfo: Return data on teacher
*/
function Rteachinfo($uid, $con){
  $sqlteach = "SELECT * FROM tbcteachers WHERE userid='$uid'";
  $rteach = mysqli_query($con,$sqlteach) or die(mysqli_connect_error());
  list($user,$tdegree, $tarea, $tclassbase, $tknow, $tgraduate, $tpost, $timg) = mysqli_fetch_array($rteach);
  return array($user, $tdegree, $tarea, $tclassbase, $tknow, $tgraduate, $tpost, $timg);
}

/*
 Rclassname: Return name of class
*/
 function Rclassname($clid, $con){
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlcl = "SELECT classname FROM tbclass WHERE id='$clid'"; 
  $rcl = mysqli_query($con,$sqlcl) or die(mysqli_connect_error());
  list($clname) = mysqli_fetch_array($rcl);
  return $clname;
 }
/*
 Rsubjectname: Return subject's name in Lao and English from tbsubject
*/
 function Rsubjectname($subid, $con){
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsub = "SELECT sublao, subeng FROM tbsubjects WHERE id='$subid'"; 
  $rsub = mysqli_query($con, $sqlsub) or die(mysqli_connect_error());
  list($slao, $seng) = mysqli_fetch_array($rsub);
  return array($slao, $seng);
 }
/*
  Rsubjectall: Return the details of subject
*/
 function Rsubjectall($subid, $con){
  $sqlsubd = "SELECT * FROM tbsubjects WHERE id='$subid'";
  $rsubd = mysqli_query($con, $sqlsubd) or die(mysqli_connect_error());
  list($sid, $slao, $seng, $credit, $dgree, $sarea) = mysqli_fetch_array($rsubd);
  return array($sid, $slao, $seng, $credit, $dgree, $sarea);
 }
/*
 Rdgree: Return degree name
*/
function Rdgree($dgrid, $con){
 $con->set_charset("utf8");
 $sqldgree = "SELECT degreename FROM tbdegree WHERE id='$dgrid'";
 $rdgree = mysqli_query($con,$sqldgree) or die(mysqli_connect_error());
 list($dgname) = mysqli_fetch_array($rdgree);
 return $dgname;
}
/*
 Rsarea: Return study area name
*/
function Rsarea($sareaid, $con){
 $con->set_charset("utf8");
 $sqlsarea = "SELECT sareaname FROM tbstudyarea WHERE id='$sareaid'";
 $rsarea = mysqli_query($con,$sqlsarea) or die(mysqli_connect_error());
 list($dgname) = mysqli_fetch_array($rsarea);
 return $dgname;
}
/*
  Rtday: Return day of teaching from tbtchday
*/
function Rtday($dayid, $con){
  $sqltday = "SELECT dayname FROM tbtchday WHERE id='$dayid'";
  $rtd = mysqli_query($con,$sqltday) or die(mysqli_connect_error());
  list($tday) = mysqli_fetch_array($rtd);
  return $tday;
}
/*
 Rttime: Return teaching time from tbtchtime
*/
function Rttime($tid, $con){
  $sqltime = "SELECT tchtime FROM tbtchtime WHERE id='$tid'";
  $rtime = mysqli_query($con, $sqltime) or die(mysqli_connect_error());
  list($ttime) = mysqli_fetch_array($rtime);
  return $ttime; 
}
/*
 Rgraduate: Return teacher's graduate name
*/
function Rgraduate($gradid, $con){
 $sqlgrad = "SELECT gname FROM tbgraduate WHERE id='$gradid'";	
 $rgrad = mysqli_query($con,$sqlgrad) or die(mysqli_connect_error());
 list($gdname) = mysqli_fetch_array($rgrad);
 return $gdname;
}
/*
 Rposition: Return teacher and admin staff's position name
*/
function Rposition($psid, $con){
 $sqlps = "SELECT psname FROM tbposition WHERE id='$psid'";
 $rps = mysqli_query($con,$sqlps) or die(mysqli_connect_error());
 list($psname) = mysqli_fetch_array($rps);
 return $psname;
}
/*
 Rusername: Return username from tbusers
*/
function Rusername($uid,$con){
 $con->set_charset("utf8");
 $sqluname = "SELECT namelao, nameeng FROM tbusers WHERE id='$uid'";
 $runame = mysqli_query($con,$sqluname) or die(mysqli_connect_error());
 list($ulao,$ueng) = mysqli_fetch_array($runame);
 return array($ulao,$ueng);	
}
/*
 Rlocation: Return location of class
*/
function Rlocation($lotid, $con){
 $con->set_charset("utf8");
 $sqllocat = "SELECT locationname FROM tblocation WHERE id='$lotid'";
 $rcllot = mysqli_query($con,$sqllocat) or die(mysqli_connect_error());
 list($lotname) = mysqli_fetch_array($rcllot);
 return $lotname;
}
/*
 Classin: Insert data into tbclass
*/
function Classin($clname,$degreeid, $areaid, $locateid,$con){
  $sqlincl = "SELECT * FROM tbclass WHERE classname='".$clname."' AND degree='$degreeid' AND studyarea='$areaid'";
  $rcl = mysqli_query($con, $sqlincl) or die(mysqli_connect_error());
  if(mysqli_num_rows($rcl)>0){
	return "exists";  
  } else {
	$sqlin = "INSERT INTO tbclass(classname, degree, studyarea, location) VALUES('".$clname."', '$degreeid', '$areaid', '$locateid')";
	mysqli_query($con, $sqlin) or die(mysqli_connect_error());
	//return "added";
	// Refresh page
     echo "<script type='text/javascript'>window.location.href = 'content.php?sad=class';</script>";
	 exit();
  }
}
/*
  Showclasslist: Show list of class. With the list
*/
 function Showclasslist($con){
  $sqlclass = "SELECT * FROM tbclass ORDER BY degree, studyarea DESC";
  $rcl = mysqli_query($con, $sqlclass) or die(mysqli_connect_error());
  if(mysqli_num_rows($rcl)>0){
	print "<table class='tbus' style='width: 100%; margin-top: 20px; border-bottom: 1px solid black; font-size: 11pt'>"; 
	print "<tr><th width='5%'>ລ/ດ</th><th width='20%'>ຊື່ຫ້ອງຮຽນ</th><th width='15%'>ຂັ້ນ</th><th width='15%'>ຂະແໜງ</th><th width='15%'>ທີ່ຕັ້ງ</th><th width='10%'>ລຶບອອກ</th><th align='center' width='10%'>ປັບປຸງ</th></tr>";
	  $i = 0;
	while($r=mysqli_fetch_array($rcl)){
		$i = $i + 1;
		$cln = $r["id"];
		$clname = $r["classname"];
		$degree = $r["degree"];
		$ndgree = Rdgree($degree, $con);
		$sareaid = $r["studyarea"];
		$sarea = Rsarea($sareaid, $con);
		$lot = $r["location"];
		$cllot = Rlocation($lot, $con);
		// Need to set style='color: black' for <a> tag, otherwise, it is not visible
		print "<tr><td align='center'>$i</td><td>$clname</td><td>$ndgree</td><td>$sarea</td><td>$cllot</td><td align='center'><a href='content.php?sad=class&sadcl=cldel&clid=$cln' style='color: black'><i class='fa fa-trash-o'></i></a></td><td align='center'><a href='' style='color: black'><i class='fa fa-fw fa-refresh'></i></a></td></tr>";
	}
	print "</table>";
  }
 }

/*
 Delclass: Delete class from tbclass
*/
 function Delclass($clid, $con){
	$sqldcl = "DELETE FROM tbclass WHERE id='$clid'";
	mysqli_query($con, $sqldcl) or die(mysqli_connect_error());
	
	// Refresh page
     echo "<script type='text/javascript'>window.location.href = 'content.php?sad=class';</script>";
	 exit();
 } 

/*
  Showlistsarea: Present list of study area
*/
  function Showlistsarea($con){
	$sqlsar = "SELECT * FROM tbstudyarea ORDER by sareaname DESC";
    $rsr = mysqli_query($con, $sqlsar) or die(mysqli_connect_error());
    if(mysqli_num_rows($rsr)>0){
	print "<table align='left' class='tbus' style='width: 70%; margin-top: 20px; border-bottom: 1px solid black; font-size: 11pt'>"; 
	print "<tr><th align='center' width='5%'>ລ/ດ</th><th align='center' width='20%'>ຊື່ຂະແໜງ/ວິຊາຮຽນ</th><th align='center' width='10%'>ລຶບອອກ</th><th align='center' width='10%'>ປັບປຸງ</th></tr>";
	$i = 0;
	while($r=mysqli_fetch_array($rsr)){
		$i = $i + 1;
		$sarid = $r["id"];
		$sarname = $r["sareaname"];
		// Need to set style='color: black' for <a> tag, otherwise, it is not visible
		print "<tr><td align='center'>$i</td><td>$sarname</td><td align='center'><a href='content.php?sad=studyarea&delsar=$sarid' style='color: black'><i class='fa fa-trash-o'></i></a></td><td align='center'><a href='content.php?sad=studyarea&updsar=$sarid' style='color: black'><i class='fa fa-fw fa-refresh'></i></a></td></tr>";
	}
	print "</table>";
  } 
  }
/*
  Academicyear: Generate academic year automaticlly base date system
*/
  function Academicyear($con){
	$cyear = date('y'); // this year in 2 digits
	$fcyear = date('Y'); // this year in 4 digits
	$lyear = $fcyear - 1;
	$nyear = $fcyear + 1; // Next year in 2 digits
	$cmonth = date('m'); // this month in 2 digits
	$acrange = array("09","10","11","12"); 
	if(is_array($cmonth)){ // Acedemic year starts in Sept
	   $ayear = $fcyear."-".$nyear; 
	 } else {
	   $ayear = $lyear."-".$cyear;
	 } 
	  return $ayear;
   }

  /*
    Saveteaching: Save teaching informaton into tbteaching
  */
   function Saveteaching($uid, $tsub, $tclass, $tday, $ttime, $tsem, $ayear, $con){
	 $sqlteaching = "INSERT INTO tbteaching(userid, subjid, classid, teachday, teachtime, semester, ayear) VALUES('$uid', '$tsub', '$tclass', '".$tday."', '".$ttime."', '".$tsem."', '".$ayear."')";
	 mysqli_query($con, $sqlteaching) or die(mysqli_connect_error());
	 // Refresh the page - GO BACK TO Teacher - teaching
	 echo "<script type='text/javascript'>window.location.href = 'content.php?sad=ttch';</script>";
	 exit();
   }
/*
 Getdg_str: Get Degree and study areas from tbsubject
*/
 function Getdg_str($subid, $con){
   $sqldgst = "SELECT credit, dgree, sarea FROM tbsubjects WHERE id='$subid'";
   $rdg = mysqli_query($con,$sqldgst) or die(mysqli_connect_error());
   if(mysqli_num_rows($rdg)>0){
	  list($cdit, $dgree, $sarea)=mysqli_fetch_array($rdg); 
	  return array($cdit, $dgree, $sarea);
   }
 }
/*
  Teachingtable: Show/Present teaching table - data from tbteaching
*/
   function Teachingtable($uid, $con){
	 $sqltch = "SELECT * FROM tbteaching WHERE userid='$uid'";
	 $rtch = mysqli_query($con,$sqltch) or die(mysqli_connect_error());
	 if(mysqli_num_rows($rtch)>0){
		print "<table class='tbus'>";
		print "<tr><th width='5%'>ລ/ດ</th><th width='12%'>ຊັ້ນ/ຂັ້ນ</th><th width='15%'>ສາຂາວິຊາ</th><th width='15%'>ວິຊາ</th><th width='10%'>ຫ້ອງຮຽນ</th><th width='8%'>ມື້ວັນ</th><th width='15%'>ເວລາ</th><th width='8%'>ລືບ</th><th width='8%'>ດັດແປງ</th></tr>";
		 $i = 0;
		 while($r=mysqli_fetch_array($rtch)){
		 $i = $i + 1;
		 $tchid = $r["id"];
		 $tchan = $r["id"];
		
		 $sj = $r["subjid"];
		 $cl = $r["classid"];
	     $tday = $r["teachday"];
		 $ttime = $r["teachtime"];
		 list($credit, $dgree, $sarea) = Getdg_str($sj, $con);
		 $ndgree = Rdgree($dgree, $con);
		 $nsarea = Rsarea($sarea, $con);
		 list($sublao, $subeng) = Rsubjectname($sj, $con);
		 $cl = Rclassname($cl, $con);
		 $tday = Rtday($tday, $con);
		 $ttime = Rttime($ttime, $con);
		 print "<tr><td align='center'>$i</td><td>$ndgree</td><td>$nsarea</td><td>$sublao</td><td align='center'>$cl</td><td align='center'>$tday</td><td align='center'>$ttime</td>
			 <td align='center'><a href='content.php?sad=teacher&deltchid=$tchid' style='color: black'><i class='fa fa-trash-o'></i></a></td><td align='center'><a href='content.php?sad=teacher&cteach=$tchid' style='color: black'><i class='fa fa-fw fa-refresh'></i></a></td</tr>";
	    } // End of while
		print "</table>";
	 } // End of if>0
   }

/*
  Studytable: Show study table by day - on ຕາຕະລາງ tab - for admin user
*/
  function Studytable($tchday, $con){
	
  $sqlstable = "SELECT * FROM tbteaching WHERE teachday='$tchday' GROUP BY classid";
  $rstable = mysqli_query($con, $sqlstable) or die(mysqli_connect_error());
  $rstable1 = mysqli_query($con, $sqlstable) or die(mysqli_connect_error());
  list($a,$b,$c,$d,$tdayid) = mysqli_fetch_array($rstable1); // Get only teaching day

  if(mysqli_num_rows($rstable)>0){
	
	switch($tdayid){
		case 1: 
	     print "<h2 style='margin-left: 150px;'>ຈັນ</h2>";
		break;
			
		case 2:
		  print "<h2 style='margin-left: 150px;'>ຄານ</h2>";
		break;
			
		case 3:
		  print "<h2 style='margin-left: 150px;'>ພຸດ</h2>";
		break;
			
		case 4:
		  print "<h2 style='margin-left: 150px;'>ພະຫັດ</h2>";
		break;
			
		case 5:
		  print "<h2 style='margin-left: 150px;'>ສຸກ</h2>";
		break;
			
		case 6:
		  print "<h2 style='margin-left: 150px;'>ເສົາ</h2>";
		break;
	} // End of switch
	  
	print "<table class='tbus' style='width: 80%; margin-left: 150px; margin-bottom:50px; border: 1px solid grey'>";
	print "<tr><th rowspan='2' style='width: 30%'>ຂະແໜງ/ວິຊາ</th><th rowspan='2' style='width: 15%'>ຊັ້ນ-ຂັ້ນ</th><th align='center' rowspan='2' style='width: 10%'>ຫ້ອງຮຽນ</th><th align='center' colspan='3'>ເວລາ/ວິຊາຮຽນ</th></tr>";
	print "<tr><th style='width: 15%'>8.20 – 9.40</th><th style='width: 15%'>10.00 – 11.30</th><th style='width: 15%'>13.20 – 15.40</th></tr>";
	  
     while($r=mysqli_fetch_array($rstable)){
	  $tsub = $r["subjid"];
	  $tcl = $r["classid"];
	  $tday = $r["teachday"];
		 
	   // Get degree from tbsubjects
		 $sqldg = "SELECT dgree, sarea FROM tbsubjects WHERE id='$tsub'";
		 $rsdg = mysqli_query($con,$sqldg) or die(mysqli_connect_error());
		 list($sdgee, $ssarea) = mysqli_fetch_array($rsdg);
		 $dgreename = Rdgree($sdgee, $con);
		 $sarea = Rsarea($ssarea, $con);
		// $dgreename = Rdegreename($sdgee, $con); - This function needs to be deleted
	   // Get class from tbteaching based on class id in tbclass
		 $tclname = Rclassname($tcl, $con);
		 
	 // Get subjects by teaching session
	 	 // 1. College- first session
		 $sqltsub1 ="SELECT subjid FROM tbteaching WHERE classid='$tcl' AND teachday='$tdayid' AND teachtime='4'"; 
		 $rts1 = mysqli_query($con,$sqltsub1) or die(mysqli_connect_error());
		 list($tsbyh1) = mysqli_fetch_array($rts1);
		 list($sublao1, $subeng1) = Rsubjectname($tsbyh1, $con); // First session
		 
		 // 2. College- second session
		 $sqltsub2 ="SELECT subjid FROM tbteaching WHERE classid='$tcl' AND teachday='$tdayid' AND teachtime='5'"; 
		 $rts2 = mysqli_query($con,$sqltsub2) or die(mysqli_connect_error());
		 list($tsbyh2) = mysqli_fetch_array($rts2);
		 list($sublao2, $subeng2) = Rsubjectname($tsbyh2, $con); 
		 
		 // 3. College- third session
		 $sqltsub3 ="SELECT subjid FROM tbteaching WHERE classid='$tcl' AND teachday='$tdayid' AND teachtime='6'"; 
		 $rts3 = mysqli_query($con,$sqltsub3) or die(mysqli_connect_error());
		 list($tsbyh3) = mysqli_fetch_array($rts3);
		 list($sublao3, $subeng3) = Rsubjectname($tsbyh3, $con); 
		
	  print "<tr><td>$sarea</td><td>$dgreename</td><td>$tclname</td><td>$sublao1</td><td>$sublao2</td><td>$sublao3</td></tr>";
		 
	  print "</tr>";
     } // End of while
    print "</table>";  
	  
  } // End of if >0
	  
  } // End of function 

 /*
  Studytablecl: Present study table by class
 */
 function Studytablecl($tday, $ttime, $con){
	$sqltsub = "SELECT subjid FROM tbteaching WHERE teachday='$tday' AND teachtime='$ttime'";
	$rtsub = mysqli_query($con, $sqltsub) or die(mysqli_connect_error());
	list($sub) = mysqli_fetch_array($rtsub);
	return $sub;
 }

 /*
  Rtchername: Return teacher's name based on subject id
 */
 function Rtchername($subid, $con){
    $sqltcher = "SELECT userid FROM tbteaching WHERE subjid='$subid'";
	$rter = mysqli_query($con,$sqltcher) or die(mysqli_connect_error());
	list($uid) = mysqli_fetch_array($rter);
	list($uname, $psw, $tnamelao) = Guser($uid,$con);
	return $tnamelao;
 }

/*
  Conttblepdf: Present contents of teaching table for a particular class for PDF printing
*/
  function Conttblepdf($clid, $con){
	$sqlttime = "SELECT teachtime FROM tbteaching WHERE classid='$class' ORDER BY teachtime ASC";
    $rttime = mysqli_query($con, $sqlttime) or die(mysqli_connect_error());
    while($r=mysqli_fetch_array($rttime)){
	  $ttime = $r["teachtime"];
	  $tchtime = Rttime($tid, $con);
	     // Subject id
		  $sub1 = Studytablecl(1, $tid, $con); // Monday
		  $sub2 = Studytablecl(2, $tid, $con); // Tuesday
		  $sub3 = Studytablecl(3, $tid, $con); // Wednsday
		  $sub4 = Studytablecl(4, $tid, $con); // Thursday
		  $sub5 = Studytablecl(5, $tid, $con); // Friday
		  $sub6 = Studytablecl(6, $tid, $con); // Satu
		  $sub7 = Studytablecl(7, $tid, $con); // Sun
		
		// Subject name
		 list($sublao1, $subeng1) = Rsubjectname($sub1, $con);
		 list($sublao2, $subeng2) = Rsubjectname($sub2, $con);
		 list($sublao3, $subeng3) = Rsubjectname($sub3, $con);
		 list($sublao4, $subeng4) = Rsubjectname($sub4, $con);
		 list($sublao5, $subeng5) = Rsubjectname($sub5, $con);
		 list($sublao6, $subeng6) = Rsubjectname($sub6, $con);
		 list($sublao7, $subeng7) = Rsubjectname($sub7, $con);
	 print "<tr><td>$tchtime</td><td>$sublao1 $tname1</td><td>$sublao2 $tname2</td><td>$sublao3 $tname3</td><td>$sublao4 $tname4</td><td>$sublao5 $tname5</td><td>$sublao6 $tname6</td><td>$sublao7 $tname7</td></tr>";	
    } // End of while
  }

 /*
  Ssubtable: Retrun teaching subjects for partcular user
 */
 function Ssubtable($uid, $tday, $ttime, $con){
	$sqltsb = "SELECT subjid FROM tbteaching WHERE userid='$uid' AND teachday='$tday' AND teachtime='$ttime'";
	$rtsb = mysqli_query($con, $sqltsb) or die(mysqli_connect_error());
	list($tsub) = mysqli_fetch_array($rtsb);
	return $tsub;
 }

/*
  Dgstable: Return degree, study area and location of class - teachers_teachtable.php
*/
 function Dgstable($uid, $subid, $ttime, $con){
	$sqlcl = "SELECT classid FROM tbteaching WHERE userid='$uid' AND subjid='$subid' AND teachtime='$ttime'";
	$rcl = mysqli_query($con, $sqlcl) or die(mysqli_connect_error());
	list($clid) = mysqli_fetch_array($rcl);
	if(!empty($clid)){		  
	// Get Degree and study area
		$sqlds = "SELECT degree, studyarea, location FROM tbclass WHERE id='$clid'";
		$rds = mysqli_query($con, $sqlds) or die(mysqli_connect_error());
		list($cldg, $sta, $lct) = mysqli_fetch_array($rds);
			  
		$clname = Rclassname($clid, $con); 
		$dgname = Rdgree($cldg, $con);
		$saname = Rsarea($sta, $con);
		
		$clname = "(".$clname.",".$dgname.",".$saname.")";
		return $clname;
	} 
 }

/*
  Rlclass: Return ID last class and number of class in the study areas and degree from tbclass
*/
 function Rlclass($dgid, $stid, $con){
	$sqllc = "SELECT id FROM tbclass WHERE degree='$dgid' AND studyarea='$stid' GROUP BY id ORDER BY id DESC";
	$rs = mysqli_query($con, $sqllc) or die(mysqli_connect_error());
	list($clid) = mysqli_fetch_array($rs);
	return $clid;
 }

/*
 Rclassmove: Return ID next class to be moved in tbclass
*/
 function Rclassmove($dgid, $stid, $sqclid, $con){
	$sqlpcl = "SELECT id FROM tbclass WHERE degree='$dgid' AND studyarea='$stid' GROUP BY id ORDER BY id ASC"; // small to big
	$rp = mysqli_query($con,$sqlpcl) or die(mysqli_connect_error()); 
	 $csq = 0;
	while($r=mysqli_fetch_array($rp)){
		$csq = $csq + 1;
		$cid = $r["id"];
		if($csq==$sqclid){
		  return $cid; 	
		}
	}
 }

/*
  Classmove: To move current class to new one
*/
function Classmove($cclid, $clmoveid, $con){
 $sqlclmove = "UPDATE tbcstudents SET class='$clmoveid' WHERE class='$cclid'";	
 mysqli_query($con, $sqlclmove) or die(mysqli_connect_error());
}

/*
 Checlmove: check if students' records exist in tbpstendents before move them to another class in new academic year
*/
 function Checlmove($classid, $con){
	// Check if the students in the class already exists in tbpstudents in the current academic year
	 $sqlchin = "SELECT a.class, a.acyear FROM tbpstudents a, tbcstudents b WHERE a.class='$classid' AND b.class='$classid' AND a.acyear=b.acyear GROUP BY a.class, b.class";
	 $rin = mysqli_query($con,$sqlchin) or die(mysqli_connect_error());
	 
	 $cfmove = ""; // Confirmation on move
	 if(mysqli_num_rows($rin)==0){
	   // Copy/move can be made
		$cfmove ="yesmove";    
	 } else {
	   $cfmove ="notmove"; // move of the class is already made
	 }
	return $cfmove;
 }

/*
  Copmvstu: Copy/move students' records from tbcstudents to tbpstudents
*/
  function Copmvstu($clid, $con){
	 $sqlinp = "INSERT INTO tbpstudents(userid, degree, stuarea, class, acyear) SELECT * FROM tbcstudents WHERE class='$clid'"; 
	  mysqli_query($con, $sqlinp) or die(mysqli_connect_error());
  }

/*
  Graduatestu: Set user status to "disable" in tbusers to disable user account and Delete students from tbcstudents
*/
  function Graduatestu($clid, $con){
	// Disable user account
	$sqlduse = "SELECT userid FROM tbcstudents WHERE class='$clid'";
	$ruse = mysqli_query($con, $sqlduse) or die(mysqli_connect_error());
	 while($r=mysqli_fetch_array($ruse)){
		$uid = $r["userid"];
		$sqldsb = "UPDATE tbusers SET status='disable' WHERE id='$uid'";
		mysqli_query($con,$sqldsb) or die(mysqli_connect_error());
	 }
	// Delete
	$sqldel = "DELETE FROM tbcstudents WHERE class='$clid'";
	mysqli_query($con, $sqldel) or die(mysqli_connect_error());
	
  }
 
/*
  Refreshstupage: Refresh the page and go to sad_students.php
*/
 function Refreshstupage($con){
	echo "<script type='text/javascript'>window.location.href = 'content.php?sad=stud';</script>";
	exit(); 
 }

/*
  // Teachers - Student attendance
  Rdgsarea: Return degree and study areas based on class id
*/
 function Rdgsarea($clid, $con){
	$sqlds = "SELECT degree, studyarea FROM tbclass WHERE id='$clid'"; 
	$rs = mysqli_query($con,$sqlds) or die(mysqli_connect_error());
	list($dgid, $staid) = mysqli_fetch_array($rs);
	return array($dgid, $staid);
 }

/*
  // Teachers - Student attendance
  AttendList: List of students from tbcstudents for attendance and tbattendance for attendance records
*/
 
 function AttendList($tcher, $dgname, $staname, $classid, $subjid, $ayear, $dateatt, $timeatt, $con){
		
	 print "<div id='atdiv'>";
	 $classname = Rclassname($classid, $con);
	 $timeattendance = Rttime($timeatt, $con);
	 // $con->set_charset("utf8");
 
	 $sqllst = "SELECT * FROM tbcstudents WHERE class='$classid'";     	 
	 $rst = mysqli_query($con, $sqllst) or die(mysqli_connect_error());
 
	 if(mysqli_num_rows($rst)>0){
  		$nst = mysqli_num_rows($rst); // Number of students
  		$i = 0;
  		print "<form class='stforme' action='content.php?tch=sattend' method='post'>";
  		print "<p align='center'>ລາຍຊື່ ນັກຮຽນ/ນັກສືກສາ ຫ້ອງ <b>$classname</b><br>ຂະແໜງ/ວິຊາ &nbsp;$staname, &nbsp;$dgname</p>";
		print "<p align='center'>ການໝາຍຂາດຮຽນ, ວັນທີ: <b>$dateatt</b> &nbsp; ເວລາ: <b> $timeattendance</b></p>";
   
  		print "<table class='tbus' style='font-size: 12pt; margin-buttom: 0px'>";
  		print "<tr><th style='width: 5%' >ລດ</th><th style='width: 45%'>ຊື່ ແລະ ນາມສະກຸນ</th><th style='width: 15%'>ໝາຍຂາດ(Tick)</th><th style='width: 15%'>ຂາດບໍ່ມີເຫດຜົນ (Tick)</th></tr>";
  			while($r=mysqli_fetch_array($rst)){
				$i = $i + 1;
				$uid = $r["userid"]; // Student id
				list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status, $rgdate, $lupdate) = Userbyid($uid, $con);
				if($gender=="m"){
	  				$sex = "ທ. ";	
				} else {
	  				$sex = "ນ. ";		
				}
				$stname = $sex." ".$namel." ".$snamel;
				// Use $tcher,$dateatt,$timeatt and $ayear for tracking student attendance- ENOUGH
				$sqlatt = "SELECT id, absence, absencenor FROM tbattendance WHERE uid='$uid' AND teacherid='$tcher' AND adate='$dateatt' AND atime='$timeatt' AND acyear='$ayear'";
				$ratt = mysqli_query($con, $sqlatt) or die(mysqli_connect_error());
				list($aid,$abs, $absno) = mysqli_fetch_array($ratt);

				print "<tr><td align='center'>$i</td><td>&nbsp;$stname<input type='hidden' name='$uid' value='$uid' ></td><td align='center'><input type='checkbox' ".($abs ? "checked" : "")." name='$uid' id='$uid' onchange='handleCheckboxAttendance(this, \"$aid\", \"$tcher\", \"$dateatt\", \"$timeatt\", \"$classid\", \"$subjid\", \"$ayear\");'></td><td align='center'><input type='checkbox' ".($absno ? "checked" : "")." name='$aid' id='$aid' onchange='handleCheckboxAbsenceReason(this, \"$aid\");'></td></tr>";


  			} // End of while
		   
 		print "</table>";
  		// Table - Submit button
 		/*
		print "<table align='right' style='width: 100%; margin: 0px 0px 50px 0px;'>";
 		print "<tr><td align='right'><input type='submit' name='btncancel' id='btncancelid' value='ຍົກເລີກ' style='width:100px;'><input type='submit' name='btnatt' id='btnattid' value='ບັນທຶກ' style='width:100px;'></td></tr>";
 		print "</table>";
		*/
 		print "</form>";
		
	 } else {
		echo "<p align='center'><span style='font-size: 30pt'<i class='fa fa-calendar-check-o'></i></span><br>ບັນທືກການຂາດຮຽນ</p>";
	 } // End of if
   print "</div>";
 }

 /*
   AttendanceVariables: Return list of essential variables
 */
 function AttendanceVariables($aid, $con){
	$sql = "SELECT * FROM tbattendance WHERE id='$aid'";
	$result = mysqli_query($con, $sql) or die(mysqli_error());
	list($aid, $sid, $tid, $subj, $cls, $adate, $atime, $ayear) = mysqli_fetch_array($result);
	return array($aid, $sid, $tid, $subj, $cls, $adate, $atime, $ayear);
 }

/*
  ChAttd: Check if attendance exists for UPDATE  AND NEW DATA ENTRY
*/

function ChAttd($stid, $subid, $clid, $datea, $timea, $ayear, $con){
	$chatt = "";
	$sqlatt = "SELECT absence FROM tbattendance WHERE uid='$stid' AND subj='$subid' AND cls='$clid' AND adate='$datea' AND atime='$timea' AND acyear='".$ayear."'";
	$rs = mysqli_query($con, $sqlatt) or die(mysqli_connect_error());
	list($abs) = mysqli_fetch_array($rs);
	if(!empty($abs)){
	  $chatt = "exist"; 
	}
	return $chatt;
}
/*
  Rabsence: Return numbr of absence by month
*/
 function Rabsence($sid, $mnth, $con){
	$sqltab = "SELECT SUM(absence) AS tabs, SUM(absencenor) AS tabsno FROM tbattendance WHERE uid='$sid' AND MONTH(adate)='$mnth' GROUP BY MONTH(adate)";
	$rab = mysqli_query($con,$sqltab) or die(mysqli_error());
	list($sabs, $sabsno) = mysqli_fetch_array($rab);
	return array($sabs, $sabsno);
 }
 /*
    AttendanceRecords: Show records of attendance for a teacher by date
 */
 function AttendanceRecords($tid, $con){
	$sqlatt = "SELECT * FROM tbattendance WHERE teacherid='$tid' GROUP BY adate,atime,subj,cls ORDER BY adate DESC";	
	$ratt = mysqli_query($con, $sqlatt) or die(mysqli_connect_error());
	if(mysqli_num_rows($ratt)>0){
		print "<table class='tbus' style='width: 100%; margin-top: 15px; border-bottom: 1px solid black; font-size: 10pt'>"; 
		print "<tr><th width='8%' align='center'>ລ/ດ</th><th width='20%' align='center'>ວັນທີ</th><th width='10%' align='center'>ເວລາ</th><th width='10%' align='center'>ວີຊາ</th><th width='10%' align='center'>ຫ້ອງຮຽນ</th></tr>";
		
		$i = 0;
		while($r=mysqli_fetch_array($ratt)){
		 $i = $i + 1;
		 $aid = $r['id'];
		 $adate = $r["adate"];
		 $atimeid = $r["atime"];
		 $atime = Rttime($atimeid, $con);
		 $subj = $r["subj"];
		 list($subjlao, $subjeng) = Rsubjectname($subj, $con);
		 $clsid = $r["cls"];
		  
		 list($dgid, $stid) = Rdgsarea($clsid, $con);
			$dgname = Rdgree($dgid, $con);
			$staname = Rsarea($stid, $con);
		$cls = Rclassname($clsid, $con);
		$fullcls = $cls." (".$dgname.", ".$staname.")";
		 $ayear = $r['acyear'];

		 print "<tr><td align='center'>$i</td><td><a href='content.php?tch=sattend&aid=$aid&adaterecord=$adate&atimerecord=$atime' class='alink'>$adate</a></td><td>$atime</td><td align='center'>$subjlao</td><td align='center'>$fullcls</td></tr>";
		}
		print "</table>";
	} else {
	  echo "<p align='center'><span style='font-size: 30pt'><i class='fa fa-calendar-check-o'></i></span><br>ຍັງບໍ່ມີການໝາຍຂາດ</p>";
	}
 }

/*
  Showstudfees: Show list of students for tuition fee payment
*/
 function Showstudfees($txtsearch, $cls, $usertype, $con){
	 // At beggining
	 $sqlstfee = "";
	 if(empty($txtsearch) && empty($cls)){
		if($usertype==6){
			$sqlstfee = "SELECT * FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE usertype=4) ORDER BY userid DESC LIMIT 50";
		} else if($usertype==7) {
			$sqlstfee = "SELECT * FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE usertype=8) ORDER BY userid DESC LIMIT 50";
		} else {
			 $sqlstfee = "SELECT * FROM tbcstudents ORDER BY userid DESC LIMIT 50";
		}
	   
	 } else {
		 // By text of name
		if(!empty($txtsearch)){
			// echo "<script>
			      //   alert('ຜົນການຄົ້ນຫາ_Function: ".$txtsearch.". ຜູ້ໃຊ້: ".$usertype."');
			 //      </script>";
			if($usertype==6){
				$sqlstfee = "SELECT * FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE namelao LIKE '$txtsearch%') AND userid IN (SELECT id FROM tbusers WHERE usertype=4) ORDER BY userid DESC LIMIT 50";
			} else if($usertype==7) {
				$sqlstfee = "SELECT * FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE namelao LIKE '$txtsearch%') AND userid IN (SELECT id FROM tbusers WHERE usertype=8) ORDER BY userid DESC LIMIT 50";
			} else {
				$sqlstfee = "SELECT * FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE namelao LIKE '$txtsearch%') ORDER BY userid DESC LIMIT 50";
			}
		  //$sqlstfee = "SELECT * FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE namelao LIKE '$txtsearch%') ORDER BY userid DESC LIMIT 50";
		}
		// By class
		if(!empty($cls)){
			$sqlstfee = "SELECT * FROM tbcstudents WHERE class='$cls' ORDER BY userid DESC LIMIT 60";
		}
	 }
	 
	$rsf = mysqli_query($con, $sqlstfee) or die(mysqli_connect_error());
	 
	if(mysqli_num_rows($rsf)>0){
	print "<table class='tbus' style='width: 100%; margin-top: 15px; border-bottom: 1px solid black; font-size: 10pt'>"; 
	print "<tr><th width='8%' align='center'>ລ/ດ</th><th width='25%' align='center'>ຊື່ ແລະ ນາມສະກຸນ</th><th width='10%' align='center'>ຫ້ອງຮຽນ</th><th width='15%' align='center'>ຂະແໜງ/ວິຊາ</th><th width='10%' align='center'>ຂັ້ນ</th><th width='10%' align='center'>ຈ່າຍ</th><th width='25%' align='center'>ປະຫັວດການຈ່າຍ</th></tr>";
	
	$i = 0;
	while($r=mysqli_fetch_array($rsf)){
	 $i = $i + 1;
		$stuid = $r["userid"];
		list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone) = Userbyid($stuid, $con);
		$fsname = "";
		if($gender=="m"){
		  $fsname = "ທ. ".$namel."  ".$snamel;
		} else {
		  $fsname = "ນ. ".$namel."  ".$snamel;
		}
			 
		$degreeid = $r["degree"];
		$ndgree = Rdgree($degreeid, $con);
		$sareaid = $r["stuarea"];
		$nsarea = Rsarea($sareaid, $con);
		$clid = $r["class"];
		$ncl = Rclassname($clid, $con);
		// Need to set style='color: black' for <a> tag, otherwise, it is not visible
		print "<tr><td align='center'>$i</td><td>$fsname</td><td align='center'>$ncl</td><td>$nsarea</td><td align='center'>$ndgree</td><td align='center'><a href='content.php?adstaff=ptfees&sad=schfees&sadkid=kidsfees&sidpay=$stuid' style='color: black'><i class='fa fa-cc-visa'></i></a></td><td align='center'><a href='content.php?adstaff=ptfees&sad=schfees&sadkid=kidsfees&sidhispay=$stuid' style='color: black'><i class='fa fa-credit-card'></i></a></td></tr>";
	}
	print "</table>";
  } 
 }
/*
  Fullname: Return full name 
*/
 function Fullname($uid, $con){
   list($sid,$suname,$spsw,$snamel, $snamee,$ssnamel, $ssnammee,$sbdate, $sgender) = Userbyid($uid, $con);
	 $fsname = "";
	if($sgender=="m"){
	   $fsname = "ທ. ".$snamel." ".$ssnamel;
	} else {
	   $fsname = "ນ. ".$snamel." ".$ssnamel;
	} 
	return $fsname;
 }
/*
  Savepayment: Save record of payment before issuing bill number
*/
function Savepayment($bno, $hstuid,$paydate, $modpay, $bankid, $dpay,$con){
	$wmess = "";
	$bnum = "";
	
	if(empty($bno)){
		$bno = "na";
	}
	if(!empty($hstuid) && !empty($paydate) && !empty($modpay)){
	   if($modpay=="cash"){
		 $sqlpay = "INSERT INTO `tbpayments`(`billno`, `uid`, `paydate`, `methodpay`, `bankid`, `despay`) VALUES('".$bno."', '$hstuid', '$paydate', '".$modpay."', '', '".$dpay."')";  
	   } else {
		 $sqlpay = "INSERT INTO `tbpayments`(`billno`, `uid`, `paydate`, `methodpay`, `bankid`, `despay`) VALUES('".$bno."', '$hstuid', '$paydate', '".$modpay."', '".$bankid."', '".$dpay."')";
	   }
	 
	  mysqli_query($con,$sqlpay) or die(mysqli_connect_error());
	 
	  $wmess = "saved";
	} else {
	  $wmess = "empty";
	}  
	// Create bill number and update tbpayments with bill number
	if($wmess=="saved"){
	  
	  $sqlgetid = "SELECT id, paydate FROM tbpayments WHERE uid='$hstuid' ORDER BY id DESC";
	  $rgid = mysqli_query($con,$sqlgetid) or die(mysqli_connect_error());
	  list($bid, $bdate) = mysqli_fetch_array($rgid);
	  $bnum = "bs".$bid.$bdate;

	  $sqluppay = "UPDATE tbpayments SET billno='".$bnum."' WHERE id='$bid'";
	  mysqli_query($con,$sqluppay) or die(mysqli_connect_error());
	  
	}
	return array($wmess,$bid,$bnum);
}
/*
 Billcontent: Prininting content of bill
*/
function Billcontent($pid, $con){
  $sqlprint = "SELECT * FROM tbpaydetails WHERE payid='$pid'";
  $rpay = mysqli_query($con,$sqlprint) or die(mysqli_connect_error());
	if(mysqli_num_rows($rpay)>0){
		echo "<table class='tbh'>";
		echo "<tr><td rowspan='3' class='frw' align='right'><img src='../images/logo.jpg'></td><td align='left'>ກະຊວງ ສຶກສາທິການ ແລະ ກິລາ</td></tr>";
        echo "<tr><td align='left'>ກົມ ອະຊີວະສຶກສາ</td></tr>";
		echo "<tr><td align='left'>ວິທະຍາໄລ ເຕັກນິກ ບີໄອເອສ ບຸນເກີດ</td></tr>";
        echo "</table>";
        echo "p align='center' class='phead'>ຕາຕະລາງຮຽນ ປະຈໍາພາກ</p>";
 
		echo "<table>";
		
		while($r=mysqli_fetch_array($rpay)){
			 $bill =$r["billno"];
			 echo "<tr><td>$bill</td></tr>";
		} // End of while
		echo "</table>";
	} // End of if
} 
/*
 Rpaylistname: Return name of payment item's name from tbpaylist
*/
 function Rpaylistname($item, $con){
	$sqlit = "SELECT payitem FROM tbpaylist WHERE id='$item'"; 
	$rit = mysqli_query($con,$sqlit) or die(mysqli_connect_error());
	list($pitem) = mysqli_fetch_array($rit);
	return $pitem;
 }
/*
 Mpayment: Return method of payment
*/
function Mpayment($pid, $con){
  $rpay = "";
  $sqlpmod = "SELECT methodpay, bankid FROM tbpayments WHERE id='$pid'";
  $rpm = mysqli_query($con,$sqlpmod) or die(mysqli_connect_error());
  list($mpay, $bid) = mysqli_fetch_array($rpm);
  if($mpay=="bank"){
	$sqlbk = "SELECT bankname FROM tbbanks WHERE id='$bid'";
	$rbk = mysqli_query($con, $sqlbk) or die(mysqli_connect_error());
	list($bname) = mysqli_fetch_array($rbk);
	$rpay = "ຜ່ານທະນາຄານ, ຊື່: ".$bname;
  } else {
	$rpay ="ເງີນສົດ";  
  }
  return $rpay;
}
/*
 Historypay: Show student's 5 top recent payment
*/
 function Historypay($stuid, $con){
	$sqlhisp = "SELECT * FROM tbpayments WHERE uid='$stuid' ORDER BY id DESC LIMIT 5"; 
	$rhisp = mysqli_query($con,$sqlhisp) or die(mysqli_connect_error());
	if(mysqli_num_rows($rhisp)>0){
	 $i = 0;
	  echo "<table class='tbusub' style='width: 95%; border-buttom: grey'>";
	  echo "<tr><th>ລ/ດ</th><th>ໃບຮັບເງີນ</th><th>ວດປ ຈ່າຍ</th><th>ຈໍານວນເງີນ(ກີບ)</th><th>ດັດແປງ</th><th>ລືບອອກ</th></tr>";
	  while($r=mysqli_fetch_array($rhisp)){
		 $i = $i + 1;
		 // Sum of payment 
		  
		  $ptid = $r["id"]; // Pay id
		  $stid = $r["uid"]; // Student id
		  $billn = $r["billno"];
		  $sqlsmp = "SELECT SUM(amount) AS tamount FROM tbpaydetails WHERE payid='$ptid' GROUP BY payid";
		  $rsmp = mysqli_query($con,$sqlsmp) or die(mysqli_connect_error());
		  list($tpam) = mysqli_fetch_array($rsmp);
		  $tpam = number_format($tpam,0);
		  
		 $bno = $r["billno"];
		 $pdate = $r["paydate"];
		 $pdate = date("d-m-Y", strtotime($pdate)); //Change to dd/mm/yyyy
		 echo "<tr><td align='center'>$i</td><td>$bno</td><td>$pdate</td><td align='center'>$tpam</td><td align='center' style='color: black'><a href='content.php?adstaff=ptfees&sad=schfees&sadkid=kidsfees&pchange=payedit&pedit=$ptid&billno=$billn' style='color: black; font-size: 10pt'><i class='fa fa-fw fa-refresh'></i></a></td><td align='center'><a href='content.php?adstaff=ptfees&pchange=paydel&pdel=$ptid&stidch=$stid' style='color: black; font-size: 10pt'><i class='fa fa-trash-o'></i></a></td></tr>";
	  }
	  echo "</table>";
	}
 }

/*
Rpayment : Return student's data from tbpayments for edition/delete
*/
function Rpayment($pid, $con){
  $sqlped = "SELECT * FROM tbpayments WHERE id='$pid'";
  $rped = mysqli_query($con,$sqlped) or die(mysqli_connect_error());
  list($pided, $bnoed, $stidped,$pdated, $mdpayed, $bkned,$despay) = mysqli_fetch_array($rped);
  return array($pided, $bnoed, $stidped,$pdated, $mdpayed, $bkned, $despay);
}

/*
 Rbankname: Return bank name
*/
function Rbankname($bkid, $con){
 $sqlbank = "SELECT bankname FROM tbbanks WHERE id='$bid'";
 $rb = mysqli_query($con, $sqlbank) or die(mysqli_connect_error());
 list($bkname) = mysqli_fetch_array($rb);
 }	

/*
 Uppayment: Update tbpayments with submission
*/
function Uppayment($payid, $stid, $pdate, $modpay, $bkid, $desc, $con){
	$sqluppay = "UPDATE tbpayments SET paydate='$pdate', methodpay='".$modpay."', bankid='".$bkid."', despay='".$desc."' WHERE id='$payid'";
	mysqli_query($con,$sqluppay) or die(mysqli_connect_errno());
	if(mysqli_affected_rows($con)){
	  return true;
	} else {
	  return false;
	}
}
/*
  Update_paydetails_tmp: Update itemid or amount on tbpaydetails_tmp
*/
function Update_paydetails_tmp($pid, $itemp, $amountp){
	
}

/*
 Update_paydetails: Update tbpaydetails by delete existing data and add the updated one into tbpaydetails
*/
function Update_paydetails($ntbrow,$pid, $billno, $con){
	// Empty tbpaydetails_tmp 
	$sqlempty = "DELETE FROM tbpaydetails_tmp";
	mysqli_query($con,$sqlempty) or die(mysqli_connect_error());
	// Copy data to tbpaydetails_tmp 
	$sqlcopy = "INSERT INTO tbpaydetails_tmp(payid, billno, itemid, amount) SELECT payid, billno, itemid, amount FROM tbpaydetails WHERE payid='$pid'";
	mysqli_query($con,$sqlcopy) or die(mysqli_connect_error());
	// Check if the data is added into tbpaydetails_tmp 
	$sqlchtmp = "SELECT * FROM tbpaydetails_tmp WHERE payid='$pid'";
	$rchtmp = mysqli_query($con, $sqlchtmp) or die(mysqli_connect_error());
	
	   if(mysqli_num_rows($rchtmp)>0){ // Confirm the data was copied
		   $nfound = mysqli_num_rows($rchtmp);
		  /*
		   echo "<script>
		            var nf ='$nfound';
					alert('Num found_tmp' + nf);
		         </script>";
		  */		 
		   // DELETE DATA FROM tbpaydetails after transperring data to tbpaydetails
		   $sqldelpd = "DELETE FROM tbpaydetails WHERE payid='$pid'";
		   mysqli_query($con,$sqldelpd) or die(mysqli_connect_error());
		 
			// Insert new updates into tbpaydetails
		  if($ntbrow>1){
	         for($i=0; $i<$ntbrow; $i++){
		         $itemupd = "selpayit".$i;
		         $amountupd = "amount".$i;
				 // Data from form submission
				 $itemv = $_POST[$itemupd];
				 $amountv = $_POST[$amountupd];
				 $dataform = $pid.$itemv.$amountv;
				if(!empty($itemv) && !empty($amountv)){
										
					$sqlcomb = "SELECT * FROM tbpaydetails_tmp WHERE itemid='$itemv'";
					$rcom = mysqli_query($con, $sqlcomb) or die(mysqli_connect_error());
					if(mysqli_num_rows($rcom)>0){
						while($rc=mysqli_fetch_array($rcom)){
							$pidtemp = $rc["payid"];
							$itidtemp = $rc["itemid"];
							$amounttemp = $rc["amount"];
							$dc = $pidtemp.$itidtemp.$amounttemp;
							if($dataform==$dc){ // if equal - no update 
								// Copy data back from tbpaydetails_tmp to tbpaydetails
								$sqlcp = "INSERT INTO tbpaydetails(payid, billno, itemid, amount) SELECT payid, billno, itemid, amount FROM tbpaydetails_tmp WHERE payid='$pidtemp' AND itemid='$itidtemp' AND amount='$amounttemp'";
								mysqli_query($con,$sqlcp) or die(mysqli_connect_error());
								
							} // End of if equal
						} // End of while
					} // End of if>0
					
					$sqlchex = "SELECT * FROM tbpaydetails WHERE payid='$pid' AND itemid='$itemv' AND amount='$amountv'";
					$rchex = mysqli_query($con, $sqlchex) or die(mysqli_connect_error());
					if(mysqli_num_rows($rchex)==0){  // Get data from form
						// ADD edited data ****
						$sqlnewd = "INSERT INTO tbpaydetails(payid, billno, itemid, amount) VALUES('$pid','".$billno."','$itemv','$amountv')";
						mysqli_query($con, $sqlnewd) or die(mysqli_connect_error());
					}
				} // if !empty($itemv)
		     } // End of for
  		   } // End of if - $ntbrow  
		  
	  } // End of if - confirm copy	   
				
	/*
	
  // 2. ADD/SAVE new updates *************
  
  */
  return true;
}

/*
  Billprint: Show payment bill for printing 
*/
 function Billprint($payid, $stid, $con){
	 echo "<script>var dvprint = document.getElementById('pbillid');
			              document.body.innerHTML='<p align=\"left\"></p>";  // Just ONLY ' (One) document.body.innerHTML=''
			// Get info of students
			$sqlst = "SELECT * FROM tbpayments WHERE id='$payid'";
			$rst = mysqli_query($con, $sqlst) or die(mysqli_connect_error());
			list($id, $bno, $stid, $pdate, $pmod, $bkname) = mysqli_fetch_array($rst);
			list($sid,$uname,$psw,$nlao, $neng,$snlao,$sneng, $bdate,$gd) = Userbyid($stid, $con);
			 if($gd==="m"){
				$fname = "ທ. ".$nlao." ".$snlao;
			 } else {
				$fname = "ນ. ".$nlao." ".$snlao; 
			 }
			// Get class, degree and area for student
			 list($scode,$clevel,$carea,$cclass,$cayear) = Currentstudent($stid,$con);
			 $nclass = Rclassname($cclass, $con);
			 $narea = Rsarea($carea, $con);
			 $ndg = Rdgree($clevel, $con);
			 
			 $flcls = $nclass.", ".$ndg;
			 $paydate = date("d-m-Y", strtotime($pdate)); // Change to dd/mm/yyyy
			// Get payment details
			$sqlprint = "SELECT * FROM tbpaydetails WHERE payid='$payid'";
			$rbl = mysqli_query($con,$sqlprint) or die(mysqli_connect_error());
			list($pdid, $billnum) = mysqli_fetch_array($rbl);
			 
  			$rpay = mysqli_query($con,$sqlprint) or die(mysqli_connect_error());
			if(mysqli_num_rows($rpay)>0){
				echo "<table align=\"left\" class=\"tbhbill\"><tr><td rowspan=\"2\"><img src=\"../images/logo.jpg\" style=\"margin-right: 6px\"></td><td>ສະຖາບັນ ບຸນເກີດ ສຶກສາ</td></tr>";
				echo "<tr><td>BOUNKEUTH INSTITUTE</td><td align=\"right\" style=\"width: 60%\">&nbsp;</td></tr>";
				echo "</table>";
				echo "<br><br>";
				//echo "<hr style=\"width: 100%\">";
				echo "<h2 align=\"center\">ໃບເກັບເງີນ</h2>";
				echo "<br>";
				echo "<p align=\"left\" style=\"margin-left: 50px\">ຊື່:&nbsp;$fname ,ຫ້ອງຮຽນ:&nbsp;$flcls <span style=\"margin-left: 150px;\">ເລກທີ:&nbsp;$billnum</span></p>";
				echo "<p align=\"left\" style=\"margin-left: 50px\">ວັນທີ:&nbsp;$paydate</p>";
				echo "<table align=\"center\" class=\"tbbilld\">";
				echo "<tr><th align=\"center\" style=\"width: 10%\">ລ/ດ</th><th>ລາຍການ</th><th align=\"center\" style=\"width: 30%\">ລາຄາ (ກີບ)</th></tr>";
				$i = 0;
				$mdpay = Mpayment($id, $con);
				$sumamt = 0;
		  			while($r=mysqli_fetch_array($rpay)){
					$i = $i + 1;
			 			$bill =$r["billno"];
						$itid =$r["itemid"];
						$itemname = Rpaylistname($itid, $con);
						$amt =$r["amount"];
						$amtdc = number_format($amt,0);
						$sumamt = $sumamt + $amt;
			 			echo "<tr><td align=\"center\">$i</td><td>$itemname</td><td align=\"center\">$amtdc</td></tr>";
		  			} // End of while
				$sumamt = number_format($sumamt,0);
				echo "<tr><td align=\"right\" colspan=\"2\"><b>ລວມທັງໝົດ:</b> </td><td align=\"center\"><b>$sumamt</b></td></tr>";
		 		echo "</table>";
				echo "<p style=\"margin-top: 15px; margin-left: 50px\"><b>ວິທີຈ່າຍ:</b>$mdpay</p>";
				echo "<div class=\"dpay\" style=\"margin-top: 15px; margin-bottom: 65px; margin-left: 150px\"><div class=\"subdpay\"><b>ຜູ້ຈ່າຍ</b></div><div class=\"subdpay\" style=\"margin-left: 300px\"><b>ຜູ້ຮັບ</b></div></div>";
				echo "<p style=\"margin-top: 20px; margin-left: 50px\"><b>ໝາຍເຫດ:</b>ຈ່າຍເງີນແລ້ວຖອນຄືນບໍ່ໄດ້<br>";
				echo "ທ່ານສາມາດຈ່າຍຄ່າຮຽນໄດ້ຜ່ານທະນາຄານການຄ້າຕ່າງປະເທດລາວ<br> ເລກບັນຊີ:<b>010120000248603001</b> ຊື່: <b>ສະຖາບັນບຸນເກີດສຶກສາ.</b> ໂທ: 020 2201 9784</p>";
			} // End of if 
			echo "';
			      window.print();
				  window.addEventListener('afterprint', function() {  
                     window.location.href = 'content.php?adstaff=ptfees&sad=schfees&sadkid=kidsfees&aftprint=yes&psid=$stid&ppid=$id';  
                   });
				</script>";
		
 }

/*
  SelectClass: Return class id and academic year from tbteaching based on teacher id
*/
function SelectClass($uid, $con) {
    $sqlutch = "SELECT subjid, classid, ayear FROM tbteaching WHERE userid='$uid' GROUP BY subjid, classid ORDER BY classid ASC";
    $rtch = mysqli_query($con, $sqlutch) or die(mysqli_connect_error());
    while($r = mysqli_fetch_array($rtch)) {
        $clid = $r["classid"];
		$subj = $r["subjid"];
		 $ayear = $r["ayear"];
		 list($dgid, $stid) = Rdgsarea($clid, $con);
		 
		  list($subjnamelao, $subeng) = Rsubjectname($subj, $con);
		  $classname = Rclassname($clid, $con);
		  $dgname = Rdgree($dgid, $con);
		  $saname = Rsarea($stid, $con);	  
		  $classname_full = $subjnamelao." - ".$classname.", ".$saname.", ".$dgname;

        echo '<option value="' . htmlspecialchars($clid) . '">' . htmlspecialchars($classname_full) . '</option>';
    }
}
/*
  teachingInfo: Return subject and class id from tbteaching based on teacher id
*/
function teachingInfo($tcherid, $con) {
    $sql = "SELECT subjid, classid FROM tbteaching WHERE userid='$tcherid' GROUP BY subjid, classid ORDER BY classid ASC";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());
    return mysqli_fetch_array($result);
}

/*
  GradeForm: Data entry form for grading students
*/
function GradeForm($gid, $tcherid, $clsid, $gtype, $gfor, $gdate, $con) {
  // Get class and subject names
   // echo "<script>alert('Hello from GradeForm! Teacher ID: $tcherid, Class ID: $clsid, Grade Type: $gtype, Grade For: $gfor, Grade Date: $gdate')</script>";
	list($dgid, $stid) = Rdgsarea($clsid, $con);
	$dgname = Rdgree($dgid, $con);
	$saname = Rsarea($stid, $con);	

	$gsub=teachingInfo($tcherid, $con)["subjid"]; // get Subject id from tbteaching
    list($subjnamelao, $subjnameeng) = Rsubjectname($gsub, $con);
	$classname = Rclassname($clsid, $con);
	$classname = $classname . ", " . $saname . ", " . $dgname;

    // Get students in the class
    $sqlstu = "SELECT * FROM tbcstudents WHERE class='$clsid'";
    $rst = mysqli_query($con, $sqlstu) or die(mysqli_connect_error());

    echo "<form id='gradeEntryForm' method='post' action='content.php?tch=savegrade'>";
    echo "<h5 style='margin-bottom: 16px;'>ບັນທືກຄະແນນ</h5>";
    echo "<div style='margin-bottom: 8px;'><b>ຫ້ອງ:</b> $classname<br> <b>ວິຊາ:</b> $subjnamelao</div>";
	echo "<div style='margin-bottom: 8px;'><b>ບັນທຶກຄັ້ງ ວັນທີ:</b> " . date("d-m-Y", strtotime($gdate)) . "</div>";
    echo "<input type='hidden' name='tcherid' value='$tcherid'>";
    echo "<input type='hidden' name='clsid' value='$clsid'>";
    echo "<input type='hidden' name='subjid' value='$subjid'>";
    echo "<input type='hidden' name='gtype' value='$gtype'>";
    echo "<input type='hidden' name='gdate' value='$gdate'>";

    echo "<table class='table table-bordered' style='width:100%; font-size: 12pt;'>";
    echo "<tr><th>ລ/ດ</th><th>ຊື່ ແລະ ນາມສະກຸນ</th><th>ຄະແນນ (number)</th><th>ຄະແນນ (A,B,C...)</th><th>ສະຖານະພາບ</th></tr>";

    $i = 0;
    while($r = mysqli_fetch_array($rst)) {
        $i++;
        $uid = $r["userid"];
        list($id, $uname, $psw, $namel, $namee, $snamel, $snammee, $bdate, $gender) = Userbyid($uid, $con);
        $fsname = ($gender == "m" ? "ທ. " : "ນ. ") . $namel . " " . $snamel;
        // Check if grade already exists for this student
		$gdate = date("Y-m-d", strtotime($gdate)); // Convert to Y-m-d format
		$sqlcheck = "SELECT * FROM tbgrades WHERE studid='$uid' AND teacherid='$tcherid' AND subjid='$gsub' AND classid='$clsid' AND grade_type='$gtype' AND gradefor='$gfor' AND gdate='$gdate'";
		$resultCheck = mysqli_query($con, $sqlcheck) or die(mysqli_connect_error());
		//$status = mysqli_num_rows($resultCheck) > 0 ? "<span style='color: green;'>ຢືນຢັນ</span>" : "<span style='color: red;'>ຍັງບໍ່ບັນທຶກ</span>";
        
		if(mysqli_num_rows($resultCheck) > 0) {
			//echo "<script>console.log('Grade already exists for student ID: $uid');</script>";
			$gradeData = mysqli_fetch_array($resultCheck);
			$gradeNum = $gradeData['grade_num'];
			$gradeText = htmlspecialchars($gradeData['grade_text']);
			$status = "<span style='color: green;'>ບັນທຶກແລ້ວ</span>";
		} else {
			echo "<script>console.log('No grade found for student ID: $uid');</script>";
			$gradeNum = '';
			$gradeText = '';
		}


        echo "<tr>";
        echo "<td align='center'>$i</td>";
        echo "<td>$fsname</td>";
        echo "<td><input type='number' step='0.01' min='0' max='100' name='gnum[$uid]' value='$gradeNum' class='form-control' style='width: 100px;' required></td>";
        echo "<td><input type='text' name='$uid' id='$uid' value='$gradeText' class='form-control' style='width: 80px;' required onchange='handleCheckInputGradeText(this, this.value, $uid, $tcherid, $gsub, $clsid, \"$gtype\", \"$gfor\", \"$gdate\")'></td>";
		echo "<td><span id='status'>$status</span></td>";
		echo "</tr>";
    }
    echo "</table>";
    echo "</form>"; 
}

/*
  StudentGradeList: Show list of students with their grades
*/
function GradeList($tcherid, $con) {
    $sql = "SELECT * FROM tbgrades WHERE teacherid='$tcherid' GROUP BY gdate,classid, gradefor, grade_type  ORDER BY gdate DESC";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        echo "<h3>ລາຍການບັນທຶກຄະແນນ</h3>";
        echo "<table class='table table-bordered'>";
        echo "<tr><th>ລດ</th><th>ວັນທີ</th><th>ວິຊາ</th><th>ຫ້ອງ</th><th>ປະເພດຄະແນນ</th><th>ສໍາລັບ</th><th>ສົກຮຽນ</th></tr>";
        $no = 1;
        while($row = mysqli_fetch_array($result)) {
			$gdate = date("d-m-Y", strtotime($row['gdate']));
			$subjid = $row['subjid'];
			list($subjnamelao, $subjnameeng) = Rsubjectname($subjid, $con);
			$clsid = $row['classid'];
			list($dgid, $stid) = Rdgsarea($clsid, $con);
			$dgname = Rdgree($dgid, $con);
			$saname = Rsarea($stid, $con);	
			$classname = Rclassname($clsid, $con);
	        $classfull = $classname . ", " . $saname . ", " . $dgname;
			$gtype_original = $row['grade_type'];
			$gtype = RgradeType($row['grade_type'], $con);
			$gradefor = $row['gradefor'];
			$schoolyear = $row['ayear'];
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td><a href='?tch=stgrades&gdatelist=$gdate&subjid=$subjid&clsid=$clsid&gtype=$gtype_original&gradefor=$gradefor&schoolyear=$schoolyear' style='color: blue; font-size: 10pt;'>$gdate</a></td>";
            echo "<td>" . htmlspecialchars($subjnamelao) . "</td>";
            echo "<td>" . htmlspecialchars($classfull) . "</td>";
            echo "<td>" . htmlspecialchars($gtype) . "</td>";
            echo "<td>" . htmlspecialchars($gradefor) . "</td>";
            echo "<td>" . htmlspecialchars($schoolyear) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No Records Yet.";
    }
}

/*
  RgradeType: Return grade type in teachers_studgrades.php
*/
function RgradeType($gtype, $con) {
	switch($gtype) {
		case "month":
			$gradetype = "ປະຈໍາເດືອນ";
			break;
		case "term":
			$gradetype = "ເທີມ";
			break;
		case "semester":
			$gradetype = "ພາກຮຽນ";
			break;
		case "year":
			$gradetype = "ປະຈໍາປີ";
			break;
		default:
			$gradetype = "ບໍ່ລະບຸ";
			break;
	}
	return $gradetype;
}

/*
  SelectSubjectGrade: Show subject selection for grading
*/
function SelectSubjectGrade($studentid, $con) {
    $sql = "SELECT subjid FROM tbgrades WHERE studid='$studentid' GROUP BY subjid";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        
        while($row = mysqli_fetch_array($result)) {
            $subjid = $row['subjid'];
            list($subjlao, $subeng) = Rsubjectname($subjid, $con);
            echo "<option value='$subjid'>$subjlao</option>";
        }
       
    } else {
        echo "No Subjects Found.";
    }
}

/*
  SelectType: Show grade type from tbgrades
*/
function SelectGradeType($studentid, $con) {
    $sql = "SELECT grade_type FROM tbgrades WHERE studid='$studentid' GROUP BY grade_type";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result)) {
			$gradetypeid = $row['grade_type'];
            $gradetypelao = RgradeType($row['grade_type'], $con);
            echo "<option value='$gradetypeid'>$gradetypelao</option>";
        }
    } else {
        echo "No Grade Types Found.";
    }
}

/*
 RstudentNumber: Get student number by class
*/
function RstudentNumber($classid, $con) {
    $sql = "SELECT COUNT(*) as total FROM tbcstudents WHERE class='$classid'";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return $row['total'];
    } else {
        return 0;
    }
}

/*
  SummaryClass: Show summary of classes with student counts
*/
function SummaryClass($degree, $starea, $con) {
	// degree : 1-high school and 8 - kindergarten
   $sqlcls = "SELECT class FROM tbcstudents WHERE degree='$degree' GROUP BY class"; 
		$rclass = mysqli_query($con,$sqlcls) or die(mysqli_connect_error());
		if(mysqli_num_rows($rclass)>0){
          echo "<table>";
		  echo "<tr><th>ຫ້ອງຮຽນ</th></tr>";
		  while($r=mysqli_fetch_array($rclass)){
			  $clid = $r["class"];
			  $clname = Rclassname($clid, $con);
			  $stunum = RstudentNumber($clid, $con);
			  //1 - refers to high school
			  echo "<tr><td><a href='content.php?sad=stud&sadkid=stud&dglist=$degree&stlist=$starea&cllist=$clid' style='font-size: 12pt;color: red'>$clname</a></td><td>&nbsp;ຈໍານວນນັກຮຽນ:&nbsp;$stunum ຄົນ</td></tr>";
		  }
		  echo "</table>";
		} else {
			echo "No Classes Found.";
		}
}

/*
  PaymentReportDate: Show payment report by date
*/
function PaymentListDate($admintype, $selected_month, $selected_year, $con) {
    $usertype = ""; // for student group
	// loop current student-first
	if($admintype == '6'){  // high school admin staff
		$usertype = '4';
		//$sqlcustu = "SELECT userid FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE usertype ='4')";
	} else if($admintype == '7'){ // kindergarten admin staff
		$usertype = '8';
		//$sqlcustu = "SELECT userid FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE usertype ='8')";
	} else {
		$usertype = '3';
		//$sqlcustu = "SELECT userid FROM tbcstudents WHERE userid IN (SELECT id FROM tbusers WHERE usertype ='3')";
	}

	//
	 $month_filter = "";
    if ($selected_month) {
        $month_filter .= " AND MONTH(a.paydate) = '" . mysqli_real_escape_string($con, $selected_month) . "'";
    }
    if ($selected_year) {
        $month_filter .= " AND YEAR(a.paydate) = '" . mysqli_real_escape_string($con, $selected_year) . "'";
    }

  			$sqlsum = "SELECT a.paydate, a.uid, b.itemid, SUM(b.amount) as total_amount
			FROM tbpayments a
			JOIN tbpaydetails b ON a.id = b.payid
			JOIN tbusers u ON a.uid = u.id
			WHERE u.usertype = '$usertype' $month_filter
			GROUP BY a.paydate, a.uid, b.itemid
			ORDER BY a.paydate DESC";

			$resultsum = mysqli_query($con, $sqlsum) or die(mysqli_connect_error());

			$prev_paydate = null;
			$subtotal = 0;

			if(mysqli_num_rows($resultsum) > 0) {		
				// First, fetch all rows and count paydate occurrences
				$rows = [];
				$paydate_counts = [];
				while($row = mysqli_fetch_array($resultsum)) {
					$rows[] = $row;
					$paydate = $row['paydate'];
					if (!isset($paydate_counts[$paydate])) {
						$paydate_counts[$paydate] = 0;
					}
					$paydate_counts[$paydate]++;
				}

				// Now, output the table with merged paydate cells
				$paydate_printed = [];
				$prev_paydate = null;
				$subtotal = 0;

				foreach ($rows as $idx => $rowsum) {
					$paydate = $rowsum['paydate'];
					$studentid = $rowsum['uid'];
					$itemid = $rowsum['itemid'];
					$itemname = RpaymentItemName($itemid, $con);
					$amount = $rowsum['total_amount'];

					// If paydate changes and it's not the first row, print subtotal row
					if ($prev_paydate !== null && $paydate !== $prev_paydate) {
						echo "<tr style='font-weight:bold; background:#f0f0f0;'><td>&nbsp;</td><td align='right'>ລວມເງີນ:&nbsp;</td><td>" . number_format($subtotal,2) . "</td><td></td></tr>";
						$subtotal = 0; // reset subtotal for new date
					}

					echo "<tr>";
					// Only print the paydate cell for the first occurrence, with rowspan
					if (!isset($paydate_printed[$paydate])) {
						$rowspan = $paydate_counts[$paydate];
						echo "<td rowspan='$rowspan'>" . date('d-m-Y', strtotime($paydate)) . "</td>";
						$paydate_printed[$paydate] = true;
					}
					echo "<td>" . htmlspecialchars($itemname) . "</td>";
					echo "<td>" . number_format($amount, 2) . "</td>";
				//	echo "<td><a href='payment_details.php?date=" . date('Y-m-d', strtotime($paydate)) . "'>View Details</a></td>";
 // Only print the View Details cell for the first occurrence, with rowspan
    if (!isset($view_printed[$paydate])) {
        $rowspan = $paydate_counts[$paydate];
        echo "<td rowspan='$rowspan'><a href='content.php?&sad=schfeesreport&sadkid=kidfeesreport&paydate=" . date('Y-m-d', strtotime($paydate)) . "&studentid=" . $studentid . "' style='color: blue;'>View Details</a></td>";
        $view_printed[$paydate] = true;
    }

					echo "</tr>";

					$subtotal += $amount;
					$prev_paydate = $paydate;
				}
								// Print last subtotal
					if ($prev_paydate !== null) {
						echo "<tr style='font-weight:bold; background:#f0f0f0;'><td>&nbsp;</td><td align='right'>ລວມເງີນ:&nbsp;</td><td>" . number_format($subtotal,2) . "</td><td></td></tr>";
					}

			} else {
        		echo "No Records Found.";
   			}
}

/*
  RpaymentItemName: Get payment item name by ID
*/
function RpaymentItemName($itemid, $con) {
    $sql = "SELECT payitem FROM tbpaylist WHERE id='$itemid'";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return $row['payitem'];
    } else {
        return "Unknown Item";
    }
}

/*
 PaymentDetails: List of detailed payments based on date
 */
function PaymentDetails($paydate, $con) {
	 $sql = "SELECT a.id as payid, a.uid as studentid, u.namelao, u.snamelao, u.gender, b.itemid, b.amount
			FROM tbpayments a
			JOIN tbpaydetails b ON a.id = b.payid
			JOIN tbusers u ON a.uid = u.id
			WHERE a.paydate = '" . mysqli_real_escape_string($con, $paydate) . "'
			ORDER BY u.namelao, u.snamelao";
	$result = mysqli_query($con, $sql) or die(mysqli_connect_error());

	if(mysqli_num_rows($result) > 0) {
		
		echo '<div style="display: flex; align-items: center; justify-content: flex-end; margin-bottom: 8px;">
    <i class="fa fa-print" style="font-size: 22px; color: #007bff; cursor: pointer; margin-right: 8px;" title="Print" onclick="printTableArea();">&nbsp;&nbsp;<span style="font-size:10pt;">ພີມອອກ</span></i>
</div>';
echo '<div id="print-area">';
        echo "<h5>ລາຍລະອຽດ ການຊໍາລະເງິນ</h5>";
        echo "<p>ວັນທີ: ".$paydate."</p>";
		echo "<table class='table table-bordered'>";
		echo "<tr><th>ລດ</th><th>ຊື່ ແລະ ນາມສະກຸນ</th><th>ຫ້ອງຮຽນ</th><th>ລາຍການ</th><th>ຈໍານວນເງີນ (ກີບ)</th></tr>";
		$no = 1;
		$total_amount = 0;
		while($row = mysqli_fetch_array($result)) {
			$student_name = $row['namelao'] . " " . $row['snamelao'];
			$classid = StudentCurrentClass($row['studentid'], $con)['class'];
			$class_name = Rclassname($classid, $con);
			$sex = $row['gender'];
			if($sex === 'm'){
				$student_name = "ທ. " . $student_name;
			} else {
				$student_name = "ນ. " . $student_name;
			}
			$itemname = RpaymentItemName($row['itemid'], $con);
			$amount = $row['amount'];
			$total_amount += $amount;

			echo "<tr>";
			echo "<td>" . $no++ . "</td>";
			echo "<td>" . htmlspecialchars($student_name) . "</td>";
			echo "<td>" . htmlspecialchars($class_name) . "</td>";
			echo "<td>" . htmlspecialchars($itemname) . "</td>";
			echo "<td>" . number_format($amount, 2) . "</td>";
			echo "</tr>";
		}
		echo "<tr style='font-weight:bold; background:#f0f0f0;'><td colspan='4' align='right'>ລວມເງີນ:</td><td>" . number_format($total_amount, 2) . "</td></tr>";
		echo "</table>";
		echo '</div>'; // End of print-area div
		// Print script
		echo '<script>
				function printTableArea() {
					var printContents = document.getElementById("print-area").innerHTML;
					var originalContents = document.body.innerHTML;

					document.body.innerHTML = printContents;

					window.print();

					document.body.innerHTML = originalContents;
					location.reload(); // Reload the page to restore event listeners
				}
			</script>';
	} else {
		echo "Please select View Details";
	}

}
/*
  StudentCurrentClass: Get current class of a student
*/
function StudentCurrentClass($studentid, $con) {
    $sql = "SELECT * FROM tbcstudents WHERE userid='$studentid'";
    $result = mysqli_query($con, $sql) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return $row;
    } else {
        return "Unknown Class";
    }
}
?>
