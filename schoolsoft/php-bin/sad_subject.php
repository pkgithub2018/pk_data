<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
?> 

<div align="center" style="" class="usform">  <!-- *** MAIN DIV *** -->
  <div align="left" style="margin-left: 60px; width: 70%;">
	  <h2 align="left">ວິຊາຮຽນ</h2>
	  <p align="left">ທ່ານ ສາມາດ ຊອກຫາຂໍ້ມູນ ວິຊາຮຽນດ້ວຍການພີມຊື່ ວິຊາຮຽນ ລົງໃນຫ້ອງຂ້າງລຸ່ມນີ້</p>
	 
	  <table style="width: 90%;">
	   
	   <tr>
		<td style="width: 70%"><form id="fsearchstu" action="content.php?sad=stusearch&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post"><i class='fa fa-search' style='font-size:20px; margin-left: 10px'></i>&nbsp;<input type="text" name="txtstusearch" id="txtstusearchid" placeholder="&nbsp;ກະລຸນາ ພີມຊື່ ຂອງ ວິຊາຮຽນ ທີຕ້ອງການຊອກ" style="width: 75%; color: #bbbbbb" onChange="Subfsearchstu();"></form>	  
	   </tr>
	 
		 <td>&nbsp;</td>
	   </tr>
	  </table> 
  </div>
  <div align="left" class="dsubj"> 
   <!-- ****************Div - Left side ******************** -->
   <div style="width: 70%; float: left; margin-left: 200px;">
	  <form action="content.php?sad=subadd" method="post">
	   <table>
		  <tr>
		    <td>ວິຊາຮຽນ(ລາວ)</td><td><input type="text" name="subnamel" id="subnamel" style="width: 300px;padding: 3px;margin: 3px;" /></td> 
		  </tr>
		  <tr>
		    <td>ວິຊາຮຽນ(ອັງກິດ)</td><td><input type="text" name="subnamee" id="subnamee" style="width: 300px;padding: 3px;margin: 3px;" /></td> 
		  </tr>
		  <tr>
		    <td>ຈ/ນ ໜ່ວຍກິດ</td><td align="center"><input type="text" name="ncredit" id="ncreditid" style="width: 300px;padding: 3px;margin: 3px;" /></td> 
		  </tr>
		  <tr>
		    <td>ຂັ້ນ/ຊັ້ນ</td><td><select name="subdgree" id="subdgreeid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td> 
		  </tr>
		  <tr>
		    <td>ຂະແໜງ/ວິຊາ</td><td><select name="subarea" id="subareaid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td><td><input type="submit" name="btnsubj" value="ເພີ່ມ" style="width:70px; height: 40px; padding: 3px;margin: 3px;"></td> 
		  </tr>
	  </table>
	  </form>   
    <?php
// ADD ITEMS INTO Select: subdegree
	   echo "<script> 
	   var subdg = document.getElementById('subdgreeid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      subdg.options.add(opt_non);
	    </script>";
$con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsdg = "SELECT * FROM tbdegree";
  $rsdg = mysqli_query($con,$sqlsdg) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rsdg)){ // 1
	$did = $r["id"];
	$dname = $r["degreename"];
	echo "<script>
	      var did ='$did';
		  var dname = '$dname';
		  var opt_non=document.createElement('option');
		  opt_non.value=did;
		  opt_non.text=dname;
	      subdg.options.add(opt_non);
	      </script>";
  }
 // ADD ITEMS INTO Select: Study area
	   echo "<script> 
	   var subarea = document.getElementById('subareaid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      subarea.options.add(opt_non);
	    </script>";
$con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsarea = "SELECT * FROM tbstudyarea";
  $rsarea = mysqli_query($con,$sqlsarea) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rsarea)){ // 1
	$srid = $r["id"];
	$srname = $r["sareaname"];
	echo "<script>
	      var srid ='$srid';
		  var srname = '$srname';
		  var opt_non=document.createElement('option');
		  opt_non.value=srid;
		  opt_non.text=srname;
	      subarea.options.add(opt_non);
	      </script>";
  }
	   
// SHOW LIST OF SUBJECTS ********************
	   Showsublist($con);
	  // Form submission - INSERT SUBJECT
	  if(isset($_POST["btnsubj"])){ // button - on click/submission
		//echo "Submission".$_POST["subnamel"]; 
		$chemp = ""; // Check empty input
		$chin = ""; // Check insert
		if(!empty($_POST["subnamel"]) && !empty($_POST["subnamee"]) && !empty($_POST["ncredit"]) && !empty($_POST["subdgree"]) && !empty($_POST["subarea"])){
		  // Subj - INSERT SUBJECT AND CHECK
		  $chin = Subj($_POST["subnamel"],$_POST["subnamee"],$_POST["ncredit"],$_POST["subdgree"], $_POST["subarea"],$con); //Add data in
		  if($chin=="exists"){  // already exists
			echo "Hello".$chin;  
		  }
		} else {
	      echo "Some of empty input";
		  $chemp="empty";
		}
	  } // End of if btnsubj submission
	 // UPDATE SUBJECTS ***************************
	  if(!empty($_GET["subsadup"]) && $_GET["subsadup"]=="subjdelete"){  // DELETE SUBJECT 
		// The subject can be deleted if it is not tought OR found in TABLE: tbteaching
		 $subjid = $_GET["subid"];
		 Delsubj($subjid, $con); // DELETE SUBJECT
		 // Refresh page
         echo "<script type='text/javascript'>window.location.href = 'content.php?sad=subject';</script>";
	     exit();
	  } // End of if
    ?>
	   
  </div> <!-- End of DIV left side -->
  <!-- ****************Div - Right side******************** -->
  <div style="width: 15%; float: left; margin-left: 10px; margin-top: 15px;">
   <p>&nbsp;</p>
  </div> <!-- End of DIV right side -->
