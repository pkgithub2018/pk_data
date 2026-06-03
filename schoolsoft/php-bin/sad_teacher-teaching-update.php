<script>
 // Remove items from select: STUDY AREA when user clicks on select DEGREE
 // EMPTY Select ***************
 var sdgtch = document.getElementById('tchdgreeid'); 
 var sareatch = document.getElementById('tchareaid');
 var subtch = document.getElementById("tchsubid");
 var cltch = document.getElementById("tchclassid");
 var daytch = document.getElementById('tchdayid');
 var timetch = document.getElementById('tchtimeid');
 var semestertch = document.getElementById('tchtsemesterid');
	
 if(sdgtch.childNodes.length>0){ 
	sdgtch.innerHTML="";	// Remove items from SELECT
 }
 if(sareatch.childNodes.length>0){
	 sareatch.innerHTML="";
	}
 if(subtch.childNodes.length>0){
	subtch.innerHTML="";
	}
 if(cltch.childNodes.length>0){
	 cltch.innerHTML="";
	}
 if(daytch.childNodes.length>0){
	 daytch.innerHTML="";
	}
 if(timetch.childNodes.length>0){
	timetch.innerHTML=""; 
 }
 if(semestertch.childNodes.length>0){
	semestertch.innerHTML=""; 
 }
</script>

<?php 
include("connection.php");
include("supports.php");
$teachid=$_POST["tchid"];  // Teaching id from sad_teacher.php

$sqltching = "SELECT * FROM tbteaching WHERE id='$teachid'";
$rtching = mysqli_query($con, $sqltching) or die(mysqli_connect_error());
list($tid, $uid, $subj, $cls, $tday, $ttime, $smst, $ayear) = mysqli_fetch_array($rtching);

list($unlao, $uneng) = Rusername($uid,$con);
$unlao = "ອາຈານ ".$unlao;  // In heading of Modal form
// Put teacher's name in <p> in sad_teacher.php
echo "<script>
      var tname = document.getElementById('tnameid'); 
	      tname.innerHTML = '$unlao';
	  </script>";
// OPEN MODAL FORM FOR UPDATE ON Teaching
// User id - assigned to HIDDEN INPUT IN Modal form
echo "<script>
       var userid = document.getElementById('huserteachingfid');
	       userid.value='$uid';
	  </script>";
// 1. Degree from tbsubjects for teaching *********************
// Fill SELECTOR first
echo "<script> 
        var dgtch = document.getElementById('tchdgreeid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      dgtch.options.add(opt_non);
	    </script>";
//$con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqldgree = "SELECT * FROM tbdegree";
  $rdgree = mysqli_query($con,$sqldgree) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rdgree)){ // 1
	$dgree = $r["id"];
	$dgname = $r["degreename"]; 
	//$dgname = Rdegreename($dgree, $con);
	// DEGREE 
	echo "<script>
	      var dgid ='$dgree';
		  var dgname = '$dgname';
		  var opt_non=document.createElement('option');
		  opt_non.value=dgid;
		  opt_non.text=dgname;
	      dgtch.options.add(opt_non);
	      </script>";
  }

// 2. Study area from tbsubjects ***************
  echo "<script> 
        var sareatch = document.getElementById('tchareaid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sareatch.options.add(opt_non);
	    </script>";

  $sqlsarea = "SELECT * FROM tbstudyarea";
  $rarae = mysqli_query($con,$sqlsarea) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rarae)){ 
    $sid = $r["id"];
	$sarname = $r["sareaname"]; 
	echo "<script>
	      var sid ='$sid';
		  var sarname = '$sarname';
		  var opt_non=document.createElement('option');
		  opt_non.value=sid;
		  opt_non.text=sarname;
	      sareatch.options.add(opt_non);
	      </script>";  
  }

// Get degree and study area id based on existing subj
$sqldgs = "SELECT dgree, sarea FROM tbsubjects WHERE id='$subj'";
$rdgs = mysqli_query($con,$sqldgs) or die(mysqli_connect_error());
list($dgreeid, $sareaid) = mysqli_fetch_array($rdgs);

// 3. Subject for teaching
echo "<script> 
        var subtch = document.getElementById('tchsubid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      subtch.options.add(opt_non);
	    </script>";

$sqlsub = "SELECT id, sublao FROM tbsubjects WHERE dgree='$dgreeid' AND sarea='$sareaid'";
$rsub = mysqli_query($con,$sqlsub) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rsub)){ 
    $subid = $r["id"];
	$subname = $r["sublao"]; 
	echo "<script>
	      var subid ='$subid';
		  var subname = '$subname';
		  var opt_non=document.createElement('option');
		  opt_non.value=subid;
		  opt_non.text=subname;
	      subtch.options.add(opt_non);
	      </script>";  
  }

// 4. Class for teaching based on Degree and Study areas
echo "<script> 
        var cltch = document.getElementById('tchclassid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      cltch.options.add(opt_non);
	    </script>";

$sqlcl = "SELECT id, classname FROM tbclass WHERE degree='$dgreeid' AND studyarea='$sareaid'";
$rcl = mysqli_query($con,$sqlcl) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rcl)){ 
    $clid = $r["id"];
	$clname = $r["classname"]; 
	echo "<script>
	      var clid ='$clid';
		  var clname = '$clname';
		  var opt_non=document.createElement('option');
		  opt_non.value=clid;
		  opt_non.text=clname;
	      cltch.options.add(opt_non);
	      </script>";  
  }

