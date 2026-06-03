<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
?> 
<div align="center" style="width: 100%; display: inline-block;" class="csfelment">
  <div style="width: 35%; float: left; margin-left: 20px;">
  <!-- THIS FORM IS SUBMITTED TO teachers.php -->
  <form method='post' action='content.php?tch=fupload' enctype='multipart/form-data' style="background-color: white; padding: 15px; margin-top: 20px">
     <h2>ອັບໂຫຼດ ບົດສອນ</h2>
	  <table align="left" style="width:100%; border-collapse: collapse;">
	   <tr><td align="right">ວິຊາ&nbsp;</td><td align="left"><select id="ssubid" name="ssub" style="width:400px"></select></td></tr>
	   <tr><td align="right">ປະເພດບົດ&nbsp;</td><td align="left">
			   <select id="stypeid" name="stype" style="width:400px">
			   <!--  
                 <option>--- ກະລຸນາ ເລືອກປະເພດບົດ ---</option>
				 <option value="1">ບົດສອນ/ບົດຮຽນ</option>
				 <option value="2">ບົດຝືກຫັດ/ວຽກບ້ານ</option>
				 <option value="3">ບົດທວນຄືນ</option>
                -->
			   </select>
		   </td>
	   </tr>
	   <tr><td align="right">ບົດທີ&nbsp;</td><td align="left"><select id="ssqnid" name="ssqn" style="width:400px"></select></td></tr>
	   <tr><td align="right">ຫົວຂໍ້&nbsp;</td><td align="left"><textarea rows='3' cols='47' name='topic' style='border: solid 1px #CFCFCF; border-radius: 5px'></textarea></td></tr>
		  <tr><td align="right">ຄໍາອະທິບາຍ&nbsp;</td><td><textarea rows='7' cols='47' name='description' style='border: solid 1px #CFCFCF; border-radius: 5px'></textarea></td></tr>  
	  </table><br><br> 
	  <p align="right">
	      ກະລຸນາເລືອກຊື່ຟາຍ ທີຕ້ອງການອັບໂຫຼດ&nbsp;&nbsp;<input type='file' name='filename'><br><br>
          <input TYPE='submit' name='upload' value='ອັບໂຫຼດ'/>
	  </p>
    </form>
  </div>
	<!-- *********** RIGHT SIDE ******************* -->
  <div style="width: 60%; float: left; display:inline-block; background-color:white; margin-top: 10px; margin-left: 25px">
	<h2>ບົດສອນ</h2>
	  <div class="dposition"> <!-- ****** Heading - search box ****** --->
        <form id="frsearch" action="content.php?tch=lessearch&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post">
		  <div class="sbox"><img src="../images/search.jpg" align="middle" />
   &nbsp;ຊອກຫາ:&nbsp;<input type="text" name="lessearch" id="textsearch" placeholder="ກະລຸນາພີມຫົວຂໍ້ ບົດສອນ ຫຼື ບົດຝຶກຫັດ..." onchange="subfsearch();" />&nbsp;<input type="date" name="searchdate" id="dsearch" onchange="subfsearch();" />
   </div>
		</form>
	  </div>
	<div style="width: 95%; margin: 0px 3px 3px 3px; float: left"> <!-- ***** Table ****  -->
	 <?php
	  // USER'S ID - based on username and password
		list($uid) = Userinfo($guname,$gpsw,$con);  // Teacher id
		//echo "User id: ".$uid;
	  // SEARCH FOR LESSONS *************************
		
	   if(!empty($_POST["lessearch"]) || !empty($_POST["searchdate"])){ // If1
		 $searchs="";
		 if(!empty($_POST["lessearch"])){
		   $searchs=$_POST["lessearch"];  
		   $sqlgless="SELECT * FROM tblessons WHERE topic LIKE '$searchs%' AND teacherid='$uid' ORDER BY topic DESC";
		 }
		 if(!empty($_POST["searchdate"])){
		   $searchs=$_POST["searchdate"];
		    $sqlgless="SELECT * FROM tblessons WHERE fileupdate LIKE '$searchs%' AND teacherid='$uid' ORDER BY fileupdate DESC";
		 }
		  
	   } else {
		  $sqlgless="SELECT * FROM tblessons WHERE teacherid='$uid' ORDER BY id DESC";  
	   } // End of If1
	   
	   $rgless=mysqli_query($con,'SET NAMES utf8');
	   $rgless=mysqli_query($con,$sqlgless) or die(mysqli_connect_error());
	   if(mysqli_num_rows($rgless)>0){
		  $i=0;
		 print "<table align='center' class='custb' style='margin-left: 10px'>";
		 print "<thead><tr><th style='width:7%'>ລດ</th><th style='width:25%'>ວິຊາ</th><th style='width:30%'>ຫົວຂໍ້</th><th style='width:10%'>ປະເພດບົດ</th><th style='width:8%'>ລືບອອກ</th><th align='center' style='width:8%'>ເບີ່ງ</th></tr></thead>";
		 print "<tbody>";
		 while($r=mysqli_fetch_array($rgless)){
			 $i = $i + 1;
			 $lessid=$r["id"];
			 $sbid = $r["subid"];
			 list($sid, $slao, $seng, $credit, $dgree, $sarea) = Rsubjectall($sbid, $con);
			 $dgname = Rdgree($dgree, $con);
		     $srname = Rsarea($sarea, $con);
			 /*
			   Get class based on tbteaching
			 */
			 $sqlscl = "SELECT classid FROM tbteaching WHERE userid='$uid' AND subjid='$sbid' GROUP BY classid";
			 $rcl = mysqli_query($con,$sqlscl) or die(mysqli_connect_error());
			 list($clsb) = mysqli_fetch_array($rcl);
			 $cln = Rclassname($clsb, $con);
			 
			 $subp = $slao.":     ".$cln.", ".$dgname.", ".$srname;
			 //********
			 $lstypeid = $r["ltype"];
			 $lstype = Rlessontype($lstypeid, $con);
			 $topicno = $r["lesqno"];
			 $tpic=$r["topic"];
			 $tpic = "ບົດທີ ".$topicno." :".$tpic;
			 $desc=$r["description"];
			 $fname=$r["filename"];
			 $fpath=$r["filepath"];
			 print "<tr><td align='center'>$i</td><td>$subp</td><td>$tpic</td><td style='width:10%'>$lstype</td><td align='center' style='width:10%'><a href='content.php?tch=lessexdel&lessid=$lessid&fname=$fpath' style='color: black; font-size: 13pt'><i class='fa fa-trash-o'></a></td><td align='center' style='width:10%'><a href='$fpath' target='_blank' style='color: black; font-size: 13pt'><i class='fa fa-eye'></a></td></tr>";
		 }
		print "</tbody>";
		print "</table>";
	   }
		
	  ?>
	</div> <!-- **** End of Table ***** -->	  
  </div>
