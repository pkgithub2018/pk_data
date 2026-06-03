<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
 $tcher_id=$_SESSION["uid"];

 // Teacher's information ******************
	  list($uid) = Userinfo($guname,$gpsw,$con); // Get user id - Teacher ID
	 
	 // ADD ITEMS INTO Select: class
	   echo "<script> 
	   var tcl = document.getElementById('clnameid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tcl.options.add(opt_non);
	    </script>";
	 
      //$con->set_charset("utf8"); // SET FONT TO "utf-8"
  
	  $sqlutch = "SELECT subjid, classid, ayear FROM tbteaching WHERE userid='$uid' GROUP BY subjid, classid ORDER BY classid ASC";
	  $rtch = mysqli_query($con, $sqlutch) or die(mysqli_connect_error());
	  while($r=mysqli_fetch_array($rtch)){
		  $subj = $r["subjid"];
		  $clid = $r["classid"];
		  //echo "My Class: ".$clid."<br>";
		  
		  list($dgid, $stid) = Rdgsarea($clid, $con);
		  $ayear = $r["ayear"];
		  list($subjnamelao, $subeng) = Rsubjectname($subj, $con);
		  $classname = Rclassname($clid, $con);
		  $dgname = Rdgree($dgid, $con);
		  $saname = Rsarea($stid, $con);
		  
		  $classname = $subjnamelao." - ".$classname.", ".$saname.", ".$dgname;
		  
		 echo "<script>
	      var cid ='$clid';
		  var cname = '$classname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      tcl.options.add(opt_non);
	      </script>"; 
		  
	  } 
	 
	 // ADD ITEMS INTO Select: Time of attendance
	 echo "<script>
		         var seltimeat = document.getElementById('timeatid');
				 var opnone = document.createElement('option');
				     opnone.value = '';
					 opnone.text = '';
					 seltimeat.options.add(opnone);
		      </script>";
		 
		 $sqlttm = "SELECT id,tchtime FROM tbtchtime WHERE tcharea='2'"; // for College
 			$rtm = mysqli_query($con, $sqlttm) or die(mysqli_connect_error());
 			while($r=mysqli_fetch_array($rtm)){
	 			$tid = $r["id"];
	 			$tcht = $r["tchtime"];
				//echo "<option value='$tid'>".$tcht."</option>";
				echo "<script>
				        var tmid = '$tid';
						var tm = '$tcht';
						var op = document.createElement('option');
						    op.value = tmid;
							op.text = tm;
							seltimeat.options.add(op);
					  </script>";
			} // End of while

	 
	
?> 

<div align="center" style="width: 100%; background-color: white; display:flex;"> 
 <div align="left" style="width: 35%; margin: 50px 80px 30px 30px"> <!-- top, right , bottom  and left -->

<div class="container mt-4">
	<!-- Heading -->
	<h2>ຜົນການ ສອບເສັງ</h2>
	<p>ກະລຸນາ ເລືອກວິຊາ ຫ້ອງຮຽນ ແລະ ປະຈໍາເດືອນ, ປີ ແລະ ພາກຮຽນ</p><br>
	<!-- Search Form -->
    <form method="post" action="">
		<div class="row mb-3">
            <div class="col-md-12">
            
                <label for="clnameid" class="form-label fw-bold" style="min-width: 60px;">ຫ້ອງຮຽນ</label>
                <select class="form-select mb-2" id="clnameid" name="clnameid">
                    <option value=""></option>
                    <?php SelectClass($uid, $con); ?>
                </select>

                <label for="gradeType" class="form-label fw-bold" style="min-width: 70px;">ປະເພດຄະແນນ</label>
                <select class="form-select mb-2" id="gradeType" name="gradeType">
                    <option value=""></option>
                    <option value="month">ປະຈໍາເດືອນ</option>
                    <option value="term">ເທີມ</option>
                    <option value="semester">ພາກຮຽນ</option>
                    <option value="year">ປະຈໍາປີ</option>
                </select>

                <label for="gradeFor" class="form-label fw-bold" style="min-width: 60px;">ປະຈໍາເດືອນ, ເທີມ, ພາກຮຽນ ແລະ ປະຈໍາປີ</label>
                <select class="form-select mb-1" id="gradeFor" name="gradeFor">
                    <option value=""></option>
                    <!-- Options will be populated based on the selected grade type. See below -->
                </select>
                <label for="created_date" class="form-label fw-bold" style="min-width: 60px;">ວັນທີ</label>
                <input type="date" class="form-control mb-2" style="width: 170px;" id="created_date" name="created_date">
            </div>
        </div>
    </form>
</div>
  
 </div> <!-- end of Div - left side -->

 <!-- Right side ******** -->   
 <div id="rightside" style="width: 60%; margin: 50px 80px 30px 0; background-color: white; padding: 10px; display: flex; flex-direction: column; align-items: flex-start;">
   <div id="gradeRecord" style="margin-bottom: 20px;">
     <!-- Results from teachersDataprocessing.php will be shown here -->
      <?php 
        if(!isset($_GET['gdatelist']) && empty($_GET['gdatelist'])) {  // In case no specifif grade record is requested(No link)
            // If no specific grade record is requested, show the grade list
            GradeList($tcher_id, $con);
        } else {
            // If a specific grade record is requested, show the grade form
            $gdatelist = $_GET['gdatelist'];
            $subjid = $_GET['subjid'];
            $clsid = $_GET['clsid'];
            $gtype = $_GET['gtype'];
            $gradefor = $_GET['gradefor'];
            $schoolyear = $_GET['schoolyear'];
            GradeForm($gid, $tcher_id, $clsid, $gtype, $gradefor, $gdatelist, $con);
        }
       ?>
  </div>
  <!--
  <div class="container mt-4">

    <h4>ລາຍການບັນທຶກ</h4>
    <table id="paymentTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>ລດ</th>
                <th>ວັນທີ</th>
                <th>ວິຊາ</th>
                <th>ຫ້ອງຮຽນ ແລະ ສາຂາວິຊາ</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            
		  <tr>
			<td>1</td>
			<td>John Doe</td>
			<td>2023-10-01</td>
			<td>$100.00</td>
			<td>Payment for services rendered</td>
		  </tr>
		  <tr>
			<td>2</td>
			<td>Jane Smith</td>
			<td>2023-10-02</td>
			<td>$150.00</td>
			<td>Payment for tutoring services</td>
		  </tr>
		  <tr>
			<td>3</td>
			<td>Michael Johnson</td>
			<td>2023-10-03</td>
			<td>$200.00</td>
			<td>Payment for group study session</td>
		  </tr>
        </tbody>
    </table>
</div>
 -->
 
</div>
</div> <!-- End of main -->

<script>
$(document).ready(function() {
    $('#paymentTable').DataTable({
        responsive: true,
        language: {
            search: "🔍 Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ payments"
        }
    });
});

// Function to handle the change of grade type
document.addEventListener('DOMContentLoaded', function() {
    const gradeType = document.getElementById('gradeType');
    const gradeFor = document.getElementById('gradeFor');

    gradeType.addEventListener('change', function() {
        gradeFor.innerHTML = '<option value=""></option>'; // Clear previous options

        if (this.value === 'month') {
            // List months in Lao and English
            const months = [
                {value: '01', text: 'ມັງກອນ (January)'},
                {value: '02', text: 'ກຸມພາ (February)'},
                {value: '03', text: 'ມີນາ (March)'},
                {value: '04', text: 'ເມສາ (April)'},
                {value: '05', text: 'ພຶດສະພາ (May)'},
                {value: '06', text: 'ມິຖຸນາ (June)'},
                {value: '07', text: 'ກໍລະກົດ (July)'},
                {value: '08', text: 'ສິງຫາ (August)'},
                {value: '09', text: 'ກັນຍາ (September)'},
                {value: '10', text: 'ຕຸລາ (October)'},
                {value: '11', text: 'ພະຈິກ (November)'},
                {value: '12', text: 'ທັນວາ (December)'}
            ];
            months.forEach(function(month) {
                const opt = document.createElement('option');
                opt.value = month.value;
                opt.text = month.text;
                gradeFor.appendChild(opt);
            });
        } else if (this.value === 'term') {
            // List I, II, III, IV for terms
            ['I', 'II', 'III', 'IV'].forEach(function(term) {
                const opt = document.createElement('option');
                opt.value = term;
                opt.text = term;
                gradeFor.appendChild(opt);
            });
        } else if (this.value === 'semester') {
            // List 1, 2 for semesters
            ['1', '2'].forEach(function(sem) {
                const opt = document.createElement('option');
                opt.value = sem;
                opt.text = 'Semester ' + sem;
                gradeFor.appendChild(opt);
            });
        } else if (this.value === 'year') {
            // Only one option for year
            const opt = document.createElement('option');
            opt.value = '<?php echo Academicyear($con); ?>'; // Get current academic year
            opt.text = '<?php echo Academicyear($con); ?>'; // Display current academic year
            gradeFor.appendChild(opt);
        }
    });
});

// set background color for select options
document.addEventListener('DOMContentLoaded', function() {
    // Select all select elements inside the form
    document.querySelectorAll('.form-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            if (this.value !== "") {
                this.style.backgroundColor = "#e3f2fd"; // Light blue
            } else {
                this.style.backgroundColor = ""; // Reset
            }
        });
        // Initial check on page load
        if (sel.value !== "") {
            sel.style.backgroundColor = "#e3f2fd";
        }
    });

	// For the date input
    var dateInput = document.getElementById('created_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value !== "") {
                this.style.backgroundColor = "#e3f2fd";
            } else {
                this.style.backgroundColor = "";
            }
        });
        // Initial check on page load
        if (dateInput.value !== "") {
            dateInput.style.backgroundColor = "#e3f2fd";
        }
    }
});

// Date on change event_Grade Record
document.getElementById('created_date').addEventListener('change', function() {
	var gDate = this.value;
	var claId = document.getElementById('clnameid').value;
	var gType = document.getElementById('gradeType').value;
	var gFor = document.getElementById('gradeFor').value;
    var tcherId = '<?php echo $tcher_id; ?>'; // Get the teacher ID from PHP_SESSION
	// You can add any additional logic here if needed
	
	$.ajax({
		url: 'teachersDataprocessing.php',
		type: 'POST',
		data: {
			claId: claId,
			gType: gType,
			gFor: gFor,
			gDate: gDate,
			tcherId: tcherId
		},
		success: function(response) {
			console.log('Response:', response);
            $('#gradeRecord').html(response);
			// You can update the UI or handle the response as needed
		},
		error: function(xhr, status, error) {
			console.error('Error:', error);
		}
	});
});
</script>
