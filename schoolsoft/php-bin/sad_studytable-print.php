<?php
require("../tcpdf_min/tcpdf.php");
 include("connection.php");
 //include("connectionlocal.php");
 include("supports.php");
 //******************************** Get data from the link
$class=$_GET["cl"];
$dgree=$_GET["dg"];
$starea=$_GET["sta"];
/*
$fdate=$_GET["fdate"];
$mid=$_GET["mid"];
*/
?>
<?php
 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'mm', 'A4', true, 'UTF-8', false);
 //PDF_PAGE_FORMAT='A4'
 //PDF_UNIT='mm' ***** minimetre
 $pdf->SetPrintHeader(false); //************** To remove line on top
 $pdf->AddPage('L'); //******* Set page as landscape: AddPage('L')
 $pdf->SetMargins(10,5); // left, top - it is applied to the whole page
 $fontname = TCPDF_FONTS::addTTFfont('../tcpdf_min/fonts/Phetsarath OT.ttf', 'TrueTypeUnicode', '', 96);
 $pdf->SetFont('Phetsarath OT','B',8); //******* Arial
 /*
   Get FA title
 */
 $sqltch="SELECT * FROM tbteaching";
 $rtch=mysqli_query($con,$sqltch) or die(mysqli_connect_error());
 list($id,$userid,$subjid,$classid, $tday, $ttime, $smst, $ay)=mysqli_fetch_array($rtch);
 
// Semester 
switch($smst){
	case 1:
   $smst = "I";
   break;
		
   case 2:
   $smst = "II";
   break;
} // End of switch

 $html = '<style>
           .mdv{
		      width: 100%;
			  }
		   .cntdv{
		     color: black; 
			 font-size: 12pt; 
		   }
		   .tbh{
		     width:50%;
			 font-size: 10pt;
		   }
		   .frw{
		    width: 15%; 
			vertical-align: middle;
		   }
         </style>';

 $html.='<div align="center" class="mdv">
		   <span class="cntdv">ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ <br>
		     ສັນຕິພາບ ເອກະລາດ ປະຊາທິປະໄຕ ເອກະພາບ ວັດທະນາຖາວອນ<br>
			 -----===000===-----
		   </span><br>  
        </div>';

 $html.='<table class="tbh">';
 $html.='<tr><td rowspan="3" class="frw" align="right"><img src="../images/logo.jpg"></td><td align="left">ກະຊວງ ສຶກສາທິການ ແລະ ກິລາ</td></tr>';
 $html.='<tr><td align="left">ກົມ ອະຊີວະສຶກສາ</td></tr>';
 $html.='<tr><td align="left">ວິທະຍາໄລ ເຕັກນິກ ບີໄອເອສ ບຸນເກີດ</td></tr>';
 $html.='</table><br><br>';
 //$pdf->MultiCell(55, 50, '[LEFT] '.'Good Morning, Phaykeo', 1, 'L', 1, 0, '', '', true);
 $pdf->writeHTML($html, false, false, false, false, '');

// ob_end_clean();
//****************** Loop the data
$mhtml='<style>
         .sttble{ 
             width:100%; 
          }
		  .sttble tr, th, td{ 
            border: 1px solid #666;
			font-size: 10pt;
          }
		  .sttble th{
		    background-color: lightgrey;
		  }
		  .sttble td{
			valign: middle; 
		  }
		  .phead{
		   font-size: 13pt;
		  }
		</style>';

  $dgname = Rdgree($dgree, $con);
  $starename = Rsarea($starea, $con);
  $clname = Rclassname($class, $con);
 $mhtml.='<p align="center" class="phead">ຕາຕະລາງຮຽນ ປະຈໍາພາກ '. $smst.'<br>ສົກຮຽນ '.$ay.'<br>ຂະແໜງ: '.$starename.'-'.$dgname.'<br>ຫ້ອງ: '.$clname.'</p>';		

