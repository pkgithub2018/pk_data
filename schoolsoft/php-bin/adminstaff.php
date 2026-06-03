<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
   
?>
<!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	<a id="ptfeesid" href="content.php?adstaff=ptfees">ຈ່າຍຄ່າຮຽນ ແລະ ອື່ນໆ</a>
	<a id="persfeesid" href="content.php?adstaff=persfees">ຄ່າຮຽນສ່ວນບຸກຄົນ</a>
	<a id="pinfoid" href="content.php?adstaff=pinfo">ຂໍ້ມູນສ່ວນຕົວ</a>
	<a id="examrsid" href="#">ຕາຕະລາງຮຽນ</a>  <!-- class="active" -->
	<a id="absenceid" href="content.php?stud=sabsence">ການຂາດຮຽນ</a>
	<a id="tuifid" href="currlum.php?access=curr">ສະຫຼຸບລາຍຮັບ</a> 
	<a id="sprofileid" href="content.php?adstaff=begin"><i class="fa fa-fw fa-user"></i>&nbsp;<?php echo $un; ?></a>  <!-- $un from content.php -->
	<a href="login.php">ອອກຈາກລະບົບ</a> 
  </div>
</div>
<?php
  // Userinfo($guname,$gpsw,$con);
   // id,username,passw,namelao,snamelao,usertype,status - CAN GET GROM content.php
  // echo "User's infor: ".$un." ".$pw." ".$uname." ".$sname;
?>
<!-- Main DIV -->
<div style="width: 100%;float: left; display: inline; margin-top: 95px"> 
	<?php
	  $stulink = "";
	  $stulink = $_GET["adstaff"];
	  if(empty($stulink)){
		$stulink = 'begin';  
	  }
	
	  switch($stulink){
		  case "begin":
			  echo "<script>
				    var stable = document.getElementById('sprofileid');
					    stable.classList.add('activealink');
					</script>";
			  include("adminstaff_profile.php");
			  break;
			  
		  case "ptfees":
			echo "<script>
				    var stable = document.getElementById('ptfeesid');
					    stable.classList.add('activealink');
					</script>";
			 include("adminstaff_payfees.php");
			break;  
			  
		case "lesson":
			echo "<script>
				    var lson = document.getElementById('lssonid');
					    lson.classList.add('activealink');
					</script>";
			include("students_lessons.php");
			break; 
			  
		case "exercise":
			echo "<script>
				    var exc = document.getElementById('excid');
					    exc.classList.add('activealink');
					</script>";
			include("students_excercise.php");
			break; 	
			  
		case "sabsence":
		   echo "<script>
				    var exc = document.getElementById('excid');
					    exc.classList.add('absenceid');
					</script>";
			include("students_absence.php");
			break; 	
			  
	  }
	?>
</div> <!-- End of Main DIV -->
<!-- ************ MODAL FORM - CHANGE PASSWORD *************** -->
<div id="mfchpw" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;&nbsp;</span>
    <div class="modal-heading">
      <div align="center" class="msheading">ປ່ຽນລະຫັດຜ່ານ</div>
    </div>
    <div style="margin-top:10px" class="cominpt">
    <form id="fmodal" action="content.php" method="post">
	  <input type="hidden" name="huname" value="<?php echo $lginname; ?>" >
	  <input type="hidden" name="hpsw" value="<?php echo $lginpsw; ?>" >
      <!-- *** ?idf=modal *** -->
      <table align="left" style="margin-left:50px">
        <tr>
           <td>&nbsp;&nbsp;<label>ລະຫັດຜ່ານເກົ່າ&nbsp;</label></td><td><input type="text" id="pswoldid" name="pswold" /></td>
        </tr>
		 <tr>
           <td>&nbsp;&nbsp;<label>ລະຫັດຜ່ານເກົ່າອີກເທື່ອໜື່ງ&nbsp;</label></td><td><input type="text" id="pswoldidag" name="pswoldag" /></td>
        </tr>
		 <tr>
           <td>&nbsp;&nbsp;<label>ລະຫັດຜ່ານໃໝ່&nbsp;</label></td><td><input type="text" id="pswnewid" name="pswnew" /></td>
        </tr>
		<tr><td>&nbsp;</td><td><input type="submit" name="btnsubmit" value="ຢືນຢັ້ນ" style="width: 250px; height: 40px; padding: 5px;margin-top: 40px;"></td></tr>  
	  </table>
	</form>
	</div>
	</div>
</div>	
<div>
 <?php
 // CHANGE PASSWORD - Back from Modal form
	//echo "Hello".$_POST["pswold"];
  ?>
</div>
<script>

//****** Modal form - FA creation
var modfch=document.getElementById("mfchpw");
 // Get the <span> element that closes the modal
var btncross = document.getElementsByClassName("close")[0];
var btnmod=document.getElementById("btnchpsw");
 btnmod.onclick=function(){
   modfch.style.display="block";
   document.getElementById("pswoldid").focus(); // Set focus
 }
 btncross.onclick=function(){
   modfch.style.display="none";
 }
 
</script>