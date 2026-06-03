<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];

?>
<style></style>
<!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	<a id="tschelid" href="content.php?tch=tschedule">ຕາຕະລາງສອນ</a>
	<a id="lessonid" href="content.php?tch=lessex">ບົດຮຽນ ແລະ ບົດຝືກຫັດ</a>  <!-- class="active" -->
	<a id="sattdid" href="content.php?tch=sattend">ບັນທຶກການຂາດຮຽນ</a>
	<a id="stgrades" href="content.php?tch=stgrades">ບົດສອບເສັງ ແລະ ຄະແນນ</a> 
	<a id="techid" href="content.php?tch=tprofile"><i class="fa fa-fw fa-user"></i>&nbsp;<?php echo $un; ?></a>  <!-- $un from content.php -->
	<a href="login.php">ອອກຈາກລະບົບ</a> 
  </div>
</div>

<!-- Main DIV -->
<div style="width: 100%;float: left; display: inline; margin-top: 60px;"> 
	<?php
	  $teachcont="";  
	  $teachcont=$_GET["tch"];  // LINKS FROM VARIOUS PAGES CONCERNING teachers
	   
	   if(!empty($teachcont)){
		   switch($teachcont){			   
			   case "tschedule": // TEACHING SCHEDULE *****************
				  echo "<script>
						 var atsch=document.getElementById('tschelid');
						     atsch.classList.add('activealink');
				       </script>";
				   include("teachers_teachtable.php");
				  break;
			   case "lessex":  // LESSONS AND EXCERC *****************
			   case "lessexdel": // Delete lessons
			   case "fupload": // File upload 
			   case "lessearch": // Search for lessons
				  include("teachers_lessonexcs.php");
				  echo "<script>
						 var lsn=document.getElementById('lessonid');
						     lsn.classList.add('activealink');
				       </script>";
				   
				   // FILE UPLOAD
			 // INSERT AND SAVE LESSONS in case GET["tch"]==fupload *************************
				   $desfile="../files/lessons/";
				   $filename = basename($_FILES["filename"]["name"]);
	               //echo "Filename:".$filename;
				   $filepath=$desfile.$filename;
				   $filetype=pathinfo($filepath,PATHINFO_EXTENSION);
				   if(isset($_POST["upload"]) && !empty($_FILES["filename"]["name"])){
					  $allowtype = array('docx','pdf'); 
					   // No duplicate if file already in the directory
					  $dupload= date('Y-m-d H:i:s'); 
					  // Userfo function 
					   list($userid,$uname,$upsw)=Userinfo($guname,$gpsw,$con);
					  // echo "Authorization:  ".$userid." ".$uname." ".$upsw."<br>";
					  if(move_uploaded_file($_FILES["filename"]["tmp_name"], $filepath)){ 
						 // $_POST[] - from lessonexcs.php
			            $sconf=Savelessons($_POST["ssub"],$_POST["stype"],$userid,$_POST["ssqn"],$_POST["topic"],$_POST["description"],$filename,$filepath,$dupload,$con); 
						//echo "Save: ".$sconf;
						echo "<script type='text/javascript'>window.location.href = 'content.php?tch=lessex';</script>";
		                exit();
					  } // End of if move_upload   
				   } // End of if isset
				   // DELETE LESSONS ********************
				   $lid=$_GET["lessid"];
				   $fpathdel=$_GET["fname"];  //$_GET fname - file with path from lessonexcs.php
				   if(!empty($lid) && !empty($fpathdel)){
				    $cfdel=Dellessons($lid,$fpathdel,$con);
				     //echo $cfdel;
				    echo "<script type='text/javascript'>window.location.href = 'content.php?tch=lessexdel';</script>";
		            exit();
		          }
			    break; 
			// STUDENTS ATTENDANCE *********************
			case "sattend":
				echo "<script>
				       var satt = document.getElementById('sattdid');
					       satt.classList.add('activealink');
					  </script>";
				  include("teachers_attendance.php"); 
				   
			  break;
			// STUDENTS GRADES *********************
			case "stgrades":
				echo "<script>
				       var stg = document.getElementById('stgrades');
					       stg.classList.add('activealink');
					  </script>";
				  include("teachers_studgrades.php");
			  break;
			// PROFILE UPDATE/CHANGE **********************
		    case "tprofile": 
				  echo "<script>
				        var techlog=document.getElementById('techid');
					    techlog.classList.add('activealink');
				        </script>";
				   include("teachers_profile.php");
				  break;		   
		   } // End switch *********************************
		   
	   } else {
		   include("teachers_profile.php"); // FIRST LOGIN
		   echo "<script>
				  var techlog=document.getElementById('techid');
					  techlog.classList.add('activealink');
				 </script>";
	   } // End of if !empty($teachcont)
	?>
