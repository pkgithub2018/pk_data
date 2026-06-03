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
include("supports.php");

$dgid=$_POST["dgid"]; // it is used for both add and update
$starea = $_POST["starea"]; // In incomplete submission of modal form
$clssup = $_POST["clup"];


// Fill in select - CLASS with data;
echo "<script>
	  var sclass=document.getElementById('sclassid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sclass.options.add(opt_non);
	  </script>";
$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqlclass = "SELECT id, classname FROM tbclass WHERE degree='$dgid' AND studyarea='$starea'";
$rclass = mysqli_query($con,$sqlclass) or die(mysqli_connect_error());
while($r=mysqli_fetch_array($rclass)){
	$cid = $r["id"];
	$cname = $r["classname"];
	echo "<script>
	      var cid ='$cid';
		  var cname = '$cname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      sclass.options.add(opt_non);
	      </script>";
}
// Fill in select STUDY AREAS with empty for the first item
echo "<script>
	  var ssarea=document.getElementById('sstareaid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      ssarea.options.add(opt_non);
	  </script>";
// Check degree for study area to fill select STUDY AREA WITH relevant data
$sqlstarea = "";
if($dgid==1){  // If secendary school
 $sqlstarea = "SELECT * FROM tbstudyarea WHERE id = '1'";
} else {
  $sqlstarea = "SELECT * FROM tbstudyarea WHERE id <> '1'";
}
$rstarea = mysqli_query($con,$sqlstarea) or die(mysqli_connect_error());
while($rw=mysqli_fetch_array($rstarea)){
  $starid = $rw["id"];
  $starname = $rw["sareaname"];
  echo "<script>
	      var cid ='$starid';
		  var cname = '$starname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      ssarea.options.add(opt_non);
	      </script>";
}
?>
<!-- ************** Refill the data for select: study area ************ -->
<script>
 var sarea = "<?php echo $starea; ?>";
 var classup = "<?php echo $clssup; ?>"; // in case of updating data ONLY
	if(sarea.length>0){
		for(i=0; i<arselect.length; i++){
		  if(arselect[i].value==sarea){
		    arselect.selectedIndex=i;
		   } 
	     }
	   }
	// Update class 
	if(classup.length>0){
		for(n=0; n<sselect.length; n++){
		  if(sselect[n].value==classup){
			  sselect.selectedIndex=n;
			 }
		}
	   }
</script>