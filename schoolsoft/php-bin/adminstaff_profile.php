   <?php
      list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr) = Userinfo($un,$pw,$con);
      list($sid,$slevel,$starea,$sclass,$sayear) = Currentstudent($id,$con);
   ?>
    <div align="center" class="cominpt">
		<h2 align="left" style="margin-left: 30%">ສະບາຍດີ,&nbsp;<?php echo $namel; ?>&nbsp;<?php echo $snamel; ?></h2>
		<p align="left" style="margin-left: 30%">
		  ຊື່: <?php echo $namel; ?><br>
		  ນາມສະກຸນ: <?php echo $snamel; ?> <br> 
		  ວດປ ເກີດ: <?php $ndate = date("d/m/Y", strtotime($bdate)); echo $ndate; ?> <br>
		  
		  ຕໍາແໜ່ງ: <?php ?><br>
		</p>
		<hr style="width: 50%">
		<p align="left" style="margin-left: 30%">
		 ຊື່ຜູ້ໃຊ້: <?php echo $un; ?><br> 
		 ລະຫັດຜ່ານ: <?php echo $pw; ?><br><br>
		 <button id="btnchpsw" class="cusbtn">ປ່ຽນລະຫັດຜ່ານ</button>
		</p>
	</div>