</div> <!-- **** End of DIV - class="adpstud"******* --> 
</div> <!-- End of MAIN DIV -->
<!-- ************ MODAL FORM - Update on new student or accademic year : move to another class *************** -->
<div id="studmod" class="modal">
 <!-- Modal content -->
  <div class="modal-content" style="height:85%;">
    <span class="close">&times;&nbsp;</span>
    <div class="modal-heading">
      <div align="center" class="msheading"><span id="mdheadid">ຕື່ມຂໍ້ມູນນັກຮຽນ/ນັກສຶກສາໃໝ່</span></div>
    </div>
    <div style="margin-top:10px" class="usform"> <!-- DIV form -->
    <form id="fmodalfup" action="content.php?sad=stuup" method="post">
	  <input type="hidden" name="hmfuname" value="<?php echo $lginname; ?>" >
	  <input type="hidden" name="hmfpsw" value="<?php echo $lginpsw; ?>" >
	    <table align="left" class="modal-table"> <!-- top right buttom left -->
          <tr>
           <td align="right">ຊື່ ແລະ ນາມສະກຸນ&nbsp;</td><td><input type="text" name="stname" id="stid" style="width: 300px;padding: 3px;margin: 3px;" /></td>
          </tr>
		  <tr>
			<td align="right">ວດປ ເກີດ&nbsp;</td><td><input type="text" name="bdate" id="bdateid" style="width: 300px;padding: 3px;margin: 3px;" /></td>
		  </tr>
		  <tr>
			<td align="right">ໂທລະສັບ&nbsp;</td><td><input type="text" name="phone" id="phoneid" style="width: 300px;padding: 3px;margin: 3px;" /></td>
		  </tr>
		  <tr>
			<td align="right">ອີແມ໋ວ&nbsp;</td><td><input type="text" name="email" id="emailid" style="width: 300px;padding: 3px;margin: 3px;" /></td>
		  </tr>
		  <tr>
			<td align="right">ຂັ້ນ/ຊັ້ນຮຽນ&nbsp;</td><td><select name="sdegree" id="sdegreeid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		   <tr>
			<td align="right">ວິຊາຮຽນ&nbsp;</td><td><select name="sstarea" id="sstareaid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right">ຫ້ອງຮຽນ&nbsp;</td><td><select name="sclass" id="sclassid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
		  <td>&nbsp;<input type="hidden" name="huser" id="huserid" value="<?php echo $userid; ?>" ><input type="hidden" name="hayear" value="<?php echo $ayear; ?>"></td> <!-- HIDDEN INPUT -->
		  </tr>
		  <tr>
		  <td>&nbsp;</td><td align="right"><input type="button" id="btnexit" value="ປິດ/ອອກ" style="width: 35%" />&nbsp;<input type="submit" name="btsubmof" id="btsubmofid" value="ບັນທຶກ" style="width: 35%" /></td>
		  </tr>
		</table>
	</form>
	</div> <!-- End of DIV form -->
  </div> <!-- End of DIV - content -->
</div>
<!-- End of Modal form -->

<!-- MODAL FORM - Message -->
<div id="modmess" class="mdmess">
 <!-- Modal content -->
  <div class="mdmess-content" style="height:25%;">
    <span class="closemsg">&times;&nbsp;</span>
    <div class="mdmess-heading">
      <div align="center" class="msheading">ແຈ້ງເຕືອນ</div>
    </div>
	  <div style="display: inline-block;margin-left: 60px; margin-top: 40px">
		  <div style="float: left; vertical-align: middle"><i class="fa-solid fa-triangle-exclamation" style="font-size: 30pt; color: #F6BE00"></i></div>
		  <div style="float: left; vertical-align: middle; margin-top: 10px; font-size: 14pt">ຂໍ້ມູນບໍ່ຄົບຖ້ວນ. ກະລຸນາ ຕື່ມເຂົ້າໃໝ່</div>
	  </div>
  </div>
</div>
<!-- End of Modal form - message -->
<div id="dresult">&nbsp;</div> <!-- **** Receive result - Just make it happy -->

<script>	
$(document).ready(function () {
 // Set focus on subject name as started
  document.getElementById("subnamel").focus();
});	 // End of ready document
  var chinsert = "<?php $ch = $chin; echo $ch;?>";
   if(chinsert=="exists"){
     alert("This subject already exists !");
   }
</script>