</div>

<?php 
 // GET USER'S ID for this teacher **********************
   $sqltchid = "SELECT id FROM tbusers WHERE username='".$guname."' AND passw='".$gpsw."' AND usertype='1'";  // usertype = 1 - teacher
   $rtchid = mysqli_query($con, $sqltchid) or die(mysqli_connect_error());
   list($uid) = mysqli_fetch_array($rtchid);
     // echo "User id: ".$uid;

 // SUBJECT SELECT from tbteaching *********************
     
echo "<script>
	  var ssub=document.getElementById('ssubid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      ssub.options.add(opt_non);
	  </script>";
 $con->set_charset("utf8");
 $sqlsub = "SELECT subjid, classid FROM tbteaching WHERE userid='$uid' GROUP BY subjid";  // subject from tbteaching
 $rsub = mysqli_query($con,$sqlsub) or die(mysqli_connect_error());
 while($rw=mysqli_fetch_array($rsub)){
	$subid=$rw["subjid"];
	$clid = $rw["classid"];
	//list($subnamelao, $subnameeng) = Rsubjectname($subid, $con);
	list($sid, $slao, $seng, $credit, $dgree, $sarea) = Rsubjectall($subid, $con);
	$clname = Rclassname($clid, $con);
	$sareaname = Rsarea($sarea, $con);
	$subnamelao = $slao." - "."ຂະແໜງ ".$sareaname." - ".$clname; 
  // Province select 
	echo "<script>
		  var subid='$subid';
		  var subname='$subnamelao';	     
		  var opt=document.createElement('option');
			       opt.value=subid;
			       opt.text=subname;
			   ssub.options.add(opt);
              </script>";
 }	
// LESSON TYPE ***************************************** 
 echo "<script>
         var lstype=document.getElementById('stypeid');
	     var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      lstype.options.add(opt_non);
	   </script>";

  $sqllst = "SELECT * FROM tblessontype";
  $rlst = mysqli_query($con, $sqllst) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rlst)){
	$lsid = $r["id"];
	$lsname = $r["ltype"];
	 echo "<script>
	         var opt_non=document.createElement('option');
			 opt_non.value = '$lsid';
			 opt_non.text = '$lsname';
			 lstype.options.add(opt_non);
		   </script>";
  }
 // LESSON NUMBER SELECT: id=ssqnid *********************
echo "<script>
	  var ssqn=document.getElementById('ssqnid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      ssqn.options.add(opt_non);
	  </script>";
$sn=1;
do{
//$snv="ບົດທີ".$sn;
echo "<script>
		  var sqnv='$sn';
		  var sqname='$sn';	     
		  var opt=document.createElement('option');
			       opt.value=sqnv;
			       opt.text=sqname;
			   ssqn.options.add(opt);
              </script>";
 $sn++;
} while($sn<=20);
?>
<script>
 function subfsearch(){
   alert("Hello, searching");
  $("#frsearch").submit();
 }
</script>