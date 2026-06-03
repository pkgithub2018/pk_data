<style>
	/* tbus - refers to class for table set in function Studytable in supports.php */
	.tbus {
	 font-size: 10pt; 
	 margin-top: 20px;
	}
	.tbus th{
	  text-align: center;
	}
	.tbus th, td{ 
	  padding: 5px;
	  border: 1px solid white; 
	  font-size: 11pt; 
	}
</style>
<div align="center" style="" class="usform">  <!-- *** MAIN DIV *** -->
  <div align="left" style="width: 70%; margin-left: 60px; margin-bottom: 50px">
	  <h2 align="left">ຕາຕະລາງສອນ/ຮຽນ</h2>
	  <p align="left">ກະລຸນາ ເລືອກ ຊັ້ນ/ຂັ້ນ, ຂະແໜງ ຫຼື ວິຊາຮຽນ ແລະ ປີຮຽນ</p>
	 
	  <form action="content.php?sad=classadd" method="post">
	   <table>
		  <tr>
		    <td>ຂັ້ນ/ຊັ້ນ</td><td><select name="stdgree" id="stdgreeid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td> 
		  </tr>
		  <tr>
		    <td>ຂະແໜງ/ວິຊາ</td><td><select name="starea" id="stareaid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td> 
		  </tr>
		  <tr>
		    <td>ຊື່ຫ້ອງຮຽນ</td><td><select name="stcl" id="stclid" style="width: 300px; height: 40px; padding: 3px;margin: 3px;">&nbsp;</select></td> 
		  </tr>
	  </table>
	  </form>   
  </div>
</div>

<?php 
  include("connection.php");
 
// Initialize degree variables
 $dg1 = $dg2 = $dg3 = $dg4 = $dg5 = $dg6 = $dg7 = 0;

// Subjects from tbteaching
 $sqlsb = "SELECT subjid FROM tbteaching GROUP BY subjid";
 $rsb = mysqli_query($con, $sqlsb) or die(mysqli_connect_error());

 while($rs = mysqli_fetch_array($rsb)){
  $subj = $rs["subjid"];
   
  $sqlsd = "SELECT dgree FROM tbsubjects WHERE id='$subj'";
  $rsd = mysqli_query($con,$sqlsd) or die(mysqli_connect_error());
  list($did) = mysqli_fetch_array($rsd);
  
  	switch($did){
		case 1:
		 $dg1 = 1;
	    break;	
		
		case 2: 
		 $dg2 = 2;
	    break;
	   
		case 3:
		 $dg3 = 3;
		break;
			
		case 4: 
		 $dg4 = 4;
	    break;
			
		case 5: 
		 $dg5 = 5;
	    break;
			
		case 6: 
		 $dg6 = 6;
	    break;
			
		case 7: 
		 $dg7 = 7;
	    break;
  	}
  
 } // End of while
 
// ADD ITEMS INTO Select: stdgreeid
	   echo "<script> 
	   var stdg = document.getElementById('stdgreeid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      stdg.options.add(opt_non);
	    </script>";

$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqldg = "SELECT id, degreename FROM tbdegree WHERE id='$dg1' OR id='$dg2' OR id='$dg3' OR id='$dg4' OR id='$dg5' OR id='$dg6' OR id='$dg7'";
$rdg = mysqli_query($con, $sqldg) or die(mysqli_connect_error());

while($rw=mysqli_fetch_array($rdg)){
	$did = $rw["id"];
	$dname = $rw["degreename"];
	
	echo "<script>
	      var did ='$did';
		  var dname = '$dname';
		  var opt_non=document.createElement('option');
		  opt_non.value=did;
		  opt_non.text=dname;
	      stdg.options.add(opt_non);
	      </script>";
   
} // End of while - degree
print "<div id='comstid' style='width: 100%'>";
 // Common study tables
  Studytable(1, $con); // Monday
  Studytable(2, $con); // Tuesday
  Studytable(3, $con); // Wed
  Studytable(4, $con); // Thurs
  Studytable(5, $con); // Friday
  Studytable(6, $con); // Saturday
print "</div>";
?>
<div id="stid" style="width: 100%"></div> <!-- Just make it happy -->

<script>
$(document).ready(function(){
  	
 // DEGREE ON CHANGE
  $("#stdgreeid").change(function(){
	var dgreev = $(this).val();
	var ssar = document.getElementById("stareaid");
	var scl = document.getElementById("stclid");
	  
	if(ssar.childNodes.length>0){
		//alert("Element");
		ssar.innerHTML = "";
	}
	if(scl.childNodes.length>0){
	     scl.innerHTML = "";
	   }
	   
	$.ajax({
	  type: "POST", 
      url: "sad_studytable-selector.php",
	  data: {dgr: dgreev},
	   success: function(rdata){
		 $("#stid").html(rdata);  // Just make it happy
	   }
		
	})
 });	
	
// Study area select to add list of class
$("#stareaid").change(function(){
   var starea = $(this).val();  // Value of study area select
   var sdgree = document.getElementById("stdgreeid").value;
   var scl = document.getElementById("stclid");
	
   if(scl.childNodes.length>0){
	   scl.innerHTML = "";
	  }
	//alert("Hello");
	$.ajax({
	  type: "POST",
	  url: "sad_studytable-selector.php",
	  data: {sarea: starea, sdgval: sdgree},
	   success: function(gdata){
		 $("#stid").html(gdata);
	   }
	});
});

// Class select to show study table by class
$("#stclid").change(function(){
  var clid = $(this).val();
  var cldg = document.getElementById("stdgreeid").value;
  var clstarea = document.getElementById("stareaid").value;
  var cdivstable = document.getElementById("comstid");
      cdivstable.innerHTML = "";  // Remove contents of DIV
     // alert("Hello, class");
	$.ajax({
	  type: "POST",
	  url: "sad_studytable-selector.php",
	  data: {class: clid, cldgree: cldg, clstarea: clstarea},
		success: function(rdata){
		  $("#stid").html(rdata);
		}
	});
});	
	
}); // End of document.ready
 
</script>
