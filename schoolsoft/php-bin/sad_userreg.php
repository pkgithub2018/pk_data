<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
 $utype=$_SESSION["usertype"];
?> 
<!-- MAIN DIV -->
<div align="center" style="" class="usform">  <!-- *** class="csfelment" *** -->
<!-- *********** LEFT SIDE ******************* -->
 <div class="usform-lside">
  <form method='post' action='content.php?sad=reguser&sadkid=reguser' enctype='multipart/form-data' style="width: 95%; padding: 5px;">
  <input type="hidden" name="huid" value="<?php $guid=$_GET["userid"]; echo $guid; ?>" /> <!-- Get userid in case of user update -->
  <div class="usform-content">
	  <h2 style="width: 80%">ລົງທະບຽນຜູ້ໃຊ້</h2> 
	  <table align="left" class="usform-tb">
		 <tr>
			 <td align="right">ຊື່ (ພາສາລາວ)&nbsp;</td><td align="left">&nbsp;<input type="text" name="namelao" id="namelaoid" style="width: 270px;" />
			 <td align="right">&nbsp;(ອັງກິດ)&nbsp;</td><td align="left">&nbsp;<input type="text" name="nameeng" id="nameengid" style="width: 270px;" /></td>
		  </tr>
		 <tr>
			 <td align="right">ນາມສະກຸນ (ພາສາລາວ)&nbsp;</td><td align="left">&nbsp;<input type="text" name="snamelao" id="snamelaoid" style="width: 270px;" /></td>
			 <td align="right">&nbsp;(ອັງກິດ)&nbsp;</td><td align="left">&nbsp;<input type="text" name="snameeng" id="snameengid" style="width: 270px;" /></td>
		  </tr>
		  <tr>
			 <td align="right">ວດປ ເກີດ&nbsp;</td><td align="left">&nbsp;<input type="date" name="db" id="dbid" style="width: 170px;" /></td>
			 <td align="right">&nbsp;</td>
			  <td>  
			  <fieldset class="usform-fset">
					  <legend style="font-size: 15px; padding: 5px">ເພດ</legend>
					  <div style="padding: 5px">
                        <input type="radio" name="rmale" id="rmaleid" value="m" onClick="switchratio(this.id);">
                        <label for="male">ຊາຍ</label>
                      </div>
                     <div style="padding: 5px">
                       <input type="radio" name="rfemale" id="rfemaleid" value="f" onClick="switchratio(this.id);">
                       <label for="female">ຍິງ</label>
                     </div>
				  </fieldset>
			  </td>
		  </tr>
		  <tr>
			 <td align="right">ໂທລະສັບ/ວອດແອບ&nbsp;</td><td colspan="3" align="left">&nbsp;<input type="text" name="phone" id="phoneid" style="width: 500px;" /></td>
		  </tr>
		  <tr>
			 <td align="right">ອີແມ໋ວ&nbsp;</td><td colspan="3" align="left">&nbsp;<input type="text" name="email" id="emailid" style="width: 500px;" /></td>
		  </tr>
		  <tr>
		    <td align="right" valign="top">ທີ່ຢູ່ປະຈຸບັນ&nbsp;</td><td colspan="3" align="left">&nbsp;<textarea rows='2' cols='65' name='caddress' id='caddressid' style="border: 1px solid #E7E0E0; border-radius: 5px; font-size: 11pt"></textarea></td>
		  </tr>
		  <tr>
			 <td align="right">ປະເພດຜູ້ໃຊ້&nbsp;</td><td colspan="3" align="left">&nbsp;<select name='susertype' id='susertypeid' style="width: 500px;" /></select></td>
		  </tr>
	  </table>
 </div>	
  <!-- BUTTON -->
	<p align="right" style="margin-right: 20px"><input type="reset" name='btnUsercancel' id='btnUsercancelid' value='ຍົກເລີກ' style="width: 20%;"/>&nbsp;&nbsp;<input TYPE='submit' name='btnUsersave' id='btnUsersaveid' value='ບັນທຶກ' style="width: 20%; margin-right: 10px;" /></p>
 </form>
	<div align="left" class="usbtn">
		<div class="usbtn-fdownup" style="display: inline-block">
		 <a href="expdraftuser.php" style="color: black">
			<div style="float:left">
			  <i class="fa fa-download" style="font-size: 40px; color: #007bff;"></i>
			  <!-- <img src="../images/fdownload.jpg"> -->
			 </div>
			<div style="float:left; border: none; margin-left: 3px;">
				<span style="font-size: 14pt; font-weight: bold; padding: 0px">ດາວໂຫຼດ</span><br>
				<span style="font-size: 11pt; padding: 0px">ຮ່າງຟາຍຂໍ້ມູນ</span>
			 </div>
		  </a>	 
		</div>
		<div class="usbtn-fdownup" style="margin-left: 120px;text-align: left; display: inline-block">
		   <button id="btnfileup" style="border: none; background-color: white;">
			  <div style="float:left">
			  <!-- <img src="../images/fupload.jpg"> -->
			   <i class="fas fa-upload" style="font-size: 40px; color: #28a745;"></i>
			   </div>
			   <div style="float:left;">
				<span style="font-size: 14pt; font-weight: bold; padding: 0px;float: left">ອັບໂຫຼດ</span><br>
				<span style="font-size: 11pt; padding: 0px; float: left">ຟາຍຂໍ້ມູນທີ່ຕື່ມແລ້ວ</span>
			   </div>
			</button>
		</div>		
	</div>
