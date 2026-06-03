<?php 
 include("connection.php");
 include("supports.php");
 //echo "Hi, Phay, UPD";
 $usid = $_POST["usid"];
 
 $sqltchup = "SELECT * FROM tbcteachers WHERE userid='$usid'";
 $rtup = mysqli_query($con,$sqltchup) or die(mysqli_connect_error());
 list($tid, $dgree, $sarea, $clbase, $gdknow, $gduate, $pstion, $imname, $impath) = mysqli_fetch_array($rtup);
 echo "<script>
         var uid = '$usid';
	   </script>";
 // DEGREE SELECT
  echo "<script>
	    var tdgree=document.getElementById('tdegreeid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tdgree.options.add(opt_non);
	    </script>";
   	
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqldegree = "SELECT * FROM tbdegree";
  $rdegree = mysqli_query($con,$sqldegree) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rdegree)){ // 1
	$did = $r["id"];
	$dname = $r["degreename"];
	// DEGREE 
	echo "<script>
	      var dgid ='$did';
		  var dgname = '$dname';
		  var opt_non=document.createElement('option');
		  opt_non.value=dgid;
		  opt_non.text=dgname;
	      tdgree.options.add(opt_non);
	      </script>";
	 // KNOWLEDGE/EDUCATION
	
		echo "<script>
		  var opt_non=document.createElement('option');
		  opt_non.value=dgid;
		  opt_non.text=dgname;
	      knlevel.options.add(opt_non);
	      </script>";  
  } // End of while 

 // TEACHING AREA
   echo "<script>
	    var tarea=document.getElementById('sstareaid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tarea.options.add(opt_non);
	    </script>";

   //$con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsarea = "SELECT * FROM tbstudyarea";
  $rsarea = mysqli_query($con, $sqlsarea) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rsarea)){
	$sid = $r["id"];
	$sname = $r["sareaname"];
	echo "<script>
	        var opt_non=document.createElement('option');
		    opt_non.value='$sid';
		    opt_non.text='$sname';
	        tarea.options.add(opt_non);
	     </script>";
  }

// CLASS BASED 
  echo "<script>
           var clbase = document.getElementById('sclassid');
		   var opt_non=document.createElement('option');
		   opt_non.value='';
		   opt_non.text='';
	       clbase.options.add(opt_non);
        </script>";

  $sqlcl = "SELECT id, classname FROM tbclass WHERE degree='$dgree' AND studyarea='$sarea'";
  $rcl = mysqli_query($con, $sqlcl) or die(mysqli_connect_error());
  while($rw = mysqli_fetch_array($rcl)){
	$clid = $rw["id"];
	$clname = $rw["classname"];
	echo "<script>
	     var opt_non=document.createElement('option');
		   opt_non.value='$clid';
		   opt_non.text='$clname';
	       clbase.options.add(opt_non);
		 </script>";
  }

// KNOWLEDGE LEVEL 
  echo "<script>
           var knlevel = document.getElementById('slevelid');
		   var opt_non=document.createElement('option');
		   opt_non.value='';
		   opt_non.text='';
	       knlevel.options.add(opt_non);
        </script>";
  
  $sqlkl = "SELECT * FROM tbdegree";
  $rkl = mysqli_query($con, $sqlkl) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rkl)){
	 $klid = $r["id"];
	 $klname = $r["degreename"];
	 echo "<script>
	         var opt_non=document.createElement('option');
		      opt_non.value='$klid';
		      opt_non.text='$klname';
	          knlevel.options.add(opt_non); 
	       </script>";
  }

// SUBJECT KNOWLEDGE - Graduation
echo "<script>
           var subjkn = document.getElementById('sknowid');
		   var opt_non=document.createElement('option');
		   opt_non.value='';
		   opt_non.text='';
	       subjkn.options.add(opt_non);
        </script>";

$sqlgr = "SELECT * FROM tbgraduate";
$rgr = mysqli_query($con,$sqlgr) or die(mysqli_connect_error());
while($rs=mysqli_fetch_array($rgr)){
  $gdid = $rs["id"];
  $gdname = $rs["gname"];
  echo "<script>
          var opt_non=document.createElement('option');
		   opt_non.value='$gdid';
		   opt_non.text='$gdname';
	       subjkn.options.add(opt_non);
		</script>";
}

// POSITION 
echo "<script>
           var sps = document.getElementById('spositionid');
		   var opt_non=document.createElement('option');
		   opt_non.value='';
		   opt_non.text='';
	       sps.options.add(opt_non);
        </script>";

$sqlps = "SELECT * FROM tbposition";
$rps = mysqli_query($con,$sqlps) or die(mysqli_connect_error());
while($r=mysqli_fetch_array($rps)){
  $psid = $r["id"];
  $psname = $r["psname"];
  echo "<script>
           var opt_non=document.createElement('option');
		   opt_non.value='$psid';
		   opt_non.text='$psname';
	       sps.options.add(opt_non);
	    </script>";
}

// PHOTO/IMAGE photoid: get photo back based on their path

	echo "<script>
       var phtpth = '$impath';
       var tpht = document.getElementById('photoid');
		   tpht.src = '".$impath."';
      </script>";
?>
<script>
  var exdgree = document.getElementById("tdegreeid");
  var exstarea = document.getElementById("sstareaid");
  var excl = document.getElementById('sclassid');
  var exknl = document.getElementById('slevelid');
  var exgrad = document.getElementById("sknowid");
  var exps = document.getElementById("spositionid");
  var btnsub = document.getElementById("btsubmofid");
  
  var cdgree = "<?php echo $dgree; ?>";
  var cstarea = "<?php echo $sarea; ?>";
  var ccl = "<?php echo $clbase; ?>";
  //    alert("Hello, class: " + ccl);
  var cknl = "<?php echo $gdknow; ?>";
  var cgrad = "<?php echo $gduate; ?>";
  var cps = "<?php echo $pstion; ?>";
  var tphpath = "<?php echo $impath; ?>";
	 	
	  if(exdgree.length>0){
		 for(k=0; k<exdgree.length; k++){
			 if(exdgree[k].value==cdgree){
				exdgree.selectedIndex=k;
			 }
		 }
		}
	
	  if(exstarea.length>0){
		  for(n=0; n<exstarea.length; n++){
			if(exstarea[n].value == cstarea){
			    exstarea.selectedIndex = n;
			   }  
		  }
		 }
	
	 if(excl.length>0){
		 for(j=0; j<excl.length; j++){
		   if(excl[j].value == ccl){
			   excl.selectedIndex = j;
			  } 
		 }
		}
	
	 if(exknl.length>0){
		for(k=0; k<exknl.length; k++){
		  if(exknl[k].value == cknl){
			 exknl.selectedIndex = k;
		  }	
		}
	 }
	
	if(exgrad.length>0){
	    for(h=0; h<exgrad.length; h++){
		  if(exgrad[h].value == cgrad){
			exgrad.selectedIndex = h;
		  }
		}
	   }
	 
	if(exps.length>0){
	    for(i=0; i<exps.length; i++){
		  if(exps[i].value == cps){
			  exps.selectedIndex = i;
			 }
		}
	   }
	// Change submit's value 
	btnsub.value = "ປັບປຸງ";
    btnsub.style.color = "yellow";
</script>