/* ******************************************* */ 
 //$pdf->SetMargins(20,10); //left and top. For right and bottom is authomatically set the same size as left and top
 $mhtml.='<table align="center" class="sttble">';
 $mhtml.='<tr><th>ເວລາ</th><th>ຈັນ</th><th>ຄານ</th><th>ພຸດ</th><th>ພະຫັດ</th><th>ສຸກ</th><th>ເສົາ</th><th>ອາທິດ</th>';
 $mhtml.='</tr>';
   $sqlttime = "SELECT teachtime FROM tbteaching WHERE classid='$class' ORDER BY teachtime ASC";
    $rttime = mysqli_query($con, $sqlttime) or die(mysqli_connect_error());
    while($r=mysqli_fetch_array($rttime)){
	  $ttime = $r["teachtime"];
	  $tchtime = Rttime($ttime, $con);
	     // Subject id
		  $sub1 = Studytablecl(1, $ttime, $con); // Monday
		  $sub2 = Studytablecl(2, $ttime, $con); // Tuesday
		  $sub3 = Studytablecl(3, $ttime, $con); // Wednsday
		  $sub4 = Studytablecl(4, $ttime, $con); // Thursday
		  $sub5 = Studytablecl(5, $ttime, $con); // Friday
		  $sub6 = Studytablecl(6, $ttime, $con); // Satu
		  $sub7 = Studytablecl(7, $ttime, $con); // Sun
		
		// Subject name
		 list($sublao1, $subeng1) = Rsubjectname($sub1, $con);
		 list($sublao2, $subeng2) = Rsubjectname($sub2, $con);
		 list($sublao3, $subeng3) = Rsubjectname($sub3, $con);
		 list($sublao4, $subeng4) = Rsubjectname($sub4, $con);
		 list($sublao5, $subeng5) = Rsubjectname($sub5, $con);
		 list($sublao6, $subeng6) = Rsubjectname($sub6, $con);
		 list($sublao7, $subeng7) = Rsubjectname($sub7, $con);
		 // Teachers
		 $tname1 = Rtchername($sub1, $con);
		 if(!empty($tname1)){
		  $tname1 = "(ອຈ. ".$tname1.")"; 
		 }
		 
		 $tname2 = Rtchername($sub2, $con);
		 if(!empty($tname2)){
		  $tname2 = "(ອຈ. ".$tname2.")"; 
		 }
		 $tname3 = Rtchername($sub3, $con);
		 if(!empty($tname3)){
		  $tname3 = "(ອຈ. ".$tname3.")"; 
		 }
		 $tname4 = Rtchername($sub4, $con);
		 if(!empty($tname4)){
		  $tname4 = "(ອຈ. ".$tname4.")"; 
		 }
		 $tname5 = Rtchername($sub5, $con);
		 if(!empty($tname5)){
		  $tname5 = "(ອຈ. ".$tname5.")"; 
		 }
		 
		 $tname5 = Rtchername($sub5, $con);
		 if(!empty($tname5)){
		  $tname5 = "(ອຈ. ".$tname5.")"; 
		 }
		 
		 $tname5 = Rtchername($sub5, $con);
		 if(!empty($tname5)){
		  $tname5 = "(ອຈ. ".$tname5.")"; 
		 }
		 
		 $tname6 = Rtchername($sub6, $con);
		 if(!empty($tname6)){
		  $tname6 = "(ອຈ. ".$tname6.")"; 
		 }
		 
		 $tname7 = Rtchername($su7, $con);
		 if(!empty($tname7)){
		  $tname7 = "(ອຈ. ".$tname7.")"; 
		 }
		
	 $mhtml.='<tr><td>'.$tchtime.'</td><td align: left>'.$sublao1.'<br>'.$tname1.'</td><td>'.$sublao2.'<br>'.$tname2.'</td><td>'.$sublao3.'<br>'.$tname3.'</td><td>'.$sublao4.'<br>'.$tname4.'</td><td>'.$sublao5.'<br>'.$tname5.'</td><td>'.$sublao6.'<br>'.$tname6.'</td><td>'.$sublao7.'<br>'.$tname7.'</td></tr>';	
    } // End of while
 $mhtml.='</table><br><br><br>';
 
// Bottom table *****************
 $pspace = "______/______/_________";
 $bmtbl = '<style>
             .tbbtm{
		       width:100%;
			   font-size: 12pt;
		     }
           </style>';
 $bmtbl.='<table class="tbbtm">';
 $bmtbl.='<tr><td></td><td align="right">ວິທະຍາໄລ ເຕັກນິກ ບີໄອເອສ ບຸນເກີດ, ວັນທີ:'.$pspace.'</td></tr>';
 $bmtbl.='<tr><td></td><td align="center">ຜູ້ອໍານວຍການ</td></tr>'; 
 $bmtbl.='</table>';
 $pdf->writeHTML($mhtml);
 $pdf->writeHTML($bmtbl);
// $pdf->Cell(120, 10, $access, 0, 'C'); //***** Cell width=120 and height=10- Set 0 for border and C-Centre
/* $x = 30;
 $y = 200;
 $w = 145;
 $h = 35;
 $pdf->Image('../images/rectangle.jpg',$x, $y, $w, $h, 'JPG');
*/ 
// We can a new page as below
/*
$pdf->SetMargins(50, 20);  // lelf, top, for right and bottom margin, it automatically set the same as left and top margin
$pdf->AddPage(); // Add new page

$nhtml='<style>
           .ntb{
		     border: 1px solid #666;
		   }
         </style>';
$nhtml.='<table class="ntb">
           <tr><td>Hello, New table</td></tr>
         </table>';
$pdf->writeHTML($nhtml);
*/
 ob_end_clean();
 $pdf->Output();
 ?>