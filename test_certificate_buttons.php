<?php
// Test file for certificate buttons
$appid_certificate = 123; // Test with a dummy application ID
$btnSubmitCertificate = 'update'; // Set to update mode to show the buttons
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Certificate Buttons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Test Certificate Buttons</h1>
        
        <div class="card">
            <div class="card-body">
                <h5>Certificate Form (Test Mode)</h5>
                <p><strong>Application ID:</strong> <?php echo $appid_certificate; ?></p>
                <p><strong>Button State:</strong> <?php echo $btnSubmitCertificate; ?></p>
                
                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2 d-flex gap-2">
                        <button type="submit" name="btnSubmitCertificate" class="btn btn-primary" value="<?php echo $btnSubmitCertificate === 'update' ? 'update' : 'submit'; ?>">
                            <i class="bi bi-save"></i> <?php echo $btnSubmitCertificate === 'update' ? ' Update' : ' Submit'; ?>
                        </button>
                        
                        <?php if ($btnSubmitCertificate === 'update'): ?>
                        <!-- Debug: appid_certificate = <?php echo isset($appid_certificate) ? $appid_certificate : 'NOT SET'; ?> -->
                        <button type="button" class="btn btn-success" onclick="viewCertificate(<?php echo $appid_certificate; ?>)" title="Open certificate in new window">
                            <i class="bi bi-file-earmark-text"></i> View Certificate
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="viewCertificateInSameWindow(<?php echo $appid_certificate; ?>)" title="Open certificate in same window">
                            <i class="bi bi-file-earmark-pdf"></i> View PDF
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="console.log('appid_certificate value:', <?php echo isset($appid_certificate) ? $appid_certificate : 'undefined'; ?>); alert('Debug: appid_certificate = ' + <?php echo isset($appid_certificate) ? $appid_certificate : 'undefined'; ?>);" title="Debug - Show appid value">
                            <i class="bi bi-bug"></i> Debug
                        </button>
                        <?php endif; ?>
                        
                        <a href="#" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
      // Function to view certificate in new window
      function viewCertificate(appid) {
        console.log('viewCertificate called with appid:', appid, 'type:', typeof appid); // Enhanced debug
        
        // Convert to string for validation
        const appidStr = String(appid);
        
        if (!appid || appidStr === '' || appidStr === '0' || appidStr === 'undefined' || appidStr === 'null') {
          console.error('Invalid appid received:', appid);
          alert('Error: Invalid application ID (' + appidStr + '). Please make sure the certificate is saved first.');
          return;
        }
        
        // Build the URL
        const certificateUrl = 'certificate_view.php?appid=' + encodeURIComponent(appid);
        console.log('Opening certificate URL:', certificateUrl);
        
        try {
          const certWindow = window.open(
            certificateUrl, 
            'certificateView', 
            'width=900,height=700,scrollbars=yes,resizable=yes,toolbar=no,menubar=no'
          );
          
          if (certWindow) {
            certWindow.focus();
            
            // Check if window was closed immediately (popup blocker)
            setTimeout(function() {
              if (certWindow.closed) {
                alert('Popup was blocked. Please allow popups for this site or use the "View PDF" button.');
              }
            }, 1000);
            
          } else {
            alert('Popup blocked! Please allow popups for this site to view the certificate, or use the "View PDF" button instead.');
          }
        } catch (error) {
          console.error('Error opening certificate window:', error);
          alert('Error opening certificate window: ' + error.message + '. Please try the "View PDF" button instead.');
        }
      }
      
      // Function to view certificate in same window (fallback)
      function viewCertificateInSameWindow(appid) {
        console.log('viewCertificateInSameWindow called with appid:', appid, 'type:', typeof appid); // Enhanced debug
        
        // Convert to string for validation
        const appidStr = String(appid);
        
        if (!appid || appidStr === '' || appidStr === '0' || appidStr === 'undefined' || appidStr === 'null') {
          console.error('Invalid appid received:', appid);
          alert('Error: Invalid application ID (' + appidStr + '). Please make sure the certificate is saved first.');
          return;
        }
        
        // Build the URL and open in same window
        const certificateUrl = 'certificate_view.php?appid=' + encodeURIComponent(appid);
        console.log('Navigating to certificate URL:', certificateUrl);
        
        try {
          window.location.href = certificateUrl;
        } catch (error) {
          console.error('Error navigating to certificate:', error);
          alert('Error opening certificate: ' + error.message);
        }
      }
    </script>
</body>
</html>