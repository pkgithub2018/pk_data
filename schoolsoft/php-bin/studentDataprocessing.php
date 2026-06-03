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

$studid = $_POST['studid'];
$gradetype = $_POST['gradetype'];
$gradefor = $_POST['gradefor'];
$ayear = $_POST['ayear'];

if(!empty($studid) && !empty($gradetype) && !empty($gradefor) && !empty($ayear)){
 $sql = "SELECT * FROM tbgrades WHERE studid = '$studid' AND grade_type = '$gradetype' AND gradefor = '$gradefor' AND ayear = '$ayear' GROUP BY grade_type, gradefor, subjid";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));
  if (mysqli_num_rows($result) > 0) {
      list($id,$uname,$psw,$namel, $namee,$snamel, $snammee) = Userbyid($studid, $con);
      $fname = $namel . " " . $snamel;

      $gradetype = RgradeType($gradetype, $con);
      $gftype = $gradetype." ".$gradefor;

      echo "<h4>ລາຍລະອຽດ ຂອງ ຄະແນນ</h4>";
      echo "<h5>". $gftype. ", ສົກຮຽນ ". $ayear ."</h5>";

      echo "<table>";
      echo "<tr><th style='padding:18px;'>ວິຊາ</th><th style='padding:8px;'>ຄະແນນ</th><th style='padding:8px;'>ເກຣດ</th></tr>";
      while ($row = mysqli_fetch_assoc($result)) {
          $subject = $row['subjid'];
          list($sublao, $subeng) = Rsubjectname($subject, $con);
          $subject_name = $sublao . " (" . $subeng . ")";
          $gradenum = $row['grade_num'];
          $gradetext = $row['grade_text'];

          echo "<tr>";
          echo "<td>" . $subject_name . "</td>";
          echo "<td align='center'>" . $gradenum . "</td>";
          echo "<td align='center'>" . $gradetext . "</td>";
          echo "</tr>";
      }
      echo "</table>";
        
    } else {
        echo "<h3>No grade details found for the specified criteria.</h3>";
    }
    
}


?>
