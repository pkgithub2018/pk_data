<?php
 include("connection.php");
// include("supports.php"); //****** This causes a heading of cvs file
/*
 $fno=$_GET["fano"]; //********* From link in content.php
 $sid=$_GET["sidup"];
 //********************************************* FUNCTIONS *****************
  function Gfitname($fid,$con){ //****** Return name of FA item
    $sqlfitem="SELECT faps FROM tbfacontent WHERE id='$fid'";
    $rftem=mysqli_query($con,$sqlfitem) or die(mysqli_connect_error());
     list($finame)=mysqli_fetch_array($rftem);
     return $finame; 
  }

function Rnamevar($vid,$con){ //******** Return name of VARIABLES
	$sqlrv="SELECT varname FROM tbvariable WHERE id='$vid'"; 
	$rrv=mysqli_query($con,$sqlrv) or die(mysqli_connect_error());
	list($vname)=mysqli_fetch_array($rrv);
	return $vname;
 }
*/ 
 //*********************************************
 $filename = "userdraft_" . date('Y-m-d') . ".csv"; 
 $delimiter = ","; 
 
// Create a file pointer 
$f = fopen('php://memory', 'w'); 
 
// Set column headers 
$fields = array('ຊື່ເປັນພາສາລາວ','ຊື່ເປັນພາສາອັງກິດ','ນາມສະກຸນເປັນພາສາລາວ','ນາມສະກຸນເປັນພາສາອັງກິດ','ວດປເກີດ','ເພດ','ໂທລະສັບ','ອີແມ໋ວ','ທີຢູ່','ປະເພດຜູ້ໃຊ້'); //****** $namep
fputcsv($f, $fields, $delimiter); 
/*
$sqlfit="SELECT id,fano,faps FROM tbfacontent WHERE fano='$fno' ORDER BY id";
$rfit=mysqli_query($con,$sqlfit) or die(mysqli_connect_error());
if(mysqli_num_rows($rfit)>0){
  while($rf=mysqli_fetch_array($rfit)){
	$varf="price".$rf["id"]; 
	$lineData = array($sid, $fno, $rf['id'], $varf, 'p','ລາຄາພື້ນຖານ',$rf['faps'],'','n/a', 'n/a',''); 
    fputcsv($f, $lineData, $delimiter); 
  }
}
*/
//******************************** DRAFT of VARIABLES - include condition for discount, and normal and urgent delivery
 $con->set_charset("utf8");
 $sqluserex="SELECT * FROM tbusers ORDER BY id DESC LIMIT 1";  //Jus one record as example
 $ruser=mysqli_query($con,$sqluserex) or die(mysqli_connect_error());
	if(mysqli_num_rows($ruser)>0){ 
	 while($r=mysqli_fetch_array($ruser)){ 
		$namelao=$r["namelao"];
		$nameeng=$r["nameeng"];
		$snlao=$r["snamelao"];
		$sneng=$r["snameeng"];
		$dbirth=$r["dbirth"];
		$gender=$r["gender"];
		$phone=$r["mphone"]; 
		$email=$r["email"];
		$addr=$r["address"];
		$utype=$r["usertype"];
		//$cdate=date('Y-m-d');
		$lineData = array($namelao, $nameeng, $snlao, $sneng, $dbirth, $gender, $phone, $email, $addr, $utype);
        fputcsv($f, $lineData, $delimiter); 
	} //******* End of while 
 } //******** End of If 	  
 //**************************************************************
// Move back to beginning of file 
fseek($f, 0); 
 
// Set headers to download file rather than displayed 
header('Content-Encoding: UTF-8');
header('Content-Type: text/csv; charset=UTF-8'); 
header('Content-Disposition: attachment; filename="' . $filename . '";'); 
echo "\xEF\xBB\xBF";
// Output all remaining data on a file pointer 
fpassthru($f); 
 
// Exit from file 
exit();
?>