</div> <!-- End of Main DIV -->
<!-- ************ MODAL FORM - CHANGE PASSWORD *************** -->
<div id="mfchpw" class="modal">
  <!-- Modal content -->
  <div class="modal-content" style="height:65%;">
    <span class="close">&times;&nbsp;</span>
    <div class="modal-heading">
      <div align="center" class="msheading">ປ່ຽນລະຫັດຜ່ານ</div>
    </div>
    <div style="margin-top:10px" class="cominpt">
    <form id="fmodal" action="content.php?tch=tprofile" method="post">
	  <input type="hidden" name="userid" value="<?php echo $id; ?>"> <!-- get id from teachers_profile.php -->
	  <input type="hidden" name="huname" value="<?php echo $lginname; ?>" >
	  <input type="hidden" name="hpsw" value="<?php echo $lginpsw; ?>" >
      <!-- *** ?idf=modal *** -->
      <table align="left" style="margin-left:50px">
        <tr>
           <td>&nbsp;&nbsp;<label>ລະຫັດຜ່ານເກົ່າ&nbsp;</label></td><td><input type="text" id="pswoldid" name="pswold" /></td>
        </tr>
		 <tr>
           <td>&nbsp;&nbsp;<label>ລະຫັດຜ່ານໃໝ່&nbsp;</label></td><td><input type="text" id="pswnid" name="pswn" /></td>
        </tr>
		 <tr>
           <td>&nbsp;&nbsp;<label>ລະຫັດຜ່ານໃໝ່ ອີກເທື່ອໜື່ງ&nbsp;</label></td><td><input type="text" id="pswnid1" name="pswn1" onBlur="Clwmess();" /></td>
        </tr>
		<tr><td>&nbsp;</td><td><input type="submit" name="btnsubmit" value="ຢືນຢັ້ນ" style="width: 250px; height: 40px; padding: 5px;margin-top: 40px;"></td></tr> 
		<tr><td align="center" colspan="2"><div id="pswch" style="margin-top: 40px; color: red">&nbsp;</div></td></tr>
	  </table>
	</form>
	</div>
  </div>
</div>	
<div>
 <?php
 // CHANGE PASSWORD - Back from Modal form
	$ucode = $_POST["userid"];
	$uname = $_POST["huname"];
	$pswold = $_POST["hpsw"]; // Old password
	$npsw = $_POST["pswn1"]; //New password
	
    if(!empty($npsw)){
	  $sqlchps = "UPDATE tbusers SET passw='".$npsw."' WHERE id='$ucode'";
	  mysqli_query($con,$sqlchps) or die(mysqli_connect_error());
	  // Refresh page
	  echo "<script type='text/javascript'>window.location.href = 'content.php?tch=tprofile';</script>";
	  exit();	
		
	}
	
  ?>
</div>
<script>
//****** MODAL FORM - Change password for the teacher
var modfch=document.getElementById("mfchpw");
 // Get the <span> element that closes the modal
var btncross = document.getElementsByClassName("close")[0];
var btnmod=document.getElementById("btnchpsw");
 btnmod.onclick=function(){
   modfch.style.display="block";
   var pwold = document.getElementById("pswoldid");
   var pwnew = document.getElementById("pswnid");
       pwold.value = "<?php echo $psw; ?>";
	   pwold.disabled = true;
       pwnew.focus(); // Set focus
 }
 btncross.onclick=function(){
   modfch.style.display="none";
 }
 $(document).ready(function(){ 
  
 }); // End of document.ready
	
 $("#pswnid1").change(function(){
	 var pwn = document.getElementById("pswnid").value;
	 var pwn1 = $(this).val();
	 var wmess = document.getElementById("pswch");
	  
	 if(pwn===pwn1){  // Operator for checking all corrects
		 if(wmess.length>0){
			 alert("The same-Cleaning");
			 wmess.innerHTML=""; 
			}
		 
		} else {
		  if(pwn1.length>0){
			wmess.innerHTML="ລະຫັດຜ່ານໃໝ່ ບໍ່ຖືກກັນ, ກະລຸນາ ກວດ ແລະ ແປງຄືນ!";  
		  } else {
			wmess.innerHTML="";  
		  }
		    
		}
  });

 function Clwmess(){
	var pwnls = document.getElementById("pswnid").value;
	var pwnls1 = document.getElementById("pswnid1").value; 
	var wmessls = document.getElementById("pswch");
	 if(pwnls!==pwnls1){
		 if(wmessls.length>0){
			wmessls.innerHTML=""; 
		 }
		}
 }
   
</script>