// 5. Teachday from tbtchday
  echo "<script> 
        var dtch = document.getElementById('tchdayid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      dtch.options.add(opt_non);
	    </script>";

  if($dgreeid=="1"){
	$sqlday = "SELECT id, dayname FROM tbtchday WHERE id NOT IN(6,7)";  // Secondary level
  } else {
	$sqlday = "SELECT id, dayname FROM tbtchday";
  }
  
  $rday = mysqli_query($con,$sqlday) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rday)){ 
    $dayid = $r["id"];
	$dayname = $r["dayname"]; 
	echo "<script>
	      var did ='$dayid';
		  var dname = '$dayname';
		  var opt_non=document.createElement('option');
		  opt_non.value=did;
		  opt_non.text=dname;
	      dtch.options.add(opt_non);
	      </script>";  
  }

// Get Class, teachday, teachtime, semester, ayear from tbteaching WHERE subject refers to degree and study areas in tbsubject 
$sqlcltch = "SELECT classid, teachday, teachtime, semester, ayear FROM tbteaching WHERE id='$tid'";
$rclt = mysqli_query($con, $sqlcltch) or die(mysqli_connect_error());
list($cltchid, $tdayid, $ttimeid, $tsmster, $ay) = mysqli_fetch_array($rclt); 

// 6. Teachtime from tbtchtime
  echo "<script> 
        var tmtch = document.getElementById('tchtimeid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tmtch.options.add(opt_non);
	    </script>";

  if($sareaid=="1"){
	  $sqltime = "SELECT id, tchtime FROM tbtchtime WHERE tcharea='1'";  // secondary level 
   } else {
	  $sqltime = "SELECT id, tchtime FROM tbtchtime WHERE tcharea='2'"; // college level
  }
 
 $rtime = mysqli_query($con,$sqltime) or die(mysqli_connect_error());
  while($j=mysqli_fetch_array($rtime)){ 
   $tid =$j["id"];
   $tname = $j["tchtime"];
   echo "<script>
	      var tid ='$tid';
		  var tname = '$tname';
		  var opt_non=document.createElement('option');
		  opt_non.value=tid;
		  opt_non.text=tname;
	      tmtch.options.add(opt_non);
	      </script>";  
	
  }

// 7. Semester
echo "<script> 
        var smsttch = document.getElementById('tchtsemesterid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      smsttch.options.add(opt_non);
	    </script>";
   $l = 1;
   $ltxt = "";
   do{   // DO WHILE
	 if($l==1){
		$ltxt = "I"; 
	 } else {
		$ltxt = "II";
	 }
	 echo "<script>
	         var opt_non=document.createElement('option');
		     opt_non.value='$l';
		     opt_non.text='$ltxt';
	         smsttch.options.add(opt_non);
	       </script>";
	 $l++;
   } while ($l<3)
?>
<script>
    var dgreesubj = document.getElementById("tchdgreeid");
	var stareasubj = document.getElementById("tchareaid");
	var ssubt = document.getElementById("tchsubid");
	var classt = document.getElementById("tchclassid");
	var dayt = document.getElementById("tchdayid");
	var timet = document.getElementById("tchtimeid");
	var smst = document.getElementById("tchtsemesterid");
	var btnsubmit = document.getElementById("btsubmoftchid");
	
    var tchingid = "<?php echo $teachid; ?>";
	var dgid = "<?php echo $dgreeid; ?>";
	var sarid = "<?php echo $sareaid; ?>";
	var subid = "<?php echo $subj; ?>";
	var clid = "<?php echo $cltchid; ?>";
	var tchdayid = "<?php echo $tdayid; ?>"; 
	var timetid = "<?php echo $ttimeid; ?>";
	var tsmsterid = "<?php echo $tsmster; ?>";
	 
	// Degree
	if(dgreesubj.length>0){ // if - SELECT degree not empty
	   for(k=0; k<dgreesubj.length; k++){
		 if(dgreesubj[k].value==dgid){
		    dgreesubj.selectedIndex=k;  // Go back to relevant value in select
		 }  
	   }
	 }
	// Study area
	if(stareasubj.length>0){
	    for(i=0; i<stareasubj.length; i++){
		  if(stareasubj[i].value==sarid){
			stareasubj.selectedIndex=i; // Go back
		  }
		}
	   }
	// Subject 
	if(ssubt.length>0){
		for(j=0; j<ssubt.length; j++){
		   if(ssubt[j].value==subid){
			   ssubt.selectedIndex=j;
			  }
		}
	}
	// Class 
	if(classt.length>0){
	    for(n=0; n<classt.length; n++){
		  if(classt[n].value == clid){
			  classt.selectedIndex = n;
			 }	
		}
	   }
	// Day 
	if(dayt.length>0){
		for(k=0; k<dayt.length; k++){
		  if(dayt[k].value == tchdayid){
			  dayt.selectedIndex = k;
			 }
		}
	}
	// Time
	if(timet.length>0){
	   for(i=0; i<timet.length; i++){
		 if(timet[i].value == timetid){
			 timet.selectedIndex = i;
		 }
	   }
	}
	// Semester 
	if(smst.length>0){
	    for(n=0; n<smst.length; n++){
		  if(smst[n].value == tsmsterid){
			  smst.selectedIndex = n;
		  }
		}
	   }
   // Submit button - Change its value
	if(tchingid.length>0){
	   btnsubmit.value = "ປັບປຸງ";
	   btnsubmit.style.color="yellow";
	} else {
	    btnsubmit.value = "ບັນທຶກ";
	}
	
</script>
