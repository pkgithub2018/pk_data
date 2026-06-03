<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
 
 // Internal functions
// 1. Tchsub : Return teaching subjects
 function Tchsub($uid, $con){ 
   $sqltch = "SELECT subjid FROM tbteaching WHERE userid='$uid' GROUP BY subjid";
			   $rtch = mysqli_query($con, $sqltch) or die(mysqli_connect_error());
			   while($r=mysqli_fetch_array($rtch)){
				   $sb = $r["subjid"];
				   list($sbname) = Rsubjectname($sb, $con);
				  // Get degree and study area
				   $sqldgs = "SELECT dgree, sarea FROM tbsubjects WHERE id='$sb' GROUP BY dgree";
				   $rdg = mysqli_query($con, $sqldgs) or die(mysqli_connect_error());
				   while($rd=mysqli_fetch_array($rdg)){
					 $dg = $rd["dgree"];
					 $sar = $rd["sarea"];
					 $dgname = Rdgree($dg, $con);
		             $saname = Rsarea($sar, $con);
				     echo "<b>$sbname</b>"." ".$saname."(".$dgname."), ";
				   }
			   } 
   }

// Teacher's information
list($id,$uname,$psw,$namel,$namee,$snamel,$snammee,$bdate,$gender,$phone,$email,$addr,$utype,$status,$rgdate,$lupdate) = Userinfo($guname,$gpsw,$con);

// Teacher's class base
$sqltbase = "SELECT tclbase, tposition, imfilepath FROM tbcteachers WHERE userid='$id'"; 
$rtb = mysqli_query($con, $sqltbase) or die(mysqli_connect_error());
list($clb, $tps, $imfile) = mysqli_fetch_array($rtb);
$classb = Rclassname($clb, $con);
$psname = Rposition($tps, $con);
// Degree and study area based on class base
$sqldsb = "SELECT degree, studyarea FROM tbclass WHERE id='$clb'";
$rds = mysqli_query($con, $sqldsb) or die(mysqli_connect_error());
list($bdgree, $bstarea) = mysqli_fetch_array($rds);
$bdgname = Rdgree($bdgree, $con);
$bsaname = Rsarea($bstarea, $con);

$tbase = $bsaname."(".$bdgname.")";

// CHANGE PASSWORD *****************
$pwn = $_POST["pswn"];
if(!empty($pwn)){
  echo "PWD: ".$pwn;
}

?> 
	<div align="center" class="cominpt" style="width: 100%; display: inline-block; float: left; margin-left: 50px;"> <!-- Main div -->
	   <div style="width: 25%; float: left; text-align: right; margin-top: 25px">
		 <img src="<?php echo $imfile; ?>" />
		</div>
		<!-- Div content -->
		<div style="width: 65%; float: left; margin-left: 15px;">
		  <h2 align="left">ສະບາຍດີ, ອຈ.&nbsp;<?php echo $namel; ?>&nbsp;<?php echo $snamel; ?></h2>
		  <p align="left">
			
		  ຊື່: <?php echo "<b>".$namel."</b>"; ?><br>
		  ນາມສະກຸນ: <?php echo "<b>".$snamel."</b>"; ?> <br> 
		  ວດປ ເກີດ: <?php $bd=date_create($bdate); echo date_format($bd,"d-m-Y"); ?> <br>
		  ສອນວິຊາ, ຂະແໜງ ແລະ ຂັ້ນ: <?php Tchsub($id, $con); ?><br>
		  ຄູປະຈໍາຫ້ອງ: <?php echo $classb.",".$tbase; ?><br>
		  ຕໍາແໜ່ງ: <?php echo $psname; ?><br>
		</p>
		<hr align="left" style="width: 50%">
		<p align="left">
		 ຊື່ຜູ້ໃຊ້: <?php echo $uname; ?><br> 
		 ລະຫັດຜ່ານ: <?php echo $psw; ?><br><br>
		 <button id="btnchpsw" class="cusbtn">ປ່ຽນລະຫັດຜ່ານ</button>
		 </p>
		</div>		
	</div>  <!-- End of main div -->
	