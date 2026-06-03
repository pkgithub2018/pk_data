<script>
 // When modal form pops up 
// Remove items from degree select
 var dgclm = document.getElementById("mdgid");
 if(dgclm.childNodes.length>0){ 
	dgclm.innerHTML="";	// Remove items from SELECT
 }
// Remove items from study area select
 var stclm = document.getElementById("mstaid");
 if(stclm.childNodes.length>0){ 
	stclm.innerHTML="";	// Remove items from SELECT
 }
	
// Remove items from class select
 var clm = document.getElementById("mclid");
 if(clm.childNodes.length>0){ 
	clm.innerHTML="";	// Remove items from SELECT
 }
</script>
<?php 
include("connection.php");
include("supports.php");

$dgm=$_POST["mdg"]; 
$starm = $_POST["sarm"]; 
$clssm = $_POST["clm"];

//echo "Hello, classmove: ".$dgm." ".$starm." ".$clssm;

// Fill in select Degree with empty for the first item
echo "<script>
	  var sdgm=document.getElementById('mdgid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sdgm.options.add(opt_non);
	  </script>";

$con->set_charset("utf8");
$sqldg = "SELECT * FROM tbdegree";
$rdg = mysqli_query($con,$sqldg) or die(mysqli_connect_error());
while($rw=mysqli_fetch_array($rdg)){
  $dgid = $rw["id"];
  $dgname = $rw["degreename"];
  echo "<script>
	      var did ='$dgid';
		  var dname = '$dgname';
		  var opt_non=document.createElement('option');
		  opt_non.value=did;
		  opt_non.text=dname;
	      sdgm.options.add(opt_non);
	      </script>";
}


// Fill in select STUDY AREAS with empty for the first item
echo "<script>
	  var ssarea=document.getElementById('mstaid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      ssarea.options.add(opt_non);
	  </script>";
// Check degree for study area to fill select STUDY AREA WITH relevant data
$sqlstarea = "SELECT * FROM tbstudyarea";
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

// Fill in select - CLASS with data;
echo "<script>
	  var sclass=document.getElementById('mclid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sclass.options.add(opt_non);
	  </script>";
//$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqlclass = "SELECT id, classname FROM tbclass WHERE degree='$dgm' AND studyarea='$starm'"; //$dgm." ".$starm.
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

?>
<!-- ************** Refill the data for select: study area ************ -->

<script>
 
 var dgrmid = "<?php echo $dgm; ?>";
 var sarea = "<?php echo $starm; ?>";
 var classm = "<?php echo $clssm; ?>"; 
	
	if(dgrmid.length>0){
		for(i=0; i<sdgm.length; i++){
		  if(sdgm[i].value==dgrmid){
		    sdgm.selectedIndex=i;
			sdgm.style.backgroundColor = "#DAF7A6";
		   } 
	     }
	   }
	
	if(sarea.length>0){
		for(i=0; i<ssarea.length; i++){
		  if(ssarea[i].value==sarea){
		    ssarea.selectedIndex=i;
			ssarea.style.backgroundColor = "#DAF7A6";
		   } 
	     }
	   }
	
	if(classm.length>0){
		for(n=0; n<sclass.length; n++){
		  if(sclass[n].value==classm){
			  sclass.selectedIndex=n;
			  sclass.style.backgroundColor = "#DAF7A6";
			 }
		}
	   }
</script>