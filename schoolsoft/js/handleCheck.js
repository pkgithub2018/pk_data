// Check absence status and handle checkbox toggle for attendance
function handleCheckboxAttendance(checkbox, aid, tcher, dateatt, timeatt, classid, subjid, ayear) { // Switch button in list of usergroups
        // Display an alert when the checkbox is toggled
        var stid = checkbox.id;
        var astatus = checkbox.checked;
       // alert("Student: " + stid + ", Att ID: " + aid);
       if (astatus== true) {
            astatus = "1";  // Assuming "1" means absent
        } else {
            astatus = "0";
        }
        // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "teachersDataprocessing.php", // Ensure this path is correct
        data: { aid: aid, stid: stid, astatus: astatus, tcher: tcher, dateatt: dateatt, timeatt: timeatt, classid: classid, subjid: subjid, ayear: ayear },
        success: function (response) {
            console.log("Server Response:", response);
            //alert("Attendance updated successfully for Student ID: " + stid);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
        
   }

   // Check absence with reason
  
   function handleCheckboxAbsenceReason(checkbox, aid) {
        var stid = checkbox.id;
        var astatus = checkbox.checked;
       //alert("Student: " + stid + ", Attendance ID: " + aid);
        if (astatus == true) {
            astatus = "1";  // Assuming "1" means absent without reason
        } else {
            astatus = "0";
        }
        // Send the AJAX request
        $.ajax({
            type: "POST",
            url: "teachersDataprocessing.php", // Ensure this path is correct
            data: { aidreason: aid, astatusreason: astatus },
            success: function (response) {
                console.log("Server Response:", response);
              //  alert("Success-Att ID: " + aid + ", Status: " + astatus);
                $("#divTest").html(response); // Make it happy - Update the div with the response
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
   }


function handleCheckInputGradeText(InputGrade, gvalue, studentId, tcherid, gsub, clsid, gtype, gfor, gdate) {
    //alert("Grade-Update: " + gvalue + ", Student ID: " + studentId + ", Teacher ID: " + tcherid + ", Subject ID: " + gsub + ", Class ID: " + clsid + ", Grade Type: " + gtype + ", Grade For: " + gfor + ", Grade Date: " + gdate);
   // console.log("Grade-Update: " + gvalue + ", Student ID: " + studentId + ", Teacher ID: " + tcherid + ", Subject ID: " + gsub + ", Class ID: " + clsid + ", Grade Type: " + gtype + ", Grade For: " + gfor + ", Grade Date: " + gdate);
    const inputName = InputGrade.name; // Get the name of the input field
    const inputId = InputGrade.id; // Student ID
    const gradeValue = gvalue; // Grade in text: A, B, C, D, F
    const gnumInput = document.querySelector('input[name="gnum[' + studentId + ']"]');
    const gnumValue = gnumInput ? gnumInput.value : ''; // Grade in number: 0-100
    const sid = studentId; // Assuming studentId is passed correctly
    const teacherid = tcherid; // Teacher ID
    const gradesubject = gsub; // Subject ID
    const classid = clsid; // Class ID
    const gradetype = gtype; // Grade Type
    const gradefor = gfor; // Grade For
    const gradedate = gdate; // Grade Date
    
    const formData = new FormData();
    //formData.append("inputName", inputName);
    
    formData.append("gradeValue", gradeValue);
    formData.append("gnumValue", gnumValue); // Append the number grade
    formData.append("sid", sid);
    formData.append("teacherid", teacherid);
    formData.append("gradesubject", gradesubject);
    formData.append("classid", classid);
    formData.append("gradetype", gradetype);
    formData.append("gradefor", gradefor);
    formData.append("gradedate", gradedate);

    fetch("teachersDataprocessing.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("Server response:", data); // Useful for debugging
        //alert(data); // Display the server response in an alert
       //  alert("Updated Grade: " + gradeValue + ", Student ID: " + sid + "\nServer Response: " + data);
    })
    .catch(error => {
        console.error("Fetch error:", error);
    });
    
}
 