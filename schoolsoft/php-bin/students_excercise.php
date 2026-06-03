  <style>
	  .lhead{
		 margin-left: 100px; 
	  }
	  .lhead input[type=date], select {
		  width: 300px;
	  }
  </style>

  <?php
      list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr) = Userinfo($un,$pw,$con);
      list($sid,$slevel,$starea,$sclass,$sayear) = Currentstudent($id,$con);
      
   ?>
    <div align="center" class="cominpt" style="width: 100%; float: left; display: inline-block">
		<div style="width: 45%; float: left"> <!-- LEFT SIDE -->
		 <h2 align="left" class="lhead">ບົດຮຽນ</h2>
		 <hr width="90%" align="left" style="margin-left: 3%">
		 <h4 align="left" class="lhead">ຊອກ ບົດຮຽນຕາມ</h4>
		  <div class="lhead">
			<form id="frlssid" action="content.php?stud=exercise" method="post">
			  <table align="left" style="width: 100%">
				<tr><td align="right">ວິຊາ:</td><td align="left">&nbsp;<select name="lbysub" id="lbysubid"></select></td></tr>
				<tr><td align="right">ວັນທີ ອັບເດດ:</td><td align="left">&nbsp;<input type="date" name="lbydate"></td></tr>
			  </table>
			</form>
			 <?php
			    // Get Subject from tbteaching based on class
       			// ADD ITEMS INTO Select: class 
	   			echo "<script> 
	   				var subsl = document.getElementById('lbysubid'); 
	    			var opt_non=document.createElement('option');
		  				opt_non.value='';
		  				opt_non.text='';
	      				subsl.options.add(opt_non);
	    			</script>";

       				$sqlsubcl = "SELECT subjid FROM tbteaching WHERE classid='$sclass' GROUP BY subjid";
       				$rsb = mysqli_query($con, $sqlsubcl) or die(mysqli_connect_error());
       					while($r=mysqli_fetch_array($rsb)){
		  					$sblss = $r["subjid"]; 
		  					list($sbnamel, $sbnamee) = Rsubjectname($sblss, $con);
		  
		  					echo "<script>
	      							var sbid ='$sblss';
		  							var sbname = '$sbnamel';
		  							var opt_non=document.createElement('option');
		  								opt_non.value=sbid;
		  								opt_non.text=sbname;
	      								subsl.options.add(opt_non);
	      							</script>";
	   					} // End of while
			  	?>
		  </div>
		  <table align="left" class="custb" style='width: 95%; margin-left: 3%'>
			<tr><th>ລດ</th><th>ວິຊາ</th><th>ຊື່ ຫົວຂໍ້ ບົດເຝືກຫັດ ຫຼື ບົດທວນຄືນ</th><th>ໂດຍ</th><th>ວດປ ເອັບໂຫຼດ</th><th>ເບີ່ງ</th></tr>
			  <?php
			  // Subject loop
			  // Search by subject and updated date		  
			    if(!empty($_POST["lbysub"]) || !empty($_POST["lbydate"])){
					$srch = "";
					if(!empty($_POST["lbysub"])){
					  $srch = $_POST["lbysub"];	
					  //ltype=1 - Lesson
					  $sqllson = "SELECT * FROM tblessons WHERE subid='$srch' AND ltype IN(2,3) ORDER BY fileupdate DESC";
					}
					
					if(!empty($_POST["lbydate"])){
					  $srch = $_POST["lbydate"];	
					  $sqllson = "SELECT * FROM tblessons WHERE fileupdate='$srch' AND ltype IN(2,3) ORDER BY fileupdate DESC";
					}
					
				} else {
					if(empty($_POST["lbysub"])){
					   $sqllson = "SELECT * FROM tblessons WHERE ltype IN(2,3) ORDER BY fileupdate DESC";
					}
				}
			  
				
				$rls = mysqli_query($con, $sqllson) or die(mysqli_connect_error());
				//list($lsid, $sblsid, $ltype, $tchidls, $sqno, $lstopic) = mysqli_fetch_array($rls);
			  $dfound = ""; // Keep data is not found
			  
			  if(mysqli_num_rows($rls)>0){
				$i = 0;
				while($rl=mysqli_fetch_array($rls)){
					$tchls = $rl["teacherid"];
					$subls = $rl["subid"];
					list($id,$uname,$psw,$tnamel, $tnamee,$tsnamel, $tsnammee) = Userbyid($tchls, $con); // Teacher's name
					$tnamel = "ອຈ. ".$tnamel;
					list($sublao, $subeng) = Rsubjectname($subls, $con);
					$topicls = $rl["topic"];
					$filepath = $rl["filepath"];
					$dateup = $rl["fileupdate"];
					$newdateup =date("d-m-Y", strtotime($dateup));
					 
					 $sqlteach = "SELECT * FROM tbteaching WHERE userid='$tchls' AND subjid='$subls' AND classid = '$sclass'";
			         $rtch = mysqli_query($con,$sqlteach) or die(mysqli_connect_error());
					 if(mysqli_num_rows($rtch)>0){
						  $i = $i + 1;
						 
						  print "<tr><td align='center'>$i</td><td>$sublao</td><td>$topicls</td><td>$tnamel</td><td>$newdateup</td><td align='center'><a style='color: black' href='content.php?stud=exercise&sub=$subls&topic=$topicls&dup=$dateup&flpath=$filepath'>ເປີດ</a></td></tr>";
					 }
				 } // End of while  
			  } else { // End of if >0
				  $dfound = "nofound";
			  } 
			    
			  ?>
		  </table>
			<div id="ndataid" style="width: 100%">&nbsp;</div>
		</div> 
		<div style="width:50%; float: left; margin-top: 120px"> <!-- RIGHT SIDE -->
			<!-- lesson file is opened -->
			<div style="font-size: 13pt">
			  <?php 
				$flesson = $_GET["flpath"];
				$ndup = date("d-m-Y", strtotime($_GET["dup"]));
				if(!empty($flesson)){
					list($sblao, $sbeng) = Rsubjectname($_GET["sub"], $con);
				   echo "<b>ວິຊາ: </b>".$sblao.",<br> <b>ຫົວຂໍ້: </b>".$_GET["topic"].", <b>ວັນທີ ອັບເດດ: </b>".$ndup;
				}
			  ?>
			  
			</div>
		  <iframe src='<?php echo $flesson; ?>' align="middle" style="width: 100%; height: 550px">
		  </iframe>
		</div>	
	</div> <!-- Main DIV -->

<script>
  $("#frlssid").change(function(){
	  this.submit();  // Submit search form
  });
// No data found - message
var ndf = "<?php echo $dfound; ?>";
 if(ndf.length>0){
	 document.getElementById("ndataid").innerHTML="ບໍ່ມີຂໍ້ມູນ ສໍາລັບ ວັນທີນີ້";
	}
</script>
