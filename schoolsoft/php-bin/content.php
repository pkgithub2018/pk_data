<?php
  session_start();
?> 
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Content</title>
 	
	<link href="../fontawesome/css/all.css" rel="stylesheet">
 	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
	<!-- Bootstrap 5.3.2 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- jQuery (needed by DataTables) -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap 5.3.2 JS Bundle -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

	<!-- DataTables (Bootstrap 5 style) -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

</head>
 <?php
  include("../header.php"); // This file helps to run css as it is included here by using php as below
 ?>
<style>
 <?php
    include("../css/lafont.css");
	include("../css/initcss.css");
 ?>	
 body {
  /* font-family: "Open Sans", sans-serif; */
  height: 100vh;
 /* background: image();  #FFD580 */
 /* background-image: url("images/bg.jpg"); */
  background-position: right;
  background-repeat: no-repeat;
  background-size: cover;
}
</style>
<script>
// CHANGE TABLE ROW COLOR WHEN MOUSEMOVE AND MOUSEOUT 
  var norColor="#FDFEFE";	
  var chColor="#D6EAF8";
  function Normrow(trow){
	  trow.style.backgroundColor=norColor;
  }
  function Chcrow(trow){
	  trow.style.backgroundColor=chColor;
  }
</script>
<body>
<?php
require("connection.php"); // replace include with require
require("supports.php");
	
if(isset($_SESSION["username"]) && isset($_SESSION["passw"])){
      $_SESSION["guname"]=""; //******Destroy session first
	  $_SESSION["gpsw"]="";
	  $_SESSION["uid"]="";
	  $_SESSION["usertype"]="";
  }
$lginname="";
$lginpsw="";
$runame="";
$rpsw="";
// IN CASE OF SUBMISSION THROUGH FORM
if(!empty($_POST['username']) && !empty($_POST['passw'])){
	$runame=$_POST['username'];
	$rpsw=$_POST['passw'];
	list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status, $rgdate, $lupdate) = Userinfo($runame,$rpsw,$con); // Get user id
	$_SESSION["uid"] = $id; // Store user id in session 
}

// IN CASE OF SUBMISSION THROUGH LINKS
if(!empty($_GET['username']) && !empty($_GET['passw'])){
	$runame=$_GET['username'];
	$rpsw=$_GET['passw'];
}
// FIRST LOGIN *************	
 if(!empty($runame) && !empty($rpsw)){
    // To be sent to each page after first login
	 $_SESSION["guname"]= $runame;
     $_SESSION["gpsw"]= $rpsw; 
	// To check against users in database 
	 $lginname=$runame;
	 $lginpsw=$rpsw;
	 
 } else {
	 //GET BACK username and password FROM PAGES
    if(!empty($_SESSION["guname"]) && !empty($_SESSION["gpsw"])){
       $lginname=$_SESSION["guname"];
       $lginpsw=$_SESSION["gpsw"];
     } else {	
	  if(!empty($_POST["huname"]) && !empty($_POST["hpsw"])) { //Send back
		 $lginname=$_POST["huname"];
		 $lginpsw=$_POST["hpsw"];
	  } else {
	   echo "<script type='text/javascript'>window.location.href = 'login.php?access=emtp';</script>";
       exit(); 
	  }
	}
 } // End of first login **********

	
if(!empty($lginname) && !empty($lginpsw)){	
//echo "Hi Login".$_SESSION["guname"]."   ".$_SESSION["gpsw"];
$con->set_charset("utf8");
$sqllog="SELECT id, username,passw,namelao,snamelao,usertype,status FROM tbusers 
         WHERE username='".$lginname."' 
		 AND passw='".$lginpsw."'
		 AND status='enable'";
$rlogin=mysqli_query($con,$sqllog) or die(mysqli_connect_error());
// SUCCESSFUL LOGIN - MAIN IF *****************************
   if(mysqli_num_rows($rlogin)>0){
	 list($id,$un,$pw,$uname,$sname,$utype,$ustatus)=mysqli_fetch_array($rlogin);
	 $_SESSION["usertype"]=$utype;
	 $_SESSION["uid"]=$id;
   } else { //  UNSUCCESSFUL LOGIN
	echo "<script type='text/javascript'>window.location.href = 'login.php?access=incorrect';</script>";
    exit(); 
   } // End of if $rlogin>0
 }  // End of !empty
//CHECK SUCCESSFUL USER's TYPE
$usertype="";
if(!empty($utype)){
  $usertype=$utype;  // From login-database
} else {
  $usertype=$_GET["ut"];  // From links from every page	
} 

if(!empty($usertype)){
// DIV - MAIN CONTENT
	switch($usertype){  // INDEX NUMBER : 1,2,3,4 and 5 - Look at tbusertype
		case "1": // Teachers
		//case "lessex": // Lessons and exercise
		 include("teachers.php");
		 break;
		case "2": // System adminstrator
		 include("sadmin.php");
		 break;
		case "3":  // College Student
		 include("students.php");
		 break;
		case "4":  // School children
		// echo "hello, children";
		 include("schildren.php");
		 break;	
		case "5":  // Admin staff
		 include("adminstaff.php");
		 break;

		case "6":  // School admin for primary, middle and high school
		 include("admin_school.php");
		 break;
		case "7":  // Kindergarten admin
		 include("admin_kids.php");
		 break;

	} // End of switch	
} // End of if-empty
?>	 

<script src="../js/handleCheck.js"></script> <!-- To add handleCheck.js file -->
</body>
</html>