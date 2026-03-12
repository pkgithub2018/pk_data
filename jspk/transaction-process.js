function passCommodity(pid, pname, spname, dproduct) { // for application in transaction.php
   
   // alert("Commodity with Name " + pname + " has been passed successfully.");
   document.querySelector('input[name="proid"]').value = pid;
    document.querySelector('input[name="proname"]').value = pname
    document.querySelector('input[name="name_oncertificate"]').value = pname;
    document.querySelector('input[name="scientific_name"]').value = spname;
   // document.querySelector('input[name="number_description"]').value = dproduct;
    // create hidden input to submit the commodity ID
    // close the modal
    // Close the modal (Bootstrap 5)
    var modalEl = document.getElementById('commodityModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }

}

function passMulitpleCommodity(pid, pname, sfname, dproduct) { // Multiple product for application in application.php
    // alert("Commodity with Name " + pname + " has been passed successfully.");
    const productIdField = document.getElementById('mp_product_id') || document.querySelector('input[name="product_id"]');
    const productNameField = document.getElementById('mp_product_name') || document.querySelector('input[name="product_name"]');
    const scientificNameField = document.getElementById('mp_scientific_name') || document.querySelector('#multipleProductsForm input[name="scientific_name"]') || document.querySelector('input[name="scientific_name"]');

    if (productIdField) {
        productIdField.value = pid;
    }
    if (productNameField) {
        productNameField.value = pname;
    }
    if (scientificNameField) {
        scientificNameField.value = sfname;
    }

    var modalEl = document.getElementById('mpProductSearchModal') || document.getElementById('productSearchModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }

}

// Function to pass importer details to the main form
function passImporter(importerId, importerName, importerAddress, importerZipcode, importerCountry) {
    // Set the importer ID if field exists
    const importerIdField = document.querySelector('input[name="importer_id"]');
    if (importerIdField) {
        importerIdField.value = importerId;
    }
    
    // Set the importer name
    const importerNameField = document.querySelector('input[name="importer_name"]');
    if (importerNameField) {
        importerNameField.value = importerName;
    }
    
    // Set the importer address - combine address, zipcode, and country
    const importerAddressField = document.querySelector('textarea[name="importer"]');
    if (importerAddressField) {
        let fullAddress = importerAddress;
        if (importerZipcode) {
            fullAddress += (fullAddress ? ', ' : '') + importerZipcode;
        }
        if (importerCountry) {
            fullAddress += (fullAddress ? ', ' : '') + importerCountry;
        }
        importerAddressField.value = fullAddress;
    }
    
    // Also try to populate the autocomplete field if it exists
    const importerNameAutocomplete = document.querySelector('#importer_name');
    if (importerNameAutocomplete) {
        let fullInfo = importerName;
        if (importerAddress) {
            fullInfo += ', ' + importerAddress;
        }
        if (importerZipcode) {
            fullInfo += ', ' + importerZipcode;
        }
        if (importerCountry) {
            fullInfo += ', ' + importerCountry;
        }
        importerNameAutocomplete.value = fullInfo;
    }
    
    // Close the modal (Bootstrap 5)
    var modalEl = document.getElementById('importerModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }
}