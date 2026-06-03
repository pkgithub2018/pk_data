  <style>
	  .lhead{
		 margin-left: 50px; 
		 vertical-align: middle; 
	  }
	  .lhead input[type=date], select {
		  height: 45px;
		  width: 300px; 
	  }
	  .custb, table{
		  margin-left: 50px; 
	  }
	  .custb, th{
		  align-content: center;
	  }
	  .abtb{
		border: solid 1px black;  
	  }
	  .abtb, td{ 
		font-weight: normal;  
	  }
	  .atbor{
		  border-left: solid 1px #73c2fb;
	  }
	  .tmg{
		  border-top: solid 1px #73c2fb;
		  border-left: solid 1px #73c2fb;
	  }
  </style>

  <?php
      list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr) = Userinfo($un,$pw,$con); // Teacher
      list($sid,$slevel,$starea,$sclass,$sayear) = Currentstudent($id,$con);
      
   ?>
    <div align="center" class="cominpt" style="width: 100%; float: left; display: inline-block">
		<div align="left" class="lhead">
			<h2 align="left" class="lhead">ການຂາດຮຽນ</h2>
			<form id="frabid" action="content.php?stud=sabsence" method="post">
			  <table align="left" style="width: 100%">
				<tr><td align="left">ວິຊາ &nbsp;&nbsp;<select name="absub" id="absubid"></select></td></tr>
				<tr><td align="left">ວັນທີ &nbsp;<input type="date" name="abdate" id="abdateid"></td></tr>
			  </table>
			</form>
			 <?php
			    // Get Subject from tbattendance
       			// ADD ITEMS INTO Select: class 
	   			echo "<script> 
	   				var subab = document.getElementById('absubid'); 
	    			var opt_non=document.createElement('option');
		  				opt_non.value='non';
		  				opt_non.text='';
	      				subab.options.add(opt_non);
	    			</script>";

       				$sqlsubcl = "SELECT subj FROM tbattendance WHERE uid='$sid' GROUP BY subj";
       				$rsb = mysqli_query($con, $sqlsubcl) or die(mysqli_connect_error());
       					while($r=mysqli_fetch_array($rsb)){
		  					$sbaid = $r["subj"]; 
		  					list($sbnamel, $sbnamee) = Rsubjectname($sbaid, $con);
		  
		  					echo "<script>
	      							var sbid ='$sbaid';
		  							var sbname = '$sbnamel';
		  							var opt_non=document.createElement('option');
		  								opt_non.value=sbid;
		  								opt_non.text=sbname;
	      								subab.options.add(opt_non);
	      							</script>";
	   					} // End of while
			
			      echo "<script> 
	    			var opt_non=document.createElement('option');
		  				opt_non.value='allsb';
		  				opt_non.text='ທັງໝົດ';
	      				subab.options.add(opt_non);
	    			</script>";
			  	?>
		</div>	
		<div style="width: 50%; float: left"> <!-- LEFT SIDE -->
		 <?php 
		  // Subject loop		
			  // Search by subject and updated date				
			    if(!empty($_POST["absub"]) || !empty($_POST["abdate"])){
					$srchab = "";
					if(!empty($_POST["absub"])){
					  $srchab = $_POST["absub"];
					  if($srchab=="allsb"){
						 $sqlab = "SELECT * FROM tbattendance WHERE uid='$sid'";
					  } else {
						$sqlab = "SELECT * FROM tbattendance WHERE uid='$sid' AND subj='$srchab'";  
					  }  
					} 
					
					if(!empty($_POST["abdate"])){
					  $srchab = $_POST["abdate"];
					  $sqlab = "SELECT * FROM tbattendance WHERE uid='$sid' AND adate='$srchab' ORDER BY adate DESC";
					}
					
				} else {
     			  $sqlab = "SELECT * FROM tbattendance WHERE uid='$sid'";		
				}
			
		  $rb = mysqli_query($con, $sqlab) or die(mysqli_connect_error());
		  if(mysqli_num_rows($rb)>0){
			 $i = 0;
			print "<table class='custb'>";
			print "<tr><th>ລດ</th><th>ວິຊາ</th><th>ສອນໂດຍ</th><th>ວັນທີ</th><th>ຊົ່ວໂມງຂາດ</th><th>ເຫດຜົນການຂາດ</th></tr>";
			 while($r=mysqli_fetch_array($rb)){
				 $i = $i + 1;
				$tid = $r["teacherid"];
				list($id,$uname,$psw,$namel, $namee,$snamel) = Userbyid($tid, $con);
				$tname = "ອຈ. ".$namel."  ".$snamel;
				$sub = $r["subj"];
				list($sbnamel, $sbnamee) = Rsubjectname($sub, $con);
				$adt = $r["adate"];
				$adt = date("d-m-Y", strtotime($adt));
				$atm = $r["atime"];
				$abtime = Rttime($atm, $con);
				$abnor = $r["absencenor"];
				if($abnor==1){
				  $abnor = "ມີເຫດຜົນ";
				} else {
				  $abnor = "ບໍ່ຮູ້ເຫດຜົນ";
				}
			  print "<tr><td align='center'>$i</td><td>$sbnamel</td><td>$tname</td><td>$adt</td><td>$abtime</td><td>$abnor</td></tr>";
		  	} // End of while 
		   print "</table>";  
		  } else {  // End of if>0
			//$nabs = "no";
			 echo "<script>
			       document.addEventListener('DOMContentLoaded', function() {
                   document.getElementById('dabid').innerHTML = 'ຍັງບໍ່ມີວັນຂາດຮຽນ';
                    });
			      </script>";
		  }
		?>
			<div align="center" id="dabid">&nbsp;</div>
		</div> <!-- End of LEFT SIDE -->	
		<div style="width: 45%; float: left; margin-left: 65px; background-color: #F3F4F9; border: solid 1px #73c2fb; border-radius: 5px"> <!-- RIGHT SIDE -->
			<?php
			   list($id,$uname,$psw,$namel,$namee,$snamel,$snammee,$bdate, $gender) = Userbyid($sid, $con);
			   $sname = $namel." ".$snamel;
			   
			   $sqltab = "SELECT SUM(absence) AS tabs, SUM(absencenor) AS tabsno FROM tbattendance WHERE uid='$sid' AND MONTH(adate)=7 GROUP BY MONTH(adate)";
			   $rab = mysqli_query($con,$sqltab) or die(mysqli_error());
			   list($sabs, $sabsno) = mysqli_fetch_array($rab);
			
			  // Count all absences
			   $sqlallab = "SELECT SUM(absence) AS allabs, SUM(absencenor) AS allabsno FROM tbattendance WHERE uid='$sid' GROUP BY uid";
			   $rtab = mysqli_query($con, $sqlallab) or die(mysqli_connect_error());
			   list($tsabs, $tsabsno) = mysqli_fetch_array($rtab);
			?>
		   <div align="center" style="margin: 15px; background-color: #DEDFE4; border-radius: 5px; height: 40px; vertical-align: middle"><div style="font-size: 14t; font-weight: bold">ສະຫຼຸບວັັນຂາດຮຽນ ຂອງ <?php echo $sname; ?></div></div>
		  <table align="center" class="abtb" style="width: 95%; margin-left: 15px; margin-bottom: 15px; border: solid 1px #73c2fb;">
			<tr><th>ເດືອນ</th><th class="atbor">ຈໍານວນຂາດ(ຄັ້ງ)</th><th class="atbor">ຈໍານວນຂາດບໍ່ມີເຫດຜົນ</th></tr>
			<tr><td>&nbsp;ມັງກອນ (01)</td><td align="center" class="atbor"><?php list($sab1, $sabn1) = Rabsence($sid, 1, $con); echo $sab1; ?></td><td align="center" class="atbor"><?php echo $sabn1; ?></td></tr>
			<tr><td>&nbsp;ກຸມພາ (02)</td><td align="center" class="atbor"><?php list($sab2, $sabn2) = Rabsence($sid, 2, $con); echo $sab2; ?></td><td align="center" class="atbor"><?php echo $sabn2; ?></td></tr>
			<tr><td>&nbsp;ມີນາ (03)</td><td align="center" class="atbor"><?php list($sab3, $sabn3) = Rabsence($sid, 3, $con); echo $sab3; ?></td><td align="center" class="atbor"><?php echo $sabn3; ?></td></tr>
			<tr><td>&nbsp;ເມສາ (04)</td><td align="center" class="atbor"><?php list($sab4, $sabn4) = Rabsence($sid, 4, $con); echo $sab4; ?></td><td align="center" class="atbor"><?php echo $sabn4; ?></td></tr>
			<tr><td>&nbsp;ພຶດສະພາ (05)</td><td align="center" class="atbor"><?php list($sab5, $sabn5) = Rabsence($sid, 5, $con); echo $sab5; ?></td><td align="center" class="atbor"><?php echo $sabn5; ?></td></tr>
			<tr><td>&nbsp;ມີຖຸນາ (06)</td><td align="center" class="atbor"><?php list($sab6, $sabn6) = Rabsence($sid, 6, $con); echo $sab6; ?></td><td align="center" class="atbor"><?php echo $sabn6; ?></td></tr>
			<tr><td>&nbsp;ກໍລະກົດ (07)</td><td align="center" class="atbor"><?php list($sab7, $sabn7) = Rabsence($sid, 7, $con); echo $sab7; ?></td><td align="center" class="atbor"><?php echo $sabn7; ?></td></tr>
			<tr><td>&nbsp;ສິງຫາ (08)</td><td align="center" class="atbor"><?php list($sab8, $sabn8) = Rabsence($sid, 8, $con); echo $sab8; ?></td><td align="center" class="atbor"><?php echo $sabn8; ?></td></tr>
			<tr><td>&nbsp;ກັນຍາ (09)</td><td align="center" class="atbor"><?php list($sab9, $sabn9) = Rabsence($sid, 9, $con); echo $sab9; ?></td><td align="center" class="atbor"><?php echo $sabn9; ?></td></tr>
			<tr><td>&nbsp;ຕຸລາ (10)</td><td align="center" class="atbor"><?php list($sab10, $sabn10) = Rabsence($sid, 10, $con); echo $sab10; ?></td><td align="center" class="atbor"><?php echo $sabn10; ?></td></tr>
			<tr><td>&nbsp;ກັນຍາ (11)</td><td align="center" class="atbor"><?php list($sab11, $sabn11) = Rabsence($sid, 11, $con); echo $sab11; ?></td><td align="center" class="atbor"><?php echo $sabn11; ?></td></tr>
			<tr><td>&nbsp;ທັນວາ (12)</td><td align="center" class="atbor"><?php list($sab12, $sabn12) = Rabsence($sid, 12, $con); echo $sab12; ?></td><td align="center" class="atbor"><?php echo $sabn12; ?></td></tr>
			 <tr><td style="border-top: solid 1px #73c2fb;">&nbsp;<b>ຈໍານວນ ວັນຂາດຮຽນທັງໝົດໃນປີ</b></td><td align="center" class="tmg"><?php echo $tsabs;  ?></td><td align="center" class="tmg"><?php echo $tsabsno; ?></td></tr>
		  </table>
		</div>
		
	</div> <!-- Main DIV -->

<script>
 $("#absubid").change(function(){
	  $("#frabid").submit();  // Submit search form
  });
	
 $("#abdateid").change(function(){
	  $("#frabid").submit();  // Submit search form
  });
	
// DIV - absence
var clab = "<?php echo $nabs; ?>";
if(clab.length>0){
  

}
	
</script>
