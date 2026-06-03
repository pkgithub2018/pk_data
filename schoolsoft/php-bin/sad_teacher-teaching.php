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
$uid=$_POST["userid"];  // To receive, just make it happy
$sarid = $_POST["sarid"];

list($unlao, $uneng) = Rusername($uid,$con);
$unlao = "ອາຈານ ".$unlao;  // In heading of Modal form
// Put teacher's name in <p> in sad_teacher.php
echo "<script>
      var tname = document.getElementById('tnameid'); 
	      tname.innerHTML = '$unlao';
	  </script>";
// FIRST MODAL FORM OPEN
// User id - assigned to HIDDEN INPUT IN Modal form
echo "<script>
       var userid = document.getElementById('huserteachingfid');
	       userid.value='$uid';
	  </script>";
// Degree *********************
echo "<script> 
        var sdgtch = document.getElementById('tchdgreeid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sdgtch.options.add(opt_non);
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
	      sdgtch.options.add(opt_non);
	      </script>";
  }
?>
<script>
 var usid = "<?php echo $uid; ?>"; 
 var btsavemod =document.getElementById("btsubmoftchid");  
     if(usid.length>0){
		 btsavemod.value ="ບັນທຶກ"; // In case that submit button's does not change its value back
		 btsavemod.style.color = "white";
		}
</script>
