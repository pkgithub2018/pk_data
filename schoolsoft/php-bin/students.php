<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
 $studid = $_SESSION["uid"];

?>
<!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	<a id="sttableid" href="content.php?stud=stable">ຕາຕະລາງຮຽນ</a>
	<a id="lssonid" href="content.php?stud=lesson">ບົດຮຽນ</a>
	<a id="excid" href="content.php?stud=exercise">ບົດເຝືກຫັດ ແລະ ບົດທວນຄືນ</a>
	<a id="gradeid" href="content.php?stud=grade">ຜົນການສອບເສັງ/ຄະແນນ</a>  <!-- class="active" -->
	<a id="absenceid" href="content.php?stud=sabsence">ການຂາດຮຽນ</a>
	<a id="tuifid" href="currlum.php?access=curr">ຄ່າຮຽນ</a> 
	<a id="sprofileid" href="content.php?stud=begin"><i class="fa fa-fw fa-user"></i>&nbsp;<?php echo $un; ?></a>  <!-- $un from content.php -->
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
	  $stulink = $_GET["stud"];
	  if(empty($stulink)){
		$stulink = 'begin';  
	  }
	
	  switch($stulink){
		  case "begin":
			  echo "<script>
				    var stable = document.getElementById('sprofileid');
					    stable.classList.add('activealink');
					</script>";
			  include("students_profile.php");
			  break;
			  
		  case "stable":
			echo "<script>
				    var stable = document.getElementById('sttableid');
					    stable.classList.add('activealink');
					</script>";
			 echo "Study table";
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
				    var abs = document.getElementById('absenceid');
					    abs.classList.add('activealink');
					</script>";
			include("students_absence.php");
			break; 	

		case "grade":
		   echo "<script>
				    var grd = document.getElementById('gradeid');
					    grd.classList.add('activealink');
					</script>";
			include("students_grades.php");
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