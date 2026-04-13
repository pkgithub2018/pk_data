
    // Usergroup **********
    function handleCheckboxChange(checkbox) { // Switch button in list of usergroups
        // Display an alert when the checkbox is toggled
        var checkboxId = checkbox.id;
        var checkboxChecked = checkbox.checked;
       // alert("Checkbox ID up: " + checkboxId + ", Checked: " + checkboxChecked);
    
        // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { groupid: checkboxId, chboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
        
   }

    // Users - Enabled=yes/no *********
    function handleUserCheckboxChange(checkbox) { // Switch button in list of users
        // Display an alert when the checkbox is toggled
        var checkboxId = checkbox.id;
        var checkboxChecked = checkbox.checked;
       // alert("Checkid-User: " + checkboxId + ", Checked: " + checkboxChecked);
    
        // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { userid: checkboxId, userchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
          //  alert("Data sent successfully: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
    }
// Locations - Enabled=yes/no *********
function handleLocationCheckboxChange(checkbox) { // Switch button in list of locations
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Checkid-Location: " + checkboxId + ", Checked: " + checkboxChecked);

    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { locationid: checkboxId, locationchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
          //  alert("Data sent successfully: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
// Provinces **********
function SelectProvinceOnChange(select) { // Switch button in list of provinces
    var selectedValue = select.value;
   // alert("Selected Province ID: " + selectedValue);
    // Send the AJAX request
    
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { provinceid: selectedValue },
        success: function (response) {
            console.log("Server Response:", response);
            //alert("Update the district select options: " + response);
            $("#district").html(response); // Update the district select options
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });

}

// Countries **********
function handleCountryCheckboxChange(switchbar){
   var swichId = switchbar.id;
   var swtichChecked = switchbar.checked; 
    alert("hello_Switch" + swichId);
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { countryid: swichId, countrystatus: swtichChecked },
        success: function (response) {
            console.log("Server Response:", response);
          //  alert("Data sent successfully: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Products - Enabled=yes/no *********
function handleProductCheckboxChange(checkbox) { // Switch button in list of products 
   
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
    //alert("Product ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { productid: checkboxId, productchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-PK: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Product groups - Enabled=yes/no *********
function handleProductGroupCheckboxChange(checkbox) { // Switch button in list of product groups
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
    //alert("Product Group ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { productgroupid: checkboxId, productgroupchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-PG-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Product units - Enabled=yes/no *********
function handleProductUnitCheckboxChange(checkbox) { // Switch button in list of product units
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
    //alert("Product Unit ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { productunitid: checkboxId, productunitchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-PU-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Conveyance - Enabled=yes/no *********
function handleConveyanceCheckboxChange(checkbox) { // Switch button in list of conveyances
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Conveyance ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { conveyanceid: checkboxId, conveyancechboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-C-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Inspection Methods - Enabled=yes/no ********* 
function handleInspectionMethodCheckboxChange(checkbox) { // Switch button in list of inspection methods
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Inspection Method ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { inspectionmethodid: checkboxId, inspectionmethodchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-IM-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Treatment Methods - Enabled=yes/no ********* 
function handleTreatmentMethodCheckboxChange(checkbox) { // Switch button in list of treatment methods
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Treatment Method ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { treatmentmethodid: checkboxId, treatmentmethodchboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-TM-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Entity Types - Enabled=yes/no ********* 
function handleEntityTypeCheckboxChange(checkbox) { // Switch button in list of entity types
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Entity Type ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { entitytypeid: checkboxId, entitytypechboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-ET-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Modules - Enabled=yes/no *********
function handleModuleCheckboxChange(checkbox) { // Switch button in list of modules 
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
     //alert("Module ID: " + checkboxId + ", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { moduleid: checkboxId, modulechboxstatus: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-M-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Permits-delete: Enable=yes/no *********
function handlePermitDeleteCheckboxChange(checkbox) { // Switch button in list of permits 
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;

    // Split the checkbox ID
      /*
       var parts = checkboxId.split('-');
       var groupId = parts[0]; 
       var moduleId = parts[1];
    */
   // alert("Permit-delete ID_Update: " + checkboxId +", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { permitid_delete: checkboxId, permitchboxstatus_delete: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-Permits-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Permits-edit: Enable=yes/no *********
function handlePermitEditCheckboxChange(checkbox) { // Switch button in list of permits 
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;

   // alert("Permit-edit ID_Update: " + checkboxId +", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { permitid_edit: checkboxId, permitchboxstatus_edit: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-Permits-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}   
// Permits-add : Enable=yes/no ********* 
function handlePermitAddCheckboxChange(checkbox) { // Switch button in list of permits 
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Permit-add ID_Update: " + checkboxId +", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { permitid_add: checkboxId, permitchboxstatus_add: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-Permits-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Permits-read: Enable=yes/no ********* 
function handlePermitReadCheckboxChange(checkbox) { // Switch button in list of permits 
    // Display an alert when the checkbox is toggled
    var checkboxId = checkbox.id;
    var checkboxChecked = checkbox.checked;
   // alert("Permit-read ID_Update: " + checkboxId +", Checked: " + checkboxChecked);
    
    // Send the AJAX request
    $.ajax({
        type: "POST",
        url: "php-bin/users_dataprocess.php", // Ensure this path is correct
        data: { permitid_read: checkboxId, permitchboxstatus_read: checkboxChecked },
        success: function (response) {
            console.log("Server Response:", response);
           // alert("Data sent successfully-Permits-Pk: " + response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}