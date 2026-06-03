<?php
 // session_start(); // Already started in content.php
 $con->set_charset("utf8");
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
 $utype = $_SESSION["usertype"];
 $uid = $_SESSION["uid"]; 
 list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr,$utype, $status) = Userbyid($uid, $con);
 $respon = "";
 switch($utype){
	case "2": //  system admin
		$respon = "ຜູ້ຄວບຄຸມລະບົບ";
		break;
	case "5": //  Admin staff
		$respon = "ພ/ງ ບໍລິຫານ";
		break;
	case "6": //  Admin staff - high school
		$respon = "ພ/ງ ບໍລິຫານ-ສາມັນ";
		break;
	case "7": //  admin staff - kindergarten
		$respon = "ພ/ງ ບໍລິຫານ-ອະນຸບານ";
		break;
 }
?> 
	<div align="center" class="cominpt"> <!-- set utf-8 in content.php -->
		<h2 align="left" style="margin-left: 30%">ສະບາຍດີ,&nbsp;<?php echo $namel; ?>&nbsp;<?php echo $sname; ?></h2>
		<p align="left" style="margin-left: 30%">
		  ຊື່: <?php 
			//echo $uname;
			echo mb_convert_encoding($namel, "UTF-8");
			?><br>
		  
		  ນາມສະກຸນ: <?php echo $sname; ?> <br> 
		  ວດປ ເກີດ: <?php echo date('d/m/Y', strtotime($bdate)); ?> <br>
		  ຕໍາແໜ່ງ ແລະ ໜ້າທີ່ຮັບຜິດຊອບ: <?php echo  $respon; ?><br>
		</p>
		<hr style="width: 50%">
		<p align="left" style="margin-left: 30%">
		 ຊື່ຜູ້ໃຊ້: <?php echo $un; ?><br> 
		 ລະຫັດຜ່ານ: <?php echo $pw; ?><br><br>
		 <button id="btnchpsw" class="cusbtn">ປ່ຽນລະຫັດຜ່ານ</button>
		</p>
	</div>
	