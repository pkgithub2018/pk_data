<?php 

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/
include("connection.php");
include("supports.php"); // DON'T INCLUDE supports.php - IT IS NOT NEEDED HERE

// ABSENCE**************
$aid = $_POST["aid"];
$stuid = $_POST["stid"];
$astatus = $_POST["astatus"];
$tcherid = $_POST["tcher"];
$dateatt = $_POST["dateatt"];
$timeatt = $_POST["timeatt"];
$classid = $_POST["classid"];
$subjid = $_POST["subjid"];
$ayear = $_POST["ayear"];

if($astatus == 0){
 
  $sqldel = "DELETE FROM tbattendance WHERE id = '$aid'";
  $result = mysqli_query($con, $sqldel);    
 
} else if($astatus == 1) {
  $sqladd ="INSERT INTO tbattendance(uid, teacherid, subj, cls, adate, atime, acyear, absence, absencenor) VALUES('$stuid', '$tcherid', '$subjid','$classid', '$dateatt', '$timeatt','$ayear', '1','0')";
  $result = mysqli_query($con, $sqladd);
}

// With no reason, update the absence status
$attendanceId = $_POST["aidreason"];
$absenceStatus = $_POST["astatusreason"];

if($absenceStatus == 0 || $absenceStatus == 1) {
    $sqlUpdate = "UPDATE `tbattendance` SET `absencenor`='" . $absenceStatus . "' WHERE `id`='$attendanceId'";
    mysqli_query($con, $sqlUpdate) or die(mysqli_error($con));
} 

// Student's grades by teacher ******************
$classid = $_POST["claId"];
$gtype = $_POST["gType"];
$gfor = $_POST["gFor"];
$gdate = $_POST["gDate"];
$tcherid = $_POST["tcherId"]; // Get the teacher ID from AJAX request


if(!empty($classid) && !empty($gtype) && !empty($gfor) && !empty($gdate)) {
    $sqlGrades = "SELECT * FROM tbgrades WHERE classid = '$classid' AND grade_type = '".$gtype."' AND gradefor = '".$gfor."' AND gdate = '$gdate'";
    $resultGrades = mysqli_query($con, $sqlGrades) or die(mysqli_error($con));
    
    if(mysqli_num_rows($resultGrades) > 0) {
       print "<table>
            <tr>
                <th>Class ID</th>
                <th>Grade Type</th>
                <th>Grade For</th>
                <th>Date</th>
            </tr>
            <tr>
                <td>$classid</td>
                <td>$gtype</td>
                <td>$gfor</td>
                <td>$selectedDate</td>
            </tr>
          </table>";
    } else {
      
         $gid = 0; // Assuming you want to create a new grade entry
         // Call the function to display the grade form
         GradeForm($gid, $tcherid, $classid, $gtype, $gfor, $gdate, $con);
    }   
}

// GradeForm Submission: Save/Store the grades to tbgrades from link: handleCheckInputGrade****************
// var_dump($_POST); // Debugging to see the POST data

$gradetext = $_POST["gradeValue"]; // Get the input name from AJAX request
$gradenum = $_POST["gnumValue"]; // Get the grade number from AJAX request
$stid = $_POST["sid"];
$tcherid = $_POST["teacherid"];
$gsub = $_POST["gradesubject"];
$clsid = $_POST["classid"];
$gtype = $_POST["gradetype"];
$gfor = $_POST["gradefor"];
$gdate = $_POST["gradedate"];

$ayear = Academicyear($con); // Academic year

//echo "Grade-Update- Phaykeo: " . $gradetext . ", Student ID: " . $stid . ", Teacher ID: " . $tcherid . ", Subject ID: " . $gsub . ", Class ID: " . $clsid . ", Grade Type: " . $gtype . ", Grade For: " . $gfor . ", Grade Date: " . $gdate;

if(!empty($gradetext) && !empty($stid) && !empty($tcherid) && !empty($gsub) && !empty($clsid) && !empty($gtype) && !empty($gfor) && !empty($gdate)) {
    $sqlCheck = "SELECT * FROM tbgrades WHERE studid = '$stid' AND teacherid = '$tcherid' AND subjid = '$gsub' AND classid = '$clsid' AND grade_type = '".$gtype."' AND gradefor = '".$gfor."' AND gdate = '$gdate'";
    $resultCheck = mysqli_query($con, $sqlCheck) or die(mysqli_error($con));
    
    if(mysqli_num_rows($resultCheck) > 0) {
       // echo "INside Loop row>0 : " . $gradetext . ", Student ID: " . $stid . ", Teacher ID: " . $tcherid . ", Subject ID: " . $gsub . ", Class ID: " . $clsid . ", Grade Type: " . $gtype . ", Grade For: " . $gfor . ", Grade Date: " . $gdate;
        // Update existing grade
        
        $row = mysqli_fetch_array($resultCheck);
        $gradeId = $row['id']; // Assuming 'id' is the primary key of the grade record
        $gradenum_d = $row['grade_num']; // Get the existing grade number
        $gradetext_d = $row['grade_text']; // Get the existing grade text

         if((empty($gradenum_d) || $gradenum_d == 0) && empty($gradetext_d)) {
            $sqldelete = "DELETE FROM tbgrades WHERE id = '$gradeId'";
            mysqli_query($con, $sqldelete) or die(mysqli_error($con));
        } else {
            // Update the existing grade
            $sqlUpdate = "UPDATE tbgrades SET grade_num = '$gradenum', grade_text = '$gradetext' WHERE id = '$gradeId'";
            mysqli_query($con, $sqlUpdate) or die(mysqli_error($con));
           // echo "Grade updated successfully.";
        }
       
    } else {
       // echo "New record-PK : " . $gradetext . ", Student ID: " . $stid . ", Teacher ID: " . $tcherid . ", Subject ID: " . $gsub . ", Class ID: " . $clsid . ", Grade Type: " . $gtype . ", Grade For: " . $gfor . ", Grade Date: " . $gdate;
        //echo "Hello, New record.";
        $sqlInsertGrade = "INSERT INTO tbgrades (studid, teacherid, subjid, classid, grade_num, grade_text, grade_type, gradefor, ayear, gdate) VALUES ('$stid', '$tcherid', '$gsub', '$clsid', '$gradenum', '".$gradetext."', '".$gtype."', '".$gfor."', '".$ayear."','$gdate')";
        mysqli_query($con, $sqlInsertGrade) or die(mysqli_error($con));
        
    }
}

