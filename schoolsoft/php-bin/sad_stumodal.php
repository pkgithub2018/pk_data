<?php 
include("connection.php");
include("supports.php");
echo "Hello from sad_stumodal.php<br>";

$uid = $_POST["uid"];
$btnval = $_POST["btnva"];
$gdegr = $_POST["gdegrb"];
$gstuarea = $_POST["stuarea"];
$gclass = $_POST["class"];

$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqlst="SELECT namelao, snamelao, dbirth, gender, mphone, email FROM tbusers WHERE id='$uid'";
$rst = mysqli_query($con,$sqlst) or die(mysqli_connect_error());
list($namelao, $snamelao, $dbirth, $gender, $mphone, $email) = mysqli_fetch_array($rst); 
$gder = Laotitle($gender,$con);
$fname = $gder.$namelao." ".$snamelao;
$gdbirth = date("d-m-Y",strtotime($dbirth));

//UPDATE student record *************
if($btnval=="ປັບປຸງ"){ 
  $sqlstupdate = "SELECT degree, stuarea, class FROM tbcstudents WHERE userid='$uid'";
  $restup = mysqli_query($con,$sqlstupdate) or die(mysqli_connect_error());
  list($degreeup, $stuareaup, $classup) = mysqli_fetch_array($restup);
  //echo "Hi".$degreeup."  ".$stuareaup." ".$classup;
  // Fill the inputs in modal form with $degreeup, $stuareaup and $classup in Javascript below
}
?>
<script>
// Complete MODAL FORM with data when poping-up
  var stname = document.getElementById("stid");
  var huid = document.getElementById("huserid");
  var sbdate = document.getElementById("bdateid");
  var sphone = document.getElementById("phoneid");
  var semail = document.getElementById("emailid");
      stname.value="<?php echo $fname; ?>"
	  stname.disabled = true;
	  huid.value = "<?php echo $uid; ?>";
      sbdate.value = "<?php echo $gdbirth; ?>"
	  sbdate.disabled = true;
	  sphone.value = "<?php echo $mphone; ?>";
	  sphone.disabled = true;
	  semail.value = "<?php echo $email; ?>";
	  semail.disabled = true;

// Remove items from select before refill it: Degree
 var dgselect = document.getElementById("sdegreeid");
 if(dgselect.childNodes.length>0){ 
	dgselect.innerHTML="";	// Remove items from SELECT - Degree
 }
 var arselect = document.getElementById("sstareaid");
 if(arselect.childNodes.length>0){ 
	arselect.innerHTML="";	// Remove items from SELECT - Degree
 }
// Remove items form select: Class in the beginning
var fsselect = document.getElementById("sclassid");
 if(fsselect.childNodes.length>0){ 
	fsselect.innerHTML="";	// Remove items from SELECT
 }

</script>
<!-- FILL IN SELECT WITH DATA -->
<?php 
 echo "<script>
	  var sdegree=document.getElementById('sdegreeid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sdegree.options.add(opt_non);
	  </script>";
 // DEGREE 
 $sqldg = "SELECT * FROM tbdegree";
 $rdg = mysqli_query($con,$sqldg) or die(mysqli_connect_error());
 while($r=mysqli_fetch_array($rdg)){
	$did =$r["id"];
	$dname = $r["degreename"];
	echo "<script>
	      var id ='$did';
		  var dgname = '$dname';
		  var opt_non=document.createElement('option');
		  opt_non.value=id;
		  opt_non.text=dgname;
	      sdegree.options.add(opt_non);
	      </script>";
 }
?>
<script>
  // INCOMPLETE SUBMISSION (Some of empty inputs), REFILL the data for SELECT for degree and study areas
  var getdgr = "<?php echo $gdegr;  ?>";
  var sarea = "<?php echo $gstuarea; ?>";
  //var sdgreeb = document.getElementById("sdegreeid");
  //var sstarea = document.getElementById("");
	if(getdgr.length>0){ // Select: degree
		for(k=0; k<dgselect.length; k++){  // dgselect is already declared above
		  if(dgselect[k].value==getdgr){
			 dgselect.selectedIndex=k; // Get back the data
			  $.ajax({
	           type: "POST",
	           url: "sad_starea.php", // replace sad_sclass with sad_starea.php
	           data: {dgid: getdgr, starea: sarea}, // For study area, its code needs to be sent to sad_sclass.php
	           success: function (rdata){
		       $("#dresult").html(rdata); // Just make it happy  
	           }
	          }); 
			} // End of if
        }
	  }
// UPDATE STUDENT data *************
var dgrup = "<?php echo $degreeup; ?>"; // Just check if degree is not empty instead of all study areas and class
var stareaup = "<?php echo $stuareaup; ?>";
var classup = "<?php echo $classup; ?>";	
	// Degree select
	if(dgrup.length>0){
	   for(i=0; i<dgselect.length; i++){
		 if(dgselect[i].value==dgrup){
			 dgselect.selectedIndex=i;			 
			 $.ajax({
	           type: "POST",
	           url: "sad_starea.php",
	           data: {dgid: dgrup, starea: stareaup, clup: classup}, // For study area, its code needs to be sent to sad_sclass.php
	           success: function (rdata){
		       $("#dresult").html(rdata); // Just make it happy  
	           }
	          }); 
		 } // End of if dgselect  
	   }
	 }
	// Study area select
/*
	if(stareaup.length>0){	
	   for(j=0; j<arselect; j++){
		  if(arselect[j]==stareaup){
			 arselect.selectedIndex=j;
		   } 
	   }
	 }
*/	 
</script>
