function passCommodity(pid, pname, spname, dproduct) {
   
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