<script>
 // Remove items from select: STUDY AREA when user clicks on select DEGREE
 var arselect = document.getElementById("sstareaid");
 if(arselect.childNodes.length>0){ 
	arselect.innerHTML="";	// Remove items from SELECT
 }
// Remove items from select CLASS when user clicks on select DEGREE
 var sselect = document.getElementById("sclassid");
 if(sselect.childNodes.length>0){ 
	sselect.innerHTML="";	// Remove items from SELECT
 }
</script>
<?php 
include("connection.php");
?>


<div align="center" style="" class="usform">  <!-- *** MAIN DIV *** -->
  <div align="left" style="margin-left: 60px; width: 70%;">
	  <h2 align="left">ຂະແໜງ/ວິຊາຮຽນ</h2>
	  <p align="left">ທ່ານ ສາມາດ ຊອກຫາຂໍ້ມູນ ຂະແໜງ ຫຼື ວິຊາຮຽນ ລົງໃນຫ້ອງຂ້າງລຸ່ມນີ້</p>
	 
	  <table style="width: 90%;">
	   
	   <tr>
		<td style="width: 70%"><form id="fsearchstu" action="content.php?sad=stusearch&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post"><i class='fa fa-search' style='font-size:20px; margin-left: 10px'></i>&nbsp;<input type="text" name="txtstusearch" id="txtstusearchid" placeholder="&nbsp;ກະລຸນາ ພີມຊື່ ຂະແໜງ ຫຼື ວິຊາຮຽນ ທີຕ້ອງການຊອກ" style="width: 75%; color: #bbbbbb" onChange="Subfsearchstu();"></form>	  
	   </tr>
	 
		 <td>&nbsp;</td>
	   </tr>
	  </table> 
  </div>
  <div align="left" class="dsubj"> 
   <!-- ****************Div - Left side ******************** -->
   <div style="width: 70%; float: left; margin-left: 200px;">
	  <form action="content.php?sad=studyarea" method="post">
	   <table>
		  <tr>
		    <td>ຂະແໜງ/ວິຊາຮຽນ</td><td><input type="text" name="staname" id="staid" style="width: 300px;padding: 3px;margin: 3px;" /></td>
			<td><input type="submit" name="btnsarea" value="ເພີ່ມ" style="width:70px; height: 40px; padding: 3px;margin: 3px;"></td>
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
 
// 1. ADD STUDY AREA - Put this action
 if(isset($_POST["btnsarea"]) && !empty($_POST["staname"])){
	
	$sqlch = "SELECT sareaname FROM tbstudyarea WHERE sareaname='".$_POST["staname"]."'";
	$rch = mysqli_query($con,$sqlch) or die(mysqli_connect_error());
	list($nsarea) = mysqli_fetch_array($rch);
	if(empty($nsarea)){ // No such name
	  $sqladd = "INSERT INTO tbstudyarea(sareaname) VALUES('".$_POST["staname"]."')";	
	  mysqli_query($con, $sqladd) or die(mysqli_connect_error());
	} else {
		echo "Already exists";  // Warning message
	}
	 
	
 }	

// 2. DELETE STUDY AREA from link in support.php
 
 if(!empty($_GET["delsar"])){
   // check study area in tbclass, tbsubjects and tbteachers
   $sqlsacl = "SELECT studyarea FROM tbclass WHERE studyarea='".$_GET["delsar"]."'";
   $rscl = mysqli_query($con, $sqlsacl) or die(mysqli_connect_error());
   list($sacl) = mysqli_fetch_array($rscl);
	 
   $sqlsasub = "SELECT sarea FROM tbsubjects WHERE sarea='".$_GET["delsar"]."'";
   $rssub = mysqli_query($con, $sqlsasub) or die(mysqli_connect_error());
   list($sasub) = mysqli_fetch_array($rssub);
	 
   $sqlsatch = "SELECT tarea FROM tbcteachers WHERE tarea='".$_GET["delsar"]."'";
   $rstch = mysqli_query($con, $sqlsatch) or die(mysqli_connect_error());
   list($satch) = mysqli_fetch_array($rstch);
	 // IF STUDY AREA IS NOT FOUND in any related data table - it can be DELETED
   if(empty($sacl) && empty($sasub) && empty($satch)){  
	$sqldelsa = "DELETE FROM tbstudyarea WHERE id='".$_GET["delsar"]."'"; 
	mysqli_query($con,$sqldelsa) or die(mysqli_connect_error());
   } else {
	  
	  echo "It is used in another";  // DEVELOP WARNING MESSAGE
   }
 }
	   
