<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
?> 

<div align="center" style="" class="usform">  <!-- *** MAIN DIV *** -->
  <div align="left" style="margin-left: 60px; width: 70%;">
	  <h2 align="left">ຫ້ອງຮຽນ</h2>
	  <p align="left">ທ່ານ ສາມາດ ຊອກຫາຂໍ້ມູນ ຫ້ອງຮຽນດ້ວຍການພີມຊື່ຫ້ອງຮຽນ ລົງໃນຫ້ອງຂ້າງລຸ່ມນີ້</p>
	 
	  <table style="width: 90%;">
	   
	   <tr>
		<td style="width: 70%"><form id="fsearchstu" action="content.php?sad=stusearch&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post"><i class='fa fa-search' style='font-size:20px; margin-left: 10px'></i>&nbsp;<input type="text" name="txtstusearch" id="txtstusearchid" placeholder="&nbsp;ກະລຸນາ ພີມຊື່ ຂອງ ຫ້ອງຮຽນ ທີຕ້ອງການຊອກ" style="width: 75%; color: #bbbbbb" onChange="Subfsearchstu();"></form>	  
	   </tr>
	 
		 <td>&nbsp;</td>
	   </tr>
	  </table> 
  </div>
  <div align="left" class="dsubj"> 
   <!-- ****************Div - Left side ******************** -->
   <div style="width: 70%; float: left; margin-left: 200px;">
	  <form action="content.php?sad=classadd" method="post">
	   <table>
		  <tr>
		    <td>ຊື່ຫ້ອງຮຽນ</td><td><input type="text" name="clname" id="clid" style="width: 300px;padding: 3px;margin: 3px;" /></td><td>(ຂື້ນທະບຽນແຕ່ຫ້ອງນ້ອຍຫາຫ້ອງໃຫຍ່. ຕົວຢ່າງ: ປີ1, ປີ2 ແລະ ປີ3 ຕາມລໍາດັບ)</td> 
		  </tr>
		  <tr>
		    <td>ຂັ້ນ/ຊັ້ນ</td><td><select name="cldgree" id="cldgreeid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td> 
		  </tr>
		  <tr>
		    <td>ຂະແໜງ/ວິຊາ</td><td><select name="clarea" id="clareaid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td> 
		  </tr>
		  <tr>
		    <td>ທີ່ຕັ້ງ</td><td><select name="cllocate" id="cllocateid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td><td><input type="submit" name="btncl" value="ເພີ່ມ" style="width:70px; height: 40px; padding: 3px;margin: 3px;"></td> 
		  </tr>
	  </table>
	  </form>   
    <?php
// ADD ITEMS INTO Select: degree for class
	   echo "<script> 
	   var cldg = document.getElementById('cldgreeid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      cldg.options.add(opt_non);
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
	      cldg.options.add(opt_non);
	      </script>";
  }
 // ADD ITEMS INTO Select: Study area for class
	   echo "<script> 
	   var clarea = document.getElementById('clareaid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      clarea.options.add(opt_non);
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
	      clarea.options.add(opt_non);
	      </script>";
  }
// LOCATION OF CLASS
	   echo "<script> 
	   var cllocat = document.getElementById('cllocateid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      cllocat.options.add(opt_non);
	    </script>";
$con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqllocat = "SELECT * FROM tblocation";
  $rlocat = mysqli_query($con,$sqllocat) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rlocat)){ // 1
	$lid = $r["id"];
	$lname = $r["locationname"];
	echo "<script>
	      var lid ='$lid';
		  var lname = '$lname';
		  var opt_non=document.createElement('option');
		  opt_non.value=lid;
		  opt_non.text=lname;
	      cllocat.options.add(opt_non);
	      </script>";
  }
	   
// SHOW LIST OF CLASS ********************
	Showclasslist($con);

// ADD/INSERT CLASS - Form submission  
	  if(isset($_POST["btncl"])){ // button - on click/submission 
		$chemp = ""; // Check empty input
		$chin = ""; // Check insert
		if(!empty($_POST["clname"]) && !empty($_POST["cldgree"]) && !empty($_POST["clarea"]) && !empty($_POST["cllocate"])){
		  // Subj - INSERT SUBJECT AND CHECK
		  $chin = Classin($_POST["clname"],$_POST["cldgree"],$_POST["clarea"],$_POST["cllocate"],$con); //Add data in
		 
		  if($chin=="exists"){  // already exists
			echo "Hello".$chin;  
		  }
		} else {
	      echo "Some of empty input";
		  $chemp="empty";
		}
	  } // End of if btnsubj submission
	   
 // DELETE CLASS *****************************
	 // sadcl=cldel&clid=$cln - LINKS FROM Showclasslst function
	   
	if(!empty($_GET["sadcl"] ?? '') && ($_GET["sadcl"] ?? '')=="cldel"){
		$clid = $_GET["clid"] ?? ''; // Class id
		Delclass($clid, $con);	   
	}   
	   
    ?>
	   
  </div> <!-- End of DIV left side -->
  <!-- ****************Div - Right side******************** -->
  <div style="width: 15%; float: left; margin-left: 10px; margin-top: 15px;">
   <p>&nbsp;</p>
  </div> <!-- End of DIV right side -->
</div> <!-- **** End of DIV - class="adpstud"******* --> 
</div> <!-- End of MAIN DIV -->

<!-- MODAL FORM - UPDATE ON Class ****************** -->
<div id="clmod" class="modal">
 <!-- Modal content -->
  <div class="modal-content" style="height:45%;">
    <span class="close">&times;&nbsp;</span>
    <div class="modal-heading">
      <div align="center" class="msheading"><span id="mdheadid">ປ່ຽນຊື່ ຫ້ອງຮຽນ</span></div>
    </div>
	  <p align="center">ຊື່ ຫ້ອງຮຽນຕ້ອງປ້ອນເຂົ້າຕາມລໍາດັບແຕ່ຫ້ອງນ້ອຍຫາໃຫຍ່</p>
    <div style="margin-top:2px" class="usform"> <!-- DIV form -->
    <form id="fmodalfup" action="content.php?sad=studyarea" method="post">
	  <input type="hidden" name="hmfuname" value="<?php echo $lginname; ?>" >
	  <input type="hidden" name="hmfpsw" value="<?php echo $lginpsw; ?>" >
	   <input type="hidden" name="hsaid" value="<?php echo $_GET["updsar"]; ?>" >
	    <table align="left" class="modal-table"> <!-- top right buttom left -->
          <tr>
           <td align="right">ຊື່ ຂະແໜງ/ວິຊາຮຽນ&nbsp;</td><td><input type="text" name="saname" id="said" style="width: 300px;padding: 3px;margin: 3px;" /></td>
          </tr>
		  <tr>
			<td>&nbsp;</td><td align="right"><input type="submit" name="btnssa" id="btnssaid" value="ບັນທຶກ" style="width: 35%" /></td>
		  </tr>
		</table>
	</form>
	</div> <!-- End of DIV form -->
  </div> <!-- End of DIV - content -->
</div>

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
 // Set focus on class name as started
  document.getElementById("clid").focus();
});	 // End of ready document
  var chinsert = "<?php $ch = $chin; echo $ch;?>";
   if(chinsert=="exists"){
     alert("This class already exists !");
   }
</script>