</div>
<!-- *********** RIGHT SIDE ******************* -->
  <div style="width: 35%; float: left; display:inline-block; background-color:white; margin-top: 10px; margin-left: 15px">
	<h2>ລາຍຊື່ຜູ້ໃຊ້</h2>
	 <div class="dposition"> <!-- ****** Heading - search box ****** --->
        <form id="fsearchuser" action="content.php?sad=reguser&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post">
		  <div class="sbox"><img src="../images/search.jpg" align="middle" />
   ຊອກຫາ:&nbsp;<input type="text" name="searchuser" id="searchuserid" placeholder="ກະລຸນາ ພີມຊື່ຜູ້ໃຊ້ (ເປັນພາສາລາວ)" style="width: 70%" onchange="subfsearch();"  />
          </div>
		</form>
	  </div>
	<?php
 
 // SHOW LIST OF USERS ********************************************************
    // echo "<script>alert('test " . $utype . "');</script>";

	  $seartext=$_POST["searchuser"]; // In case of searching
	  Showusers($seartext, $utype, $con); // this found is in supports.php - LIST OF USERS
 // UPDATE  user's status*************
	if(!empty($_GET["subsadst"])){ // Link from function: Showusers in supports.php
		$cuserstatus=$_GET["subsadst"];
		$uid=$_GET["userid"];
		$sqlustup="";
		// USER'S STATUS BY doubleclick on <a> tag in supports.php
		if($cuserstatus=="enable"){
		  $sqlustup="UPDATE tbusers SET status='disable' WHERE id='$uid'";
		} else {
		  $sqlustup="UPDATE tbusers SET status='enable' WHERE id='$uid'";
		}
		mysqli_query($con,$sqlustup) or die(mysqli_connect_error());
	 } // if empty	- status
 // UPDATE user's all record data *****************************************
	 if(!empty($_GET["subsadup"]) && $_GET["subsadup"]=="userupdate"){                      
	   $uidup=$_GET["userid"];
	  list($username, $passw, $namelao, $nameeng, $snamelao, $snameeng, $dbirth, $gender, $mphone, $email, $address, $utype)= Guser($uidup,$con);
	 }
	 ?>
  </div> <!-- End of RIGHT SIDE -->
</div> <!-- End of MAIN DIV -->
<!-- ************ MODAL FORM - Upload user template file *************** -->
<div id="mffupload" class="modalfup">
 <!-- Modal content -->
  <div class="modalfup-content">
    <span class="close">&times;&nbsp;</span>
    <div class="modalfup-heading">
      <div align="center" class="msheading">ອັບໂຫຼດຟາຍ</div>
    </div>
    <div style="margin-top:10px" class="modalfup-form">
    <form id="fmodalfup" action="content.php?sad=userfup" enctype='multipart/form-data' method="post">
	  <input type="hidden" name="hmfuname" value="<?php echo $lginname; ?>" >
	  <input type="hidden" name="hmfpsw" value="<?php echo $lginpsw; ?>" >
      <!-- *** ?idf=modal *** -->
       <div align="center">
	      ກະລຸນາເລືອກຊື່ຟາຍ ທີຕ້ອງການອັບໂຫຼດ&nbsp;&nbsp;<input type='file' name='filename'><br><br>
          <input type='submit' name='upload' value='ອັບໂຫຼດ' style='text-align: center'/>
	  </div>
	</form>
	</div>
  </div>
</div>
<!-- End of Modal form -->
<?php 
 // USER TYPE SELECT *********************	
echo "<script>
	  var sutype=document.getElementById('susertypeid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sutype.options.add(opt_non);
	  </script>";
 $con->set_charset("utf8");
 switch($utype){
	 case "6":  //ພ/ງ ບໍລິຫານ-ສາມັນ
		 $sqlutype = "SELECT id,usertype FROM tbusertype WHERE id='4'";  
		 break;
	 case "7":  //ພ/ງ ບໍລິຫານ-ອະນຸບານ
		 $sqlutype = "SELECT id,usertype FROM tbusertype WHERE id='8'";  
		 break;
	
	 default:
		 $sqlutype = "SELECT id,usertype FROM tbusertype";  
 }
 $rutype = mysqli_query($con,$sqlutype) or die(mysqli_connect_error());
 while($rw=mysqli_fetch_array($rutype)){
	$userid=$rw["id"];
	$usertype=$rw["usertype"];
  // Province select 
	echo "<script>
		  var uid='$userid';
		  var utype='$usertype';	     
		  var opt=document.createElement('option');
			       opt.value=uid;
			       opt.text=utype;
			   sutype.options.add(opt);
              </script>";
 }	
// USER REGISTRATION FORM SUBMISSION **********************
//SAVE - INSERT button ************************************
if(isset($_POST["btnUsersave"]) && $_POST["btnUsersave"]=="ບັນທຶກ"){ 
	//echo "<script>alert('Save user !');</script>";
  $namelao=$_POST["namelao"];
  $nameeng=$_POST["nameeng"];
  $snamelao=$_POST["snamelao"];
  $snameeng=$_POST["snameeng"];
  $dbirth=$_POST["db"];
  $gender="";
  if(!empty($_POST["rmale"])){
	$gender= $_POST["rmale"];
  } else {
	if(!empty($_POST["rfemale"])){
	  $gender= $_POST["rfemale"];	
	}  
  }
   $phone=$_POST["phone"];
   $email=$_POST["email"];
   $caddress=$_POST["caddress"];
   $utype=$_POST["susertype"];
   $ckempty="";  // Check empty key imput
   $ckempty=Checkuserinfo($namelao,$nameeng,$snamelao,$snameeng,$dbirth,$gender,$phone,$utype,$con);
   // if 1
   if($ckempty=="input"){
	 // Check if the user exists
	 $checkext="";
	 $checkext=Checkuexist($namelao,$nameeng,$snamelao,$snameeng,$dbirth,$gender,$con);
	 // if 2
	 if($checkext=="notexist"){ // If the user does not exist in database - NEW USER
	   // INSERT DATA****************************
	 Saveusers($namelao,$nameeng,$snamelao,$snameeng,$dbirth,$gender,$phone,$email,$caddress,$utype,$con);
	  echo "<script type='text/javascript'>window.location.href = 'content.php?sad=reguser&sadkid=reguser';</script>";
	  exit(); 
	 } else {
	   echo "This user already exists !"; // Modal form pops up	 
	 } // End of if 2 	 
   } else {
	 echo "Go back and refill";  
   } // End of if 2 ='input' - 
} // End of if - SAVE submission
// SAVE button for UPDATE ************************************ປັບປຸງ
if(isset($_POST["btnUsersave"]) && $_POST["btnUsersave"]=="ປັບປຸງ"){
  $userid = $_POST["huid"];
  //echo "Userid: ".$userid;
  $nlaoup=$_POST["namelao"];
  $nengup=$_POST["nameeng"];
  $snlaoup=$_POST["snamelao"];
  $snengup=$_POST["snameeng"];
  $dbirthup=$_POST["db"];
  $genderup="";
  if(!empty($_POST["rmale"])){
	$genderup= $_POST["rmale"];
  } else {
	if(!empty($_POST["rfemale"])){
	  $genderup= $_POST["rfemale"];	
	}  
  }
   $phoneup=$_POST["phone"];
   $emailup=$_POST["email"];
   $caddressup=$_POST["caddress"];
   $utypeup=$_POST["susertype"];
   //echo "Hello".$nlaoup."  ".$nengup."  ".$snlaoup."  ".$snengup."  ".$dbirthup."  ".$genderup."  ".$phoneup."  ".$utypeup;
   $ckemptyup="";  // Check empty key imput
   $ckemptyup=Checkuserinfo($nlaoup,$nengup,$snlaoup,$snengup,$dbirthup,$genderup,$phoneup,$utypeup,$con);

 if($ckemptyup=="input"){
	 //echo "Save it";
	 Userupdate($userid,$nlaoup,$nengup,$snlaoup,$snengup,$dbirthup,$genderup,$phoneup,$emailup,$caddressup,$utypeup,$con);
	 echo "<script type='text/javascript'>window.location.href = 'content.php?sad=reguser';</script>";
	 exit();
   } else {
	  echo "Refill update";
   }  
}
//CANCEL/RESET button ************************************
if(isset($_POST["btnUsercancel"])){  
  // Clicking this button will change SAVE button value from "ປັບປຸງ" to "ບັນທຶກ" - In Javascript
}
// FILE UPLOAD - As Modal form is submitted *****************************
 $csvext = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');  
if(!empty($_FILES['filename']['name']) && in_array($_FILES['filename']['type'], $csvext)){ // if 1
   //echo "Hello, file name".$_FILES['filename']['name'];
	if(is_uploaded_file($_FILES['filename']['tmp_name'])){ // if 2
	  // Open uploaded CSV file with read-only mode
       $csvFile = fopen($_FILES['filename']['tmp_name'], 'r');         
      // Skip the first line
      fgetcsv($csvFile);
	  // Parse data from CSV file line by line
      while(($line = fgetcsv($csvFile, 10000, ',')) !== FALSE){
		  $unamel = $line[0]; // username in lao
		  $unameeng = $line[1]; // username in english
		  $usnamel = $line[2]; // surname in lao
		  $usnameeng = $line[3]; // surname in English
		  $udbirth = $line[4]; 
		  $usex = $line[5];
		  $uphone = $line[6];
		  $uemail = $line[7];
		  $add = $line[8];
		  $ut = $line[9]; // user type 
		  $cdate= date('Y-m-d'); // Current date
		  // Check if user exists ****************
		  $chextup="";
		  $chextup=Checkuexist($unamel,$unameeng,$usnamel,$usnameeng,$udbirth,$usex,$con);
		  if($chextup=="notexist"){
			// INSERT INTO thusers (database) 
		    Saveusers($unamel,$unameeng,$usnamel,$usnameeng,$udbirth,$usex,$uphone,$uemail,$add,$ut,$con);  
		  }		  
	  } // End of while
	  // Refresh the page
	  echo "<script type='text/javascript'>window.location.href = 'content.php?sad=reguser';</script>";
      exit();
	 // Close opened CSV file
      fclose($csvFile);
	} // End of if 2
} //End of if 1
?>
<script>	
$(document).ready(function () {
  // GET BACK DATA TO FORM - User registration 
  var usnameup="<?php $us=$username; echo $us; ?>"; // username is received from function Guser in supports.php
  var passwup="<?php $ps=$passw; echo $ps; ?>";
  var namelao="<?php $nlao=$namelao; echo $nlao; ?>";
  var nameeng="<?php $neng=$nameeng; echo $neng; ?>";
  var snamelao="<?php $snlao=$snamelao; echo $snlao; ?>";
  var snameeng="<?php $sneng=$snameeng; echo $sneng; ?>";
  var dbirth="<?php $db=$dbirth; echo $db; ?>";
  var gender="<?php $gd=$gender; echo $gd; ?>";
  var phone="<?php $phone=$mphone; echo $phone; ?>";
  var email="<?php $email=$email; echo $email; ?>";
  var address="<?php $addr=$address; echo $addr; ?>";	
  var usertype="<?php $ustype=$utype; echo $ustype; ?>";
 // UPDATE user******************************************	
  if(usnameup.length>0){  
	// alert("Hi" + usnameup);
	var txtnamel = document.getElementById("namelaoid");
	var txtnamee = document.getElementById("nameengid");
	var txtsnamel = document.getElementById("snamelaoid");
	var txtsnamee = document.getElementById("snameengid");
	var txtdbirth = document.getElementById("dbid");
	var txtphone =document.getElementById("phoneid");
	var txtemail = document.getElementById("emailid");
    var txtaddr = document.getElementById("caddressid");
	var btnSave = document.getElementById("btnUsersaveid"); // Save button
	  
	txtnamel.value = namelao;
	txtnamee.value = nameeng;
    txtsnamel.value = snamelao;
	txtsnamee.value = snameeng;
	txtdbirth.value = dbirth;
	// Gender
	if(gender.length>0){
	   if(gender=="m"){
		  document.getElementById("rmaleid").checked=true;
		  document.getElementById("rfemaleid").checked=false; 
	   } else {
		  document.getElementById("rmaleid").checked=false;
		  document.getElementById("rfemaleid").checked=true; 
	   }
	 }
	txtphone.value = phone;
	txtemail.value = email;
	txtaddr.value = address;
	// Usertype
	var sutype = document.getElementById("susertypeid");
	if(usertype.length>0){ // if - usertype
	  // alert("Utype:" + usertype);
	   for(k=0; k<sutype.length; k++){
		 if(sutype[k].value==usertype){
		    sutype.selectedIndex=k;  // Go back to relevant value in select
		 }  
	   }
	 }
	// ********Change BACKGROUND COLOR OF INPUT
      btnSave.value="ປັບປຸງ";
      btnSave.style.color="yellow";
	  txtnamel.style.backgroundColor="lightyellow";
	  txtnamee.style.backgroundColor="lightyellow";
	  txtsnamel.style.backgroundColor="lightyellow";
	  txtsnamee.style.backgroundColor="lightyellow";
	  txtdbirth.style.backgroundColor="lightyellow";
	  txtphone.style.backgroundColor="lightyellow";
	  txtemail.style.backgroundColor="lightyellow";
	  txtaddr.style.backgroundColor="lightyellow";
	  sutype.style.backgroundColor="lightyellow";
   } // End of if - usertype
 // CANCEL BUTTON - Set INPUT empty and change SAVE button's value
 var btnCancel= document.getElementById("btnUsercancelid");	
	 btnCancel.onclick=function(){
	  window.location.href = 'content.php?sad=reguser'; // Refresh page so that no need to change background color of input
	  var btSavechange = document.getElementById("btnUsersaveid");
	   btSavechange.value ="ບັນທຶກ";
	   btSavechange.style.color="white";  
	   document.getElementById("namelaoid").focus();
	  // return;
	 }
 // When page starts - set focus to name input
  var namel=document.getElementById("namelaoid");
   namel.focus(); 
});
 function subfsearch(){
   $("#fsearchuser").submit();
 }
// RADIO FOR GENDER
 function switchratio(ratioid){
   var rmale=document.getElementById("rmaleid");
   var rfemale=document.getElementById("rfemaleid");
   if(ratioid=="rmaleid"){
	  rmale.checked=true;
	  rfemale.checked=false;
	}
	if(ratioid=="rfemaleid"){
	  rmale.checked=false;
	  rfemale.checked=true;
	}
 }
// CANCEL BUTTON IS ON CLICK
var btnccel=document.getElementById("btnUsercancelid");
	btnccel.onclick=function(){
	 document.getElementById("namelaoid").focus();	
	}
// MODAL FORM - USER TEMPLATE FILE UPLOAD
var mffup = document.getElementById("mffupload");
var btncrossfup = document.getElementsByClassName("close")[0];
var btnfupload= document.getElementById("btnfileup");
	btnfupload.onclick=function(){
	 mffup.style.display="block";
	}
	btncrossfup.onclick = function(){
	  mffup.style.display="none";	
	}
</script>