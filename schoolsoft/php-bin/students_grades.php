  <style>
	  .lhead{
		 margin-left: 50px; 
		 vertical-align: middle; 
	  }
	  .lhead input[type=date], select {
		  height: 45px;
		  width: 300px; 
	  }
	  .custb, table{
		  margin-left: 50px; 
	  }
	  .custb, th{
		  align-content: center;
	  }
	  .abtb{
		border: solid 1px black;  
	  }
	  .abtb, td{ 
		font-weight: normal;  
	  }
	  .atbor{
		  border-left: solid 1px #73c2fb;
	  }
	  .tmg{
		  border-top: solid 1px #73c2fb;
		  border-left: solid 1px #73c2fb;
	  }
  </style>

  <?php
      list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr) = Userinfo($un,$pw,$con); // Teacher
      list($sid,$slevel,$starea,$sclass,$sayear) = Currentstudent($id,$con);
      
   ?>
    <div align="center" class="cominpt" style="width: 100%; float: left; display: inline-block">
		<div align="left" class="lhead">
			<h2 align="left" class="lhead">ຜົນການສອບເສງ (ຄະແນນ)</h2>
			 <p>ຄະແນນ ຈະສະແດງໃນສອງຮູບແບບ ຄື: 1) ລາຍເດືອນ, ຖ້າເລືອກ ວີຊາ ແລະ 2) ລາຍວິຊາ, ຖ້າເລືອກ ປະຈໍາ (ເດືອນ, ເທີມ, ...)</p>
			<form id="frabid" action="" method="post" style="margin-bottom: 20px;">
				<div class="row g-2 align-items-center" style="margin-left: 50px;">
				 <div class="col-12 mb-2 d-flex align-items-center">
					<label for="grade_subject" class="form-label fw-bold me-2" style="min-width: 70px;">ວິຊາ</label>
					<select name="grade_subject" id="grade_subject" class="form-select" style="width: 320px; height: 45px;">
					  <option value="non">&nbsp;</option>
					  <?php SelectSubjectGrade($sid, $con); ?>
					</select>
				 </div>

				 <div class="col-12 mb-2 d-flex align-items-center">
					<label for="gradetype" class="form-label fw-bold me-2" style="min-width: 70px;">ປະຈໍາ</label>
					<select name="gradetype" id="gradetype" class="form-select" style="width: 320px; height: 45px;">
						<option value="non">&nbsp;</option>
						<?php SelectGradeType($sid, $con); ?>
					</select>&nbsp;&nbsp;
					<select name="gradefor" id="gradefor" class="form-select" style="width: 180px; height: 45px;">
						<!-- Add options here as needed -->
					</select>
					</div>
				</div>
			</form>
		</div>	
		<div style="width: 50%; float: left"> <!-- LEFT SIDE -->
		  <table class="table table-bordered table-striped" style="width: 95%; margin: 10px auto;">
			<thead>
				<tr>
					<th>ລດ</th>
					<th>ຄະແນນ ປະຈໍາ</th>
					<th>ສົກຮຽນ</th>
					<th>ວັນທີ</th>
					<th>ລາຍລະອຽດ</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sql = "SELECT * FROM tbgrades WHERE studid = '$sid' GROUP BY grade_type, gradefor ORDER BY grade_type, gradefor, gdate";
				$result = mysqli_query($con, $sql) or die(mysqli_error($con));
				$no = 1;
				while($row = mysqli_fetch_array($result)){
					$studidg = $row['studid'];
					$gradetype = htmlspecialchars($row['grade_type']);
					$gradefor = htmlspecialchars($row['gradefor']);
					$ayear = htmlspecialchars($row['ayear']);
					$gdate = htmlspecialchars($row['gdate']);

					$gtypelao = RgradeType($gradetype, $con);
					$fulldesc = $gtypelao . " " . $gradefor;

					$gradeid = $row['id']; // assuming there is an id column
					echo "<tr>";
					echo "<td align='center'>{$no}</td>";
					echo "<td>{$fulldesc}</td>";
					echo "<td>{$ayear}</td>";
					echo "<td>{$gdate}</td>";
					echo "<td align='center'>
							<a href='#' title='View Details' onclick=\"showGradeDetails('{$studidg}', '{$gradetype}', '{$gradefor}', '{$ayear}')\">
								<i class='fa fa-eye' style='color: #007bff; font-size: 18px;'></i>
							</a>
						</td>";
					echo "</tr>";
					$no++;
				}
				?>
			</tbody>
		</table>
		</div> <!-- End of LEFT SIDE -->	
		<!-- RIGHT SIDE -->
		<div style="width: 50%; float: right;"> <!-- RIGHT SIDE -->
			<div align="center" id="grade_details">&nbsp;</div> <!-- This will be filled with AJAX response -->	
		</div>
		
	</div> <!-- Main DIV -->

<script>
// SELECT - Gradefor: Function to handle the change of grade type
document.addEventListener('DOMContentLoaded', function() {
    const gradetype = document.getElementById('gradetype');
    const gradefor = document.getElementById('gradefor');
    if (!gradetype || !gradefor) {
        alert("gradetype or gradefor not found!");
        return;
    }

    gradetype.addEventListener('change', function() {
		//alert("gradetype changed to: " + this.value);
		gradefor.innerHTML = '<option value="non">&nbsp;</option>'; // Clear previous options

		// Populate gradefor based on the selected gradetype
      
		gradefor.innerHTML = '<option value="non">&nbsp;</option>'; // Use the same default as your HTML

        if (this.value === 'month') {
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
                gradefor.appendChild(opt);
            });
        } else if (this.value === 'term') {
            ['I', 'II', 'III', 'IV'].forEach(function(term) {
                const opt = document.createElement('option');
                opt.value = term;
                opt.text = term;
                gradefor.appendChild(opt);
            });
        } else if (this.value === 'semester') {
            ['1', '2'].forEach(function(sem) {
                const opt = document.createElement('option');
                opt.value = sem;
                opt.text = 'Semester ' + sem;
                gradefor.appendChild(opt);
            });
        } else if (this.value === 'year') {
            const opt = document.createElement('option');
            opt.value = '<?php echo Academicyear($con); ?>';
            opt.text = '<?php echo Academicyear($con); ?>';
            gradefor.appendChild(opt);
        }
	 
    });
});

// Show Grade Details
function showGradeDetails(studId, gradetype, gradefor, ayear) {
    // Implement the logic to show grade details
    alert("Show details for student ID: " + studId + ", Grade Type: " + gradetype + ", Grade For: " + gradefor + ", Academic Year: " + ayear);
	$.ajax({
		url: 'studentDataprocessing.php',
		type: 'POST',
		data: {
			studid: studId,
			gradetype: gradetype,
			gradefor: gradefor,
			ayear: ayear
		},
		success: function(response) {
			//document.getElementById('grade_details').innerHTML = response;
			 const gradeDetailsDiv = document.getElementById('grade_details');
            if (response && response.trim() !== "") {
                gradeDetailsDiv.innerHTML = response;
                gradeDetailsDiv.style.display = "block";
            } else {
                gradeDetailsDiv.innerHTML = "";
                gradeDetailsDiv.style.display = "none";
            }
		},
		error: function(xhr, status, error) {
			console.error("Error fetching grade details:", error);
			document.getElementById('grade_details').innerHTML = "Error loading details.";
		}
	});
}

</script>
