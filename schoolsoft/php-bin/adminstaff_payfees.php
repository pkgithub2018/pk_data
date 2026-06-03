  <style>
	  /*  Set page for printing  */
	  @media print {
            @page {
                size: 23mm 14mm;  /* A4 size, can be changed to custom dimensions */
                margin: 2mm;        /* Custom margin */
            }
		  .dcontent{
			 padding: 2mm;
		  }
	  } /* End of page for printing */
	  
	  .tbhead{
		  border-collapse: collapse;
		  width: 60%;
		 
	  }
	  .tbhead, td{
		  padding:3px; 
	  }
	  .tbhead, input[type=text], select{
		  margin: 1px;
		  height: 40px;
	  }
	  .tbhead, select{
		  width: 40%;
	  }
	  .tbhead, input[type=text]{
		  width: 70%; 
		  border-radius: 5px; 
		  font-size: 12pt;
	  }
	  .dv-left{
		  margin-left: 50px;
	  }
	  .dv-headpay{
		 width: 98%;
		 margin-top: 1%; 
		/* margin-bottom: 2%;*/
		 border: solid #DCDCDC 1px;
		 background-color: #77DD77;
	  }
	 
	  .dv-outpay{
		  width: 100%; 
		  margin-top: 18px; 
		  box-shadow: -3px -3px #d8f9ff;
		  border: solid #D6EBF2 1px; 
		  border-radius: 3px; 
		  background-color: #D3F5D5;
	  }
	  .dv-inpay{
		 overflow: auto; /* Ensure div covers its contents */
		 width: 98%; 
		 
		/* margin-top: 2%; */
		 margin-bottom: 1%;
		 border: solid #DCDCDC 1px;
		 background-color: white;
	  }
	  .fset{
		  width: 90%;
		  height: 30%; 
	      margin: 10px 3px 10px 3px;
		  border: solid 1px #D5D5D5; 
		  background-color: #EBEBEB;
	  }
	  .tbpay{
		  width: 94%; 
		  border: solid 1px #D5D5D5;
		 /* margin-left: 10px; */
	  }
	  .tbcontent{
		  width: 95%;
		  border-collapse: collapse;
		  border-spacing: 2px;
		  margin-left: 15px;
		  /* border: solid grey 1px; */
	  }
	  	  
	  .dv-btn{
		 width: 100%;
		 margin: 10px 15px 15px 45%; 
	  }
	  .dv-btn, input[type=reset],input[type=button],input[type=submit]{
		  width: 90px;
		  display: inline;
	  }
	  .paytb_items{
		  text-align: center; 
		  background-color: royalblue;
	  }
	  .tbhbill{
		  width:100%;
		  font-size: 4pt;
		  padding: 0px;
		}
	
  </style>
<script>
 