// 3. UPDATE STUDY AREA
 
 if(!empty($_GET["updsar"])){  // $_POST["staname"] - INPUT
	$sqlsaname = "SELECT sareaname FROM tbstudyarea WHERE id='".$_GET["updsar"]."'";
	$rsname = mysqli_query($con, $sqlsaname) or die(mysqli_connect_error());
	list($saname) = mysqli_fetch_array($rsname); // Used in Javascript
	
 }
// Modal form for updat is submitted
$subup = "";  // Keep value of submission
if(isset($_POST["btnssa"])){
  $hsaid = $_POST["hsaid"]; // Hidden input 
  $sanew = $_POST["saname"];
	
  $sqlsanew = "SELECT sareaname FROM tbstudyarea WHERE sareaname='".$sanew."'";
	$rsnew = mysqli_query($con, $sqlsanew) or die(mysqli_connect_error());
	list($nsame) = mysqli_fetch_array($rsnew); // Used in Javascript
	if(empty($nsame)){ // if not the same
	  $sqlup = "UPDATE tbstudyarea SET sareaname='".$sanew."' WHERE id='$hsaid'";
	  mysqli_query($con,$sqlup) or die(mysqli_connect_error());	
	} else {
	  $subup = "yes";	// The same - no update
	}
  
}

// 4. SHOW LIST OF study area ********************
	
 Showlistsarea($con);
	   
?>
	   
  </div> <!-- End of DIV left side -->
  <!-- ****************Div - Right side******************** -->
  <div style="width: 15%; float: left; margin-left: 10px; margin-top: 15px;">
   <p>&nbsp;</p>
  </div> <!-- End of DIV right side -->
</div> <!-- **** End of DIV - class="adpstud"******* --> 
</div> <!-- ************************* End of MAIN DIV ***************************** -->
<!-- ************ MODAL FORM 1 - Update on study area *************** -->
<div id="samod" class="modal">
 <!-- Modal content -->
  <div class="modal-content" style="height:45%;">
    <span class="close">&times;&nbsp;</span>
    <div class="modal-heading">
      <div align="center" class="msheading"><span id="mdheadid">ປ່ຽນຊື່ ຂະແໜງ/ວິຊາຮຽນ</span></div>
    </div>
	  <p align="center">ຊື່ ຂະແໜງ/ວິຊາຮຽນໃໝ່ ຕ້ອງແຕກຕ່າງ (ບໍ່ຄືກັນ)ກັບຊື່ທີ່ມີແລ້ວ (ຊື່ເກົ່າ)</p>
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
<!-- End of Modal form 1 -->
<!-- MODAL FORM - Message ****************************************  -->
<div id="modmess" class="mdmess">
 <!-- Modal content -->
  <div class="mdmess-content" style="height:25%;">
    <span class="closemsg">&times;&nbsp;</span>
    <div class="mdmess-heading">
      <div align="center" class="msheading">ແຈ້ງເຕືອນ</div>
    </div>
	  <div style="display: inline-block;margin-left: 60px; margin-top: 40px">
		  <div style="float: left; vertical-align: middle"><i class="fa-solid fa-triangle-exclamation" style="font-size: 30pt; color: #F6BE00"></i></div>
		  <div style="float: left; vertical-align: middle; margin-top: 10px; font-size: 14pt">ຊື່ ຂະແໜງ/ວິຊາຮຽນດັ່ງກ່າວມີແລ້ວ. ກະລຸນາພີມຊື່ໃໝ່</div>
	  </div>
  </div>
</div>
<!-- End of Modal form - message -->
<div id="dresult">&nbsp;</div> <!-- **** Receive result - Just make it happy -->

<script>
// Modal form for update
  var upsa = "<?php $sa = $_GET["updsar"]; echo $sa;  ?>";
  var samodf = document.getElementById("samod");
  var btncls = document.getElementsByClassName("close")[0];
  var sainp = document.getElementById("said");
	
  if(upsa.length>0){
	  var sname = "<?php echo $saname; ?>";
	  samodf.style.display = "block";  // Open modal form for update study area
	  sainp.value = sname;
	  sainp.style.backgroundColor = "lightblue";
	  sainp.focus();
	  
  }
	
 btncls.onclick = function(){
	  samodf.style.display = "none";
	}

 // Modal form for warning
 var cfup = "<?php echo $subup; ?>";
  // alert("Hello, " + cfup);
 var wmmodf = document.getElementById("modmess");
 var btnclms = document.getElementsByClassName("closemsg")[0];
	
 if(cfup.length>0){
	 wmmodf.style.display = "block";
	}
 btnclms.onclick = function(){
	wmmodf.style.display = "none"; 
 }	
</script>
