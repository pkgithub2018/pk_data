<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php 
include("connection.php");
//include("supports.php");
//***********************************
$gpvid=$_POST["pvid"];
//*******
echo "<script>
	  $('#dtid').empty(); 
	  var sdst=document.getElementById('dtid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sdst.options.add(opt_non);
	  </script>";
$con->set_charset("utf8"); //**** Make Lao font happy
$sqldist="SELECT districtid,distname_lao FROM tbdistrict WHERE provid='$gpvid'";
$redist=mysqli_query($con,$sqldist) or die(mysqli_connect_error());
while($r=mysqli_fetch_array($redist)){
 $did=$r["districtid"];
 $dname=$r["distname_lao"];
 echo "<script>
		  var gdid='$did';
		  var gdname='$dname';	
		  var opt=document.createElement('option');
			       opt.value=gdid;
			       opt.text=gdname;
			   sdst.options.add(opt);	
              </script>";
}  
?> 