</script>
  <?php
      list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr) = Userinfo($un,$pw,$con); // Teacher
      list($sid,$slevel,$starea,$sclass,$sayear) = Currentstudent($id,$con);

	  $utype = $_SESSION["usertype"];

    // Paylist **********
  /*
    $sqlplist = "SELECT * FROM tbpaylist";
    $rplist = mysqli_query($con, $sqlplist) or die(mysqli_connect_error());
    $pls = array();
     $i = 0;
      while($r=mysqli_fetch_assoc($rplist)){
	   $i = $i + 1;
		  $pname = $r["payitem"];
		  $pls[] = $pname;
		 // echo "Plupdate: ".$i."<br>";
	  }
   */  
   ?>
  <div align="left" style="margin-left: 50px"> <!-- Heading -->
	<h2 align="left" class="lhead">ການຈ່າຍຄ່າຮຽນ ແລະ ອື່ນໆ</h2>
	<form id="frsrpayid" action="content.php?adstaff=ptfees&sad=schfees&fid=txtsearch" method="post">
		 <table class="tbhead">
		 <tr><td colspan="2" align="left"><i class='fa fa-search' style='font-size:20px; margin-left: 10px'></i>&nbsp;<input type="text" name="txtchname" id="txtchnameid" placeholder="&nbsp;ກະລຸນາ ພີມຊື່ ຫຼື ຕົວອັກສອນໃດໜື່ງ ທີຕ້ອງການຊອກ" style="color: #bbbbbb; border: solid #CFCFCF 1px;" onChange="Subfsearchstu();"></td></tr>
		 <tr><td colspan="2" align="left"><b>ຊອກຕາມຫ້ອງຮຽນ</b></td></tr>
		 <tr><td align="right" style="width: 10%">ຊັ້ນ/ຂັ້ນ</td><td align="left"><select name="gradfee" id="gradfeeid"></select></td></tr>
		 <tr><td align="right">ຂະແໜງ/ວິຊາ</td><td align="left"><select name="arsfee" id="arsfeeid"></select></td></tr>
		 <tr><td align="right">ຫ້ອງຮຽນ</td><td align="left"><select name="clsfee" id="clsfeeid"></select></td></tr> 
		</table>
	</form>
		<?php 
	        // DEGREE SELECT **********
	  echo "<script>
	  var msdg=document.getElementById('gradfeeid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      msdg.options.add(opt_non);
	  </script>";
 		//$con->set_charset("utf8");
		if(!empty($utype) && $utype=="6"){ // School admin
			$sqldgree = "SELECT id,degreename FROM tbdegree WHERE id=1"; 
		} else if(!empty($utype) && $utype=="8"){ // Kidkaden admin
			$sqldgree = "SELECT id,degreename FROM tbdegree WHERE id=8"; 
		} else { // System admin
 		    $sqldgree = "SELECT id,degreename FROM tbdegree";  
		}
 		$rdgree = mysqli_query($con,$sqldgree) or die(mysqli_connect_error());
 		while($rw=mysqli_fetch_array($rdgree)){
			$dgid=$rw["id"];
			$dgname=$rw["degreename"];
  			// degree select 
			echo "<script>
		  var dgid='$dgid';
		  var dgname='$dgname';	     
		  var opt=document.createElement('option');
			       opt.value=dgid;
			       opt.text=dgname;
			   msdg.options.add(opt);
              </script>";
 		}	
	    ?>
	</div>	<!-- End of heading -->

    <div align="center" class="cominpt" style="width: 100%; float: left; display: inline-block"> <!-- Main content -->
		
		<div class="dv-left" style="width: 50%; float: left"> <!-- LEFT SIDE -->
		  <?php	
			if(!empty($_POST["txtchname"])){
			  $txtsearch = $_POST["txtchname"];	
			}
			
			if(!empty($_GET["searclass"])){
			  $cls = $_GET["searclass"];
			}

			Showstudfees($txtsearch, $cls, $utype, $con);  // LIST OF STUDENTS
		  ?>
			<div align="center" id="dfeeid">&nbsp;</div>
		</div> <!-- End of LEFT SIDE -->	
		<div id="dv-pay" style="width: 40%; float: left; margin-left: 20px;"> <!-- RIGHT SIDE - Payment -->
			<div class="dv-outpay">
			 <div class="dv-headpay">
			   <h3>ການຈ່າຍຂອງນັກຮຽນນັກ/ສຶກສາ</h3>
			 </div>
			 <div class="dv-inpay">
			  <form id="frpay" class="usform" action="content.php?adstaff=ptfees&sad=schfees&sadkid=kidsfees" method="post">
			   <fieldset class="fset"> <!-- declared in initcss.css -->
				 <legend><b>ຂໍ້ມູນສ່ວນຕົວ</b></legend>
				  <table align="left" style="width: 100%">
				   <tr><td align="right">ຊື່ ແລະ ນາມສະກຸນ &nbsp;</td><td align="left"><input type="text" name="stflname" id="stflnameid"></td></tr>
				   <tr><td align="right">ຫ້ອງຮຽນປະຈຸບັນ &nbsp;</td><td align="left"><input type="text" name="cclass" id="cclassid"></td></tr>
				  </table>
				</fieldset>
				  <!-- ************ HIDDEN INPUT ********** -->
				  <input type="hidden" name="hstudent" id="hstudentid"> 
				  <input type="hidden" name="hclass" id="hclassid">
				  <input type="hidden" name = "hfullcls" id = "hfullclsid">
				  <input type="hidden" name = "htbrow" id = "htbrowid">
				  <!-- ************************************ -->
		   <!-- ****** HISTORY OF PAYMENT ************* -->
				<div id="hispayid" align="left">&nbsp;</div>
			<!--  Content_pay -->  
			  <div id="content_pay">
			    <table align="centre" class="tbpay">
				  <tr><td align="right">ວັນທີຈ່າຍ</td><td align="left"><input type="date" name="paydate" id="paydateid" style="width: 85%"></td></tr>
				  <tr><td align="right">ວິທີຈ່າຍ</td><td align="left"><input type="checkbox" name="chcash" id="chcashid" value="" onClick="Chcash_bank(this.id)">&nbsp;ເງີນສົດ</td></tr>
				  <tr><td align="right">&nbsp;</td><td align="left"><input type="checkbox" name="chbpay" id="chbpayid" value="" onClick="Chcash_bank(this.id)">&nbsp;ເງີນໂອນ, &nbsp;&nbsp;ທະນາຄານ&nbsp;&nbsp;<select name="nbank" id="bankid" disabled style="width: 85%"></select></td></tr>
				  <tr><td align="right">ເນື້ອໃນ/ໝາຍເຫດ</td><td align="left"><textarea name="descpay" id="descpayid" rows="2" cols="42" style="border: 1px solid #E3DBDB"></textarea></td></tr> 
				</table>
				  <!-- ****** HIDDEN INPUT - tbpayment ******* -->
				    <input type="hidden" name="hpayment" id="hpaymentid">
				    <input type="hidden" name="hbill" id="hbillid">
				  <!-- **************************************** -->
				<div align="left" style="margin: 10px 0px 10px 10px">
				   <input type="checkbox" name="chaddr" id="chaddrid"><label>ເພີ່ມລາຍການ</label>
				</div>
				
				<table id="tbpayid" class="tbcontent">
				   <tr><th align="center" style="background-color: #e7e7e7">ລດ</th><th align="center" style="width:50%; background-color: #e7e7e7">ລາຍການ</th><th align="center" style="width:35%; background-color: #e7e7e7">ຈໍານວນເງີນ(ກີບ)</th><th style="width:10%; background-color: #e7e7e7">&nbsp;</th></tr>
				   
				</table>
					
				<!-- Summary table -->
				<table id="tbpaysumid" class="tbcontent" style="border-top: 1px solid black">
					<tr><td>&nbsp;</td><td align="right" style="width:50%"><b>ລວມທັງໝົດ</b></td><td align="center" style="width:35%"><input type="text" name="tamount" id="tamountid" disabled="disabled" style="width: 100%; height: 30px; margin: 0px; background-color: #73c2fb"></td><td style="width:10%">&nbsp;</td></tr>
				</table>
			  </div> <!-- End of content_pay   -->	
				 <div id="div_buttn" align="right" class="dv-btn">
				   <input type="submit" name="btncancel" id="btncancelid" value="ຍົກເລີກ"><input type="submit" name="btnsubmit" id="btnsubmitid" value="ພີມອອກ">
				</div>
			 </form>
			 </div>
			</div> <!-- End of div-outpay -->
			<!-- ******************************** PAYMENT BILLING ******************************* -->
			<div class="dcontent" id="pbillid">&nbsp;</div>
		</div> <!-- End of right side -->
	   <?php 
		// SUBMIT/SAVE payment data **************************************
		 if(isset($_POST["btnsubmit"]) && $_POST["btnsubmit"]=="ພີມອອກ"){
			//echo "Hello, Student ID: ".$_POST["hstudent"]."   ".$_POST["paydate"];
			 $modpay = "";
			 $rwarnmess = ""; 
			 $tbrows = "";
			 $cfprint = "";
			 list($stid,$stname,$stpsw,$stnamel, $stnamee,$stsnamel, $stsnammee, ,$stbdate, $stgender) = Userbyid($_POST["hstudent"], $con);
			 $studname = Fullname($_POST["hstudent"], $con);
			 $fclass = $_POST["hfullcls"];
			 // Check if checkbox - cash is checked
			 	if(isset($_POST["chcash"])){
					$modpay = "cash"; // paid by cash
			 	}
			    if(isset($_POST["chbpay"])){
					$modpay ="bank";
				} 
			 // GET information after saving the payment 
			 list($rwarnmess, $payid, $bllnum) = Savepayment("", $_POST["hstudent"],$_POST["paydate"], $modpay, $_POST["nbank"], $_POST["descpay"],$con);
			 
			 		 
			// SAVE DETAILS OF PAYMENT ****************		 		 
			// $_POST["htbrow"] - Number of payment table rows - Number of table rows must be more than 1
			if($_POST["htbrow"]>1){
			  for($i=1; $i<$_POST["htbrow"]; $i++){
				 $amount = "amount".$i;
				 $item = "selpayit".$i;
				// echo "Item and Amount: ".$_POST[$item]."  ".$_POST[$amount]."<br>";
				 
				 $sqlpdetails = "INSERT INTO `tbpaydetails`(`payid`, `billno`, `itemid`, `amount`) VALUES('$payid', '".$bllnum."', '".$_POST[$item]."', '".$_POST[$amount]."')";
				 mysqli_query($con,$sqlpdetails) or die(mysqli_connect_error());
			 } // End of for
				$cfprint = "yes";  // Confirm printing after saved
			} // End of if - SUBMIT SAVE
			 
	// SEND DATA TO PRINTER for BILL PRINTING  *******************
			// document.body.innerHTML='<p align=\"left\">ກະຊວງ ສຶກສາທິການ ແລະ ກິລາ<br>Hello</p>"
			$hstuid = $_POST["hstudent"];
			Billprint($payid, $hstuid, $con); // This function is replaced with code below
		
		 } // End of isset - FORM SUBMISSION
		
		//  AFTER PRININTING BILLING ***************************
		if(!empty($_GET["aftprint"]) && $_GET["aftprint"]=="yes"){ // $_GET is in this file
			//echo "Printing: ".$_GET["aftprint"]."  ".$_GET["psid"]."  ".$_GET["ppid"];
		} // End of if ater printing
		
		// HOSTORY OF PAYMENT - Show list of list of payment
		if(!empty($_GET["sidhispay"])){
			//echo "List of payment".$_GET["sidhispay"];
		}
		
		// EDIT ON PAYMENT DETAILS
		if(!empty($_GET["pchange"]) && $_GET["pchange"]=="payedit"){ // it is also declared as below
			// Get student id based on pay id
			list($pided, $bnoed, $stidped,$pdated, $mdpayed, $bkned, $despay) = Rpayment($_GET["pedit"], $con);
			//echo "Hello, student ".$stidped;
			$sqlpded = "SELECT * FROM tbpaydetails WHERE payid='$pided'";
			$rped = mysqli_query($con, $sqlpded) or die(mysqli_connect_error()); // It is used in bottom of this file
			$numpay = mysqli_num_rows($rped); // IT IS USED in javascript below
		}
		
		// DELETE ON PAYMENT WITHOUT CONFIRMATION **************
		if(!empty($_GET["pchange"]) && $_GET["pchange"]=="paydel"){ // Link supports.php
			$pid_del = $_GET["pdel"];
			$stid_del = $_GET["stidch"]; // Student id
					
			if(!empty($pid_del) && !empty($stid_del)){
			 // Store student id and payment id in hidden input in modal form
			  $confdel = true;	 // hstuidl - confirm delete: For opening modal form
			} else {
			  $confdel = false;
			  // DELETE PAYMENT WITHOUT confirmation because NO DATA in tbpaydetails TABLE
			  $sqldel = "DELETE FROM tbpayments WHERE id='$pid_del'";
			  mysqli_query($con, $sqldel) or die(mysqli_connect_error());
			  // Go back to history of payment
			  echo "<script>
			         window.location.href = 'content.php?adstaff=ptfees&sidhispay=$stid_del'; 
			     </script>";
			}
		}

		// DELETE ON PAYMENT WITH CONFIRMATION - Data on payment in both tables: tbpayments and tbpaydetails
		if(isset($_POST["btnfModal"]) && $_POST["btnfModal"]=="ແມ່ນ"){
			$pidmodal = $_POST["hpaydl"];
			$sidmodal = $_POST["hstuidl"]; 

		    echo "<script>
			       var hpid = '".$pidmodal."';
				   var hstuid = '".$sidmodal."';
				   alert('ລືບໃບເກັບເງີນ: '+hpid +' ຂອງນັກຮຽນ: '+hstuid);
				  </script>";

			$sqldelpay = "DELETE FROM tbpayments WHERE id='$pidmodal' AND uid='$sidmodal'";
			mysqli_query($con, $sqldelpay) or die(mysqli_connect_error());

			$sqldelpayd = "DELETE FROM tbpaydetails WHERE payid='$pidmodal'";
			mysqli_query($con, $sqldelpayd) or die(mysqli_connect_error());
			// back to history of payment
			echo "<script>
			         window.location.href = 'content.php?adstaff=ptfees&sidhispay=$sidmodal'; 
			     </script>";	
		} else if(isset($_POST["btnfModalno"]) && $_POST["btnfModalno"]=="ບໍ່"){
			$sidmodalno = $_POST["hstuidl"]; 
			// back to history of payment
			echo "<script>
			         window.location.href = 'content.php?adstaff=ptfees&sidhispay=$sidmodalno'; 
			     </script>";	
		}
		
		// UPDATE ON PAYMENT and PAYMENT IN DETAILS **********************
		if(isset($_POST["btnsubmit"]) && $_POST["btnsubmit"]=="ປັບປຸງ"){
			$spayid = $_POST["hpayment"]; // Pay id
			$billno = $_POST["hbill"];
			$st = $_POST["hstudent"];
			$pd = $_POST["paydate"];
			$modpayment = "";
			 if(isset($_POST["chcash"])){  // Pay by cash
				$modpayment = "cash";
			 } else if(isset($_POST["chbpay"])){ // Pay via bank
				$modpayment = "bank"; 
				$bankid = $_POST["nbank"];
			 }
			
			$pdes = $_POST["descpay"];
			// UPDATE ON tbpayment ***********			
			$cfpup = Uppayment($spayid, $st,$pd, $modpayment, $bankid, $pdes, $con);
			echo "Confirm: ".$cfpup;
			
			// UPDATE ON tbpaydetails **********
			$chupdate = "";		
			$chupdate=Update_paydetails(15,$spayid, $billno, $con); // set number of table rows to 15 to ensure all the data can be saved
			 	
			if($chupdate==true){
				Billprint($spayid, $st, $con);
				
				// GIVE MESSAGE FOR SUCCESSFULL UPDATE ON PAYMENT - NOT USED
				/*
				echo "<script>
			         window.location.href = 'content.php?adstaff=ptfees&msupd=payupdate&stpu=$st&pdu=$pd'; 
			     </script>";
				 */
			}	
			
		
		} // End of if - UPDATE ON PAYMENT

	// CANCEL PAYMENT and GO BACK PAYMENT page **********
		if(isset($_POST["btncancel"]) && $_POST["btncancel"]=="ຍົກເລີກ"){	
		 	echo "<script>
		         	window.location.href = 'content.php?adstaff=ptfees'; 
		     	</script>";
		}
	  ?>
</div> <!-- Main content DIV -->
<!-- MODAL FORM - WARNING MESSAGE  - DELETE BILL **********************-->
 
<div id="fModal" class="mdmess">
 <!-- Modal content -->
  <div class="mdmess-content" style="height:25%;">
    <span class="closemsg">&times;&nbsp;</span>
    <div class="mdmess-heading">
      <div align="center" class="msheading">ແຈ້ງເຕືອນ</div>
    </div>
	  <div style="display: inline-block;margin-left: 60px; margin-top: 20px">
		  <div style="float: left; vertical-align: middle"><i class="fa-solid fa-triangle-exclamation" style="font-size: 30pt; color: #F6BE00"></i></div>
		  <div style="float: left; vertical-align: middle; margin-top: 10px; font-size: 14pt">ຕ້ອງການລືບໃບເກັບເງີນນີ້ບໍ?ພກ</div>
	  </div>
	  <div align="center" style="margin-bottom: 5px">
	     <form action ="content.php?adstaff=ptfees" method="post">
			<input type="hidden" name="hpaydl" id="hpaydl" value="">
		    <input type="hidden" name="hstuidl" id="hstuidl" value="">
			<input type="submit" name="btnfModal" value="ແມ່ນ" style="width: 15%" />&nbsp;&nbsp;<input type="submit" name="btnfModalno" value="ບໍ່" style="width: 15%" />    
		</form>
	  </div>
  </div>
</div>
<?php
  	
?>
<script>

// document.ready ***************************
$(document).ready(function () {
	
 }); // End of document.ready
//  degree to add study area ****************

  $("#gradfeeid").change(function(){
	$(this).css("background-color", "#BCE6FF");
	var msdgid = $(this).val();
	var sareas = document.getElementById("arsfeeid");
	   if(sareas.childNodes.length>0){
		   sareas.innerHTML = "";
		  }
	   
	 $.ajax({
		type: "POST",
		url: "adminstaff_payfees-area.php",  
		data: {msdgid: msdgid},
		 
		success: function(gdata){
		  $("#dfeeid").html(gdata); // just make it happy
		}
	 }); 
	 
  });

// areas to add class 
  $("#arsfeeid").change(function(){
	$(this).css("background-color", "#BCE6FF");
	var msarea = $(this).val();
	var dgrv = document.getElementById("gradfeeid").value;
	var cls = document.getElementById("clsfeeid");
	  
	  if(cls.childNodes.length>0){
		  cls.innerHTML ="";
	  }
	  
	 $.ajax({
		type: "POST",
		url: "adminstaff_payfees-class.php",  //sad_mainsclass.php is changed to sad_student-class.php
		data: {dgid: dgrv, areaid: msarea},
		success: function(gdata){
		  $("#dfeeid").html(gdata); // just make it happy
		}
	 });   
  });
	
// class for searching 
// Class select in main file
  $("#clsfeeid").change(function(){
	 $(this).css("background-color", "#BCE6FF");
	 var mscl = $(this).val();
	 var msarea = document.getElementById("arsfeeid").value;
	 var msdegree = document.getElementById("gradfeeid").value;
	 window.location.href = 'content.php?adstaff=ptfees&sad=schfees&searclass=' + mscl + '&seararea=' + msarea + '&seardgree=' + msdegree; // SEND JavaScript variable to PHP
  });

// *************
 $("#txtchnameid").change(function(){  // Search text box
	  $("#frsrpayid").submit();  // Submit search form
  });
	
 $("#abdateid").change(function(){
	  $("#frabid").submit();  // Submit search form
  });
// Hide pay table
	$("#tbpayid").hide();
	$("#tbpaysumid").hide(); // summary pay table

// Checkbox - pay table on change ******************
  $("#chaddrid").change(function(){
	  if($(this).is(':checked')){  // if checked
		$("#tbpayid").show();  
		 AddRow('tbpayid');
	  } else {
		 // Delete table rows and hide
		 var ptble = document.getElementById("tbpayid");
		  for(var i = ptble.rows.length - 1; i > 0; i--)
		  	{
    			ptble.deleteRow(i); 
			}
		 $("#tbpayid").hide(); 
	  }
  });

// Pay table on change
  $("#tbpayid").change(function(){
	  $("#tbpaysumid").show();
  });

// SHOW PAYMENT FORM - make payments *****************
var showpform = "<?php 
                   if(!empty($_GET["sidpay"])){
					$stuid = $_GET["sidpay"];   
				   } else if(!empty($_GET["psid"])){ // AFTER BILL PRINTING
					 $stuid = $_GET["psid"]; 
				   } else if(!empty($stidped)){  // PAYMENT EDITION
					  $stuid = $stidped; 
				   } else if(!empty($_GET["sidhispay"])){
					  $stuid = $_GET["sidhispay"];
				   }
  					
                    echo $stuid; // Keep ECHO GET link from functon - Showstudfees in supports.php
                   
                    $fstuname = Fullname($stuid, $con);
                    // Get currennt class for students
					list($sid,$slevel,$starea,$sclass,$sayear) = Currentstudent($stuid,$con);
					$clname = Rclassname($sclass, $con);
					$dgree = Rdgree($slevel, $con);
					$stuarea = Rsarea($starea, $con);
					$fullclass = $clname.", ".$dgree.", ".$stuarea;
                 ?>"; 
 //Submit Payment form *************
var submitval ="<?php echo $_POST["btnsubmit"]; ?>";
	
	if(showpform.length>0 || submitval.length>0){
	  $("#dv-pay").show();
	  
		 // Case of submit - use hidden input 
		var hstudid ="<?php $stvid = $_POST["hstudent"]; 
		                echo $stvid; 
		                 $fstunamesub = Fullname($stvid, $con);
		               ?>";
		
	  var fstname = document.getElementById("stflnameid");
	  var stclass = document.getElementById("cclassid");
	  var hfstuid = document.getElementById("hstudentid");
	  var hfclass = document.getElementById("hfullclsid");  // Full name of class
	  var hclassv = document.getElementById("hclassid");
	    	  	
		if(submitval.length>0 && submitval=="ພີມອອກ"){
			//alert("Hidden-Up" + hstudid);
		    $("#content_pay").hide();
		  }
		
	      fstname.value = "<?php $stfname = !empty($stuid)?$fstuname:$fstunamesub; echo $stfname; ?>"; 
		 // alert("Full name: " + fstname.value);
		  hfstuid.value = "<?php $std = !empty($stuid)?$stuid:$hstudid; echo $std; ?>";
		  hclassv.value = "<?php echo $sclass; ?>";
		  hfclass.value = "<?php echo $fullclass; ?>";
		
		  var scls = "<?php echo $fclass; ?>";
		      if(scls.length>0){
				//alert("Full class: " + scls);  
			  }
		  
		  stclass.value = "<?php $ccls = !empty($stuid)?$fullclass:$fclass; echo $ccls; ?>"; // $stuid from link $_GET[""]
			                
		  fstname.style="background-color: lightblue;";
		  stclass.style="background-color: lightblue;";
		 // fstname.disabled = true;
		 // stclass.disabled = true;
		
		// FILL PAYMENT for Edition ************
		
	} else {
	  $("#dv-pay").hide(); //
	} // End of if - form submission


// SELECT BANK NAME *******************
	var sbank = document.getElementById("bankid");
	    sbank.innerHTML="<?php
	          echo "<option value=''>&nbsp;</option>";
			  $sqlbank = "SELECT * FROM tbbanks";
    		  $rbank = mysqli_query($con, $sqlbank) or die(mysqli_connect_error());
      		  while($r=mysqli_fetch_array($rbank)){
				$bid = $r["id"];
				$bname = $r["bankname"];
				echo "<option value='$bid'>$bname</option>";
			  }
			?>";
	
// AFTER BILL PRINTING or DIRECT ACCESS TO EDIT THE PAYMENT  ***********
 var abillprint = "<?php echo $_GET["aftprint"]; ?>";  // After bill printing
 var diraccess = "<?php echo $_GET["sidhispay"]; ?>";  // Direct access to edit payment
	
	if((abillprint.length>0 && abillprint=="yes") || diraccess.length>0){
	     $("#dv-pay").show();
		 $("#content_pay").hide(); // HIDE the details of bill
		 $("#div_buttn").hide(); // HIDE 
		// ADD/SHOW history of payment in  ***********
		var hisp = document.getElementById("hispayid");
		  hisp.innerHTML = "<div style='margin-left: 10px'>ປະຫັວດການຈ່າຍ <?php $paystid = !empty($_GET["psid"])?$_GET["psid"]:$_GET["sidhispay"]; Historypay($paystid,$con); ?></div>";
	   }

// UPDATE PAYMENT - Fill the form with payment for edition
// $mdpayed, $bkned, $despay	
var pedition = "<?php $ed=$_GET["pchange"]; echo $ed; ?>";
	if(pedition.length>0 && pedition=="payedit"){
		 var payediid = "<?php $pid = $_GET["pedit"]; echo $pid; ?>";
		 var pbillid = "<?php $bill = $_GET["billno"]; echo $bill; ?>";
		 var paymentdate = "<?php echo $pdated; ?>";
		 var mdpay = "<?php echo $mdpayed; ?>";
		 var bkid = "<?php echo $bkned; ?>";
		 var payremark = "<?php echo $despay; ?>";
		
		 var hpayid = document.getElementById("hpaymentid");
		 var hbill = document.getElementById("hbillid");
	     var pdate = document.getElementById("paydateid");
		 var chcashup = document.getElementById("chcashid");
	     var chbankup = document.getElementById("chbpayid");
	     var selbankup = document.getElementById("bankid"); 
		 var despay = document.getElementById("descpayid");
		 // alert("Pay code: " + payediid);
		   hpayid.value = payediid; // Payment id
		   hbill.value = pbillid; // Bill number
		   pdate.value = paymentdate;
		   pdate.style = "background-color: lightgrey;";
		 if(mdpay=="cash"){
			 chcashup.checked = true;
		  } else if(mdpay=="bank"){
			 chbankup.checked = true;
			 selbankup.disabled = false;  // enable 
			 for(var i=0; i<selbankup.length; i++){
				if(selbankup[i].value==bkid){
				    selbankup.selectedIndex=bkid;
					selbankup.style = "background-color: lightgrey;";
				   }
			 } // End of for
			 // bankk
		  } // End of if cash
		despay.value = payremark;
		despay.style = "background-color: lightgrey;";
		
		// EDIT DETAILS OF PAYMENT
		$("#content_pay").show();
		
	   } // End if pedition for edit payment
	
// MODAL FORM - MESSAGE OPENING for UPDATE ON PAYMENT - NOT USED **********************

 // Get the modal
var modal = document.getElementById("fModal");

// Get the button that opens the modal

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("closemsg")[0];

// MODAL FORM - Delete payments -WARNING MESSAGE for CONFIRMATION  **********************
var confexist = "<?php echo $confdel; ?>";
	if(confexist.length>0 && confexist==true){
	    //alert("Payment is ready to be deleted");
		modal.style.display = "block";
		// Open Modal form and assign value to hidden input
		document.getElementById('hpaydl').value = "<?php echo $pid_del;?>";
		document.getElementById('hstuidl').value = "<?php echo $stid_del; ?>";
	  }

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

// CHECKBOX - CASH AND BANK
   function Chcash_bank(chid){
	 var chcash = document.getElementById("chcashid");
	 var chbank = document.getElementById("chbpayid");
	 var selbank = document.getElementById("bankid");  // This select was set to disabled as default
	   
	  if(chid=="chcashid"){  // By cash
		   
		   if(chbank.checked){
			   chbank.checked = false;
			}
		  selbank.selectedIndex = 0;
		  selbank.disabled = true;
		 } 
	   
	  if(chid=="chbpayid"){  // Via bank
		   if(chcash.checked){
			  chcash.checked = false;
			} 
		   selbank.disabled = false;
		 }
   }  // End of function
	
// OPEN AND EDIT Details of payment
var npay = "<?php echo $numpay; ?>";
	
	if(npay.length>0){
		document.getElementById('chaddrid').checked=true; //tick the checkbox
		// Show table of payment details in PHP code below
		// Loop rows of the table in PHP code below
	 }
  	
  /* ******************* Add Table rowns ****************  */
 function AddRow(tableID){
	
   var table=document.getElementById(tableID);
   var rowCount=table.rows.length;
   var row=table.insertRow(rowCount);

	//**************************************** Column1 - Check in
 
   var cell1=row.insertCell(0);
   var em1=document.createElement("input");
	   em1.style="text-align: center; margin: 0px 0px 0px 2px;"; 
       em1.type="checkbox";
	   em1.name="check" + rowCount;
	   em1.onclick=function(){
		   var btnpid = "btnaddid" + rowCount;
		   var btnv = document.getElementById(btnpid);
		   
		   if(this.checked==true){
			  btnv.value="ລືບອອກ";
			  btnv.style ="width: 100%; height: 30px; background-color: #e7e7e7; color: red; font-size: 12px; font-weight: normal; margin: 0px;";
			  this.checked==false;
			  } else {
			   this.checked==true;
			   btnv.value="ເພີ່ມ";
			  }
	   }
	   
	  // em1.height="32px";
	   //em1.id="font_lao22";
	   cell1.appendChild(em1);
	   
   //**************************************** Column2 - Pay items
   var cell2=row.insertCell(1);
   var em2=document.createElement("select");
  // var em2s=em2.style;
      em2.style="width: 100%; height: 30px; margin: 0px;";
      em2.name="selpayit"+rowCount; 
      em2.id="selpayit" + rowCount;
	  /*
	  em2.onmouseover = function(){
		alert("id " + em2.id);
	  }
	  */
	  em2.innerHTML="<?php 
	      // Paylist **********
		   echo "<option value=''>*** ກະລຸນາເລືອກ ລາຍການ ***</option>";
    		$sqlplist = "SELECT * FROM tbpaylist";
    		$rplist = mysqli_query($con, $sqlplist) or die(mysqli_connect_error());
      		while($r=mysqli_fetch_array($rplist)){
				$itid = $r["id"];
				$pitem = $r["payitem"];
				echo "<option value='$itid'>$pitem</option>";
				
			}
	  ?>";
	 // em2.options[8]=new Option('*** ກະລຸນາເລືອກ ລາຍການ  ***','');
	  em2.selectedIndex=0; // Set default value
	//  em2s.border="0";
	  cell2.appendChild(em2);
	  	 
	 //**************************************** Column3 - Amount
 
   var cell3=row.insertCell(2);
   var em3=document.createElement("input"); 
       em3.type="text";
	   em3.style="width: 100%; height: 30px; margin: 0px;";
	   em3.name="amount" + rowCount;
	   em3.id = "amountid" + rowCount;
	   em3.onmouseover = function(){
		//alert("Amount id" + em3.id + em3.value);
	   }

	   em3.onblur = function(){
		  // check empty
		 if(em3.value.trim()!==""){
		  em2.style="background-color: #E5F3FD; width: 100%; height: 30px; margin: 0px;";
		  em3.style="background-color: #E5F3FD; width: 100%; height: 30px; margin: 0px;";
		 }
		 // check number 
		 var vem3=$(this).val();
			if(isNaN(vem3)){ //****** if not numberic
				alert("ກະລຸນາ ປ້ອນຕົວເລກ");
				return false;
				//this.focus();
			 }
		em3.value = vem3.toLocaleString();
		  var tpayment = 0;
		   tpayment = SumAmout("tbpayid");
		   document.getElementById("tamountid").value = tpayment.toLocaleString();
		  // alert("ID " + this.id);
	   } // End of onblur
	   //em1.size="30";
	   //em1.id="font_lao22";
	   cell3.appendChild(em3);
	 
	//**************************************** Column4 - Button
    var cell4=row.insertCell(3);
   var em4=document.createElement("input");
       em4.type="button";
	   em4.style="width: 100%; height: 30px; background-color: #e7e7e7; color: black; font-size: 12px; font-weight: normal; margin: 0px;"; 
	   em4.name="btnadd" + rowCount;
	   em4.id="btnaddid" + rowCount;
	   em4.value ="ເພີ່ມ";
	   em4.onclick=function(){
		   var btnpay = this.value;
		  // alert("Button's value:" + btnpay);
		   if(btnpay=="ເພີ່ມ"){
			    AddRow("tbpayid"); // Add new row as this button is clicked
			 }
		   
		  if(btnpay=="ລືບອອກ"){
			    deleteRow("tbpayid"); // Delete checked input - amount from table
			    var reamount = RSumAmout("tbpayid");
			    document.getElementById("tamountid").value = reamount.toLocaleString();
			 } 
		   
	   }
	   //em1.size="30";
	   //em1.id="font_lao22";
	   cell4.appendChild(em4);
	 // *******************
	//Resettable(tableID); // Reset
 }

 //*********************************************************************** DeleteRows 

function deleteRow(tableID){ 
	
    try {
		  var table = document.getElementById(tableID);
    	  var rowCount = table.rows.length;
    		//if (lastRow > 2) table.deleteRow(lastRow - 1);
			for(var i=0; i<rowCount; i++){
			  var row=table.rows[i];
			  var chkbox=row.cells[0].childNodes[0];
			  if(null !=chkbox && true==chkbox.checked){
			    table.deleteRow(i);
				rowCount--;
				i--;
				 row = i;
				// alert("row id" + row);
				// reset sequential number
				//row.cells[0].textContent = rowCount - i;
			  } 
			}
		} catch(e) {
		  alert(e);
		}
	// *******************
	//Resettable(tableID); // Reset
}
	
// Sum amount 
 function SumAmout(tableID){
   var table=document.getElementById(tableID);
   var rowCount=table.rows.length;
		
	 var totalSum = 0;

    for (var k = 0; k < rowCount; k++) {
        var pamount = "amountid" + k;
        var inputElement = document.getElementById(pamount);
        
        if (inputElement) { // Check if the element exists
            var vamt = parseFloat(inputElement.value);
            if (!isNaN(vamt)) { // Check if it's a valid number
                totalSum += vamt; // Add to total sum
            } else {
                alert("Invalid number in " + pamount);
            }
        } 
    }
    
	 return totalSum;
 }
	
// Return Sum amount 
 function RSumAmout(tableID){
   var table=document.getElementById(tableID);
   var rowCount=table.rows.length;
		
	 var rtotalSum = 0;

    for (var k = 0; k <= rowCount; k++) {
        var reamt = "amountid" + k;
        var inputElement = document.getElementById(reamt);
        
        if (inputElement) { // Check if the element exists
            var vamt = parseFloat(inputElement.value);
            if (!isNaN(vamt)) { // Check if it's a valid number
                rtotalSum += vamt; // Add to total sum
            } else {
                alert("Invalid number in " + pamount);
            }
        } 
    }
    
	 return rtotalSum;
 }

// Reset sequential number of table

 function Resettable(tableID){
   var ptable = document.getElementById(tableID);
   var ntbrows = ptable.rows.length;
   for(var j=0; j<ntbrows; j++){
	ptable.rows[j].id=j;
	//alert("Rw id" + ptable.rows[j].id);
   }
 }


// Return number of table rows - list of item payment
$("#frpay").submit(function(){
	var tb = document.getElementById('tbpayid');
	var hnrows = document.getElementById('htbrowid'); // hidden input for storing number of table rows
	var tbr = tb.rows.length;
	hnrows.value = tbr;  // Add number of rows into hidden input in form
//	alert("Form is submitted" + tbr);
});
</script>
<?php 
 // EDIT PAYMENT DETAILES BASED ON SQL STATEMENT ABOVE
  if(!empty($_GET["pchange"]) && $_GET["pchange"]=="payedit"){
  $k = 0;
	while($rs = mysqli_fetch_array($rped)){ // $rs - on the top
	  $k = $k + 1;
	  $payid = "amountid".$k;
	  $itemid = "selpayit".$k;
		
	  $bl = $rs["billno"];
	  $itpay = $rs["itemid"];
	  $pamt = $rs["amount"];
		/*
		echo "<script>
		       var itemcode = '$itemid';
			   var itemval = '$itpay';
			   alert('code: ' + itemcode + 'val: ' + itemval);
		      </script>";
		*/
			echo "<script>
		       var pt = '$payid';
			   var iptid ='$itemid';
			   var item = '$itpay';
			   var amt = '$pamt';
			       amtcom = amt.toLocaleString('en-US');
			   var btnsubm = document.getElementById('btnsubmitid');
			  
			   document.getElementById('tbpayid').style.display = 'block';
			   AddRow('tbpayid');
			   
			   document.getElementById(iptid).selectedIndex = item - 1;
			   document.getElementById(pt).value = amtcom;
			   btnsubm.value = 'ປັບປຸງ';
			   btnsubm.style.color = 'yellow';
		      </script>";
		
	 } // End of while
  }
?>
