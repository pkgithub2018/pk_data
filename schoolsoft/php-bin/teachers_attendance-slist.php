
<?php 
include("connection.php");
include("supports.php");

// Open attendance form for teachers
$tcher = $_POST["tch"];
$classid = $_POST["cls"];
$adate = $_POST["adate"];
$atime = $_POST["atime"];
$subjid = $_POST["subj"]; 
$ayear = $_POST["ayr"];	


//echo "Hello, Class: ".$classid;
if(!empty($tcher) && !empty($classid) && !empty($adate) && !empty($atime) && !empty($subjid) && !empty($ayear)) {
    
    list($dgid, $stid) = Rdgsarea($classid, $con);
        $dgname = Rdgree($dgid, $con);
        $staname = Rsarea($stid, $con);
        AttendList($tcher, $dgname, $staname, $classid, $subjid, $ayear, $adate, $atime, $con);   // Data form for student attendance
} 
 
?>
