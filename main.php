<?php
session_start();

// AJAX handler for certificate status
if (isset($_GET['action']) && $_GET['action'] === 'get_certificate_status') {
    require("php-bin/connection.php");
    require("php-bin/supports.php");
    
    // Clean output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    
    $appid = isset($_GET['appid']) ? $_GET['appid'] : '';
    
    if (empty($appid) || !is_numeric($appid)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid application ID'
        ]);
        exit;
    }
    
    $logs = CertificateStatusInfo($appid, $con);
    
    if ($logs === null) {
        echo json_encode([
            'success' => false,
            'error' => 'Error retrieving certificate status'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'logs' => $logs
        ]);
    }
    exit;
}

require("php-bin/connection.php");
require("php-bin/supports.php");

function SaveApplicationAttachment($fileInput, $appId, $userId, $con) {
  if (!isset($_FILES[$fileInput])) {
    return ['success' => true, 'skipped' => true, 'message' => 'No file uploaded'];
  }

  if (empty($appId) || !is_numeric($appId)) {
    return ['success' => false, 'message' => 'Invalid application ID for attachment upload'];
  }

  $file = $_FILES[$fileInput];
  $isMultiUpload = isset($file['name']) && is_array($file['name']);
  if (!isset($file['error'])) {
    return ['success' => false, 'message' => 'Invalid upload payload'];
  }

  if (!$isMultiUpload && $file['error'] === UPLOAD_ERR_NO_FILE) {
    return ['success' => true, 'skipped' => true, 'message' => 'No file uploaded'];
  }

  if ($isMultiUpload) {
    $hasAnyFile = false;
    foreach ($file['error'] as $uploadError) {
      if ((int)$uploadError !== UPLOAD_ERR_NO_FILE) {
        $hasAnyFile = true;
        break;
      }
    }
    if (!$hasAnyFile) {
      return ['success' => true, 'skipped' => true, 'message' => 'No file uploaded'];
    }
  }

  $maxFileSize = 10 * 1024 * 1024;
  $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
  $allowedMimes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/octet-stream'
  ];

  $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'application_documents';
  if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    return ['success' => false, 'message' => 'Failed to create upload directory'];
  }

  $createTableSql = "CREATE TABLE IF NOT EXISTS tbapplication_uploads (
            id SERIAL PRIMARY KEY,
            application_id INTEGER NOT NULL,
            original_filename TEXT NOT NULL,
            stored_filename TEXT NOT NULL,
            file_path TEXT NOT NULL,
            mime_type TEXT,
            file_size BIGINT,
            uploaded_by TEXT,
            uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
          )";
  pg_query($con, $createTableSql);

  $fileCount = $isMultiUpload ? count($file['name']) : 1;
  $uploadedCount = 0;
  $failedMessages = [];
  $uploadedPaths = [];

  for ($index = 0; $index < $fileCount; $index++) {
    $currentError = $isMultiUpload ? (int)$file['error'][$index] : (int)$file['error'];
    if ($currentError === UPLOAD_ERR_NO_FILE) {
      continue;
    }
    if ($currentError !== UPLOAD_ERR_OK) {
      $failedMessages[] = 'File upload failed with error code: ' . $currentError;
      continue;
    }

    $currentSize = $isMultiUpload ? (int)$file['size'][$index] : (int)$file['size'];
    if ($currentSize <= 0 || $currentSize > $maxFileSize) {
      $failedMessages[] = 'Invalid file size. Maximum allowed size is 10MB';
      continue;
    }

    $currentNameRaw = $isMultiUpload ? (string)$file['name'][$index] : (string)$file['name'];
    $originalName = basename($currentNameRaw);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
      $failedMessages[] = 'Invalid file type for ' . $originalName;
      continue;
    }

    $tmpName = $isMultiUpload ? $file['tmp_name'][$index] : $file['tmp_name'];

    $mimeType = '';
    if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      if ($finfo) {
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
      }
    }
    if ($mimeType !== '' && !in_array($mimeType, $allowedMimes, true)) {
      $failedMessages[] = 'MIME type not allowed for ' . $originalName;
      continue;
    }

    if (function_exists('random_bytes')) {
      $randomSuffix = bin2hex(random_bytes(5));
    } else {
      $randomSuffix = preg_replace('/[^a-zA-Z0-9]/', '', uniqid((string)mt_rand(), true));
    }

    $storedFilename = 'app_' . (int)$appId . '_' . date('YmdHis') . '_' . $randomSuffix . '.' . $extension;
    $absolutePath = $uploadDir . DIRECTORY_SEPARATOR . $storedFilename;
    $relativePath = 'uploads/application_documents/' . $storedFilename;

    if (!move_uploaded_file($tmpName, $absolutePath)) {
      $failedMessages[] = 'Could not save file ' . $originalName;
      continue;
    }

    $safeAppId = pg_escape_string($con, (string)$appId);
    $safeOriginal = pg_escape_string($con, $originalName);
    $safeStored = pg_escape_string($con, $storedFilename);
    $safePath = pg_escape_string($con, $relativePath);
    $safeMime = pg_escape_string($con, $mimeType);
    $safeSize = pg_escape_string($con, (string)$currentSize);
    $safeUserId = pg_escape_string($con, (string)$userId);

    $insertSql = "INSERT INTO tbapplication_uploads (application_id, original_filename, stored_filename, file_path, mime_type, file_size, uploaded_by)
            VALUES ('$safeAppId', '$safeOriginal', '$safeStored', '$safePath', '$safeMime', '$safeSize', '$safeUserId')";
    $insertResult = pg_query($con, $insertSql);

    if (!$insertResult) {
      if (file_exists($absolutePath)) {
        unlink($absolutePath);
      }
      $failedMessages[] = 'Failed to save metadata for ' . $originalName;
      continue;
    }

    $uploadedCount++;
    $uploadedPaths[] = $relativePath;
  }

  if ($uploadedCount === 0 && !empty($failedMessages)) {
    return ['success' => false, 'skipped' => false, 'message' => implode('; ', $failedMessages)];
  }

  if ($uploadedCount === 0) {
    return ['success' => true, 'skipped' => true, 'message' => 'No file uploaded'];
  }

  if (!empty($failedMessages)) {
    return [
      'success' => true,
      'skipped' => false,
      'message' => 'Uploaded ' . $uploadedCount . ' file(s), some failed: ' . implode('; ', $failedMessages),
      'paths' => $uploadedPaths,
      'uploaded_count' => $uploadedCount
    ];
  }

  return [
    'success' => true,
    'skipped' => false,
    'message' => 'Uploaded ' . $uploadedCount . ' file(s) successfully',
    'paths' => $uploadedPaths,
    'uploaded_count' => $uploadedCount
  ];
}

$_SESSION['lang'] = 'en'; // NOT WORKING -FIX- Default to English
if (isset($_GET['lang'])) {
  $selectedLang = $_GET['lang'];
  $_SESSION['lang'] = $selectedLang; // Store the selected language in the session
} else {
  // Default to English if no language is selected
  if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
  }
}
//if (ob_get_level()) ob_end_clean();
// Fixed language loading for cloud server compatibility
$lang = 'en'; // Default to English
if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} elseif (isset($_GET['lang']) && !empty($_GET['lang'])) {
    $lang = $_GET['lang'];
}
// Include the appropriate language file
$langFile = "php-bin/lang_" . $lang . ".php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    // Fallback translations if file doesn't exist
    $translations = array(
        'dashboard' => 'Dashboard',
        'Dashboard' => 'Dashboard'
    );
}
// Build language switch URLs preserving current query parameters
$__lang_params = $_GET;
if (!isset($__lang_params['uid']) || empty($__lang_params['uid'])) {
  $__lang_params['uid'] = isset($userid) ? $userid : '';
}
$__lang_params_la = $__lang_params; $__lang_params_la['lang'] = 'la';
$__lang_params_en = $__lang_params; $__lang_params_en['lang'] = 'en';

$langHrefLa = '?' . http_build_query($__lang_params_la);
$langHrefEn = '?' . http_build_query($__lang_params_en);
// Ensure legacy variable is defined for templates expecting it
$selectedLang = $lang;
// echo "<script>alert('Login-Updated with User id: " . (isset($_GET["uid"]) ? $_GET["uid"] : 'not set') . "');</script>"; 
// Dynamic Authentication System - same as entity.php
$userid = '';
// Try multiple sources for userid (priority order)
if (isset($_GET["uid"]) && !empty($_GET["uid"])) {
    $userid = $_GET["uid"];
} elseif (isset($_POST["uid"]) && !empty($_POST["uid"])) {
    $userid = $_POST["uid"];
} elseif (isset($_POST["huid"]) && !empty($_POST["huid"])) {
    $userid = $_POST["huid"];
} elseif (isset($_COOKIE["ephyto_uid"]) && !empty($_COOKIE["ephyto_uid"])) {
    $userid = $_COOKIE["ephyto_uid"];
} elseif (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {
    $referer = $_SERVER["HTTP_REFERER"];
    if (preg_match('/[?&]uid=([^&]+)/', $referer, $matches)) {
        $userid = urldecode($matches[1]);
    }
} 
if (!empty($userid)) {
    // Get user data from database
    $userdata = Userdata($userid, $con);  
    if ($userdata) {
        $username = $userdata['name'];
        $email = $userdata['email'];
        $position = $userdata['position'];
        $groupid = isset($userdata['group_id']) && !empty($userdata['group_id']) ? $userdata['group_id'] : '1'; // Default to group 1 if not set
        $groupname = GroupName($userdata['group_id'], $con);      
        // Get and store user profile image
        $uprofile = Profiledata($userid, $con);
        if (!$uprofile) {
            // Initialize profile if it doesn't exist
            InitializeProfile($userid, $con);
            $uprofile = Profiledata($userid, $con);
        }
        // Debug alert to show actual profile data - FIXED SYNTAX
        if ($uprofile && is_array($uprofile)) {
            $imgpath_value = isset($uprofile['imgfilepath']) ? $uprofile['imgfilepath'] : 'NOT SET';      
        } else {
            // echo "<script>alert('User profile: NO PROFILE DATA FOUND');</script>";
        }  
        if ($uprofile && isset($uprofile['imgfilepath']) && !empty($uprofile['imgfilepath']) && $uprofile['imgfilepath'] !== 'default_imgfilepath') {
            $uimage = $uprofile['imgfilepath'];
        } else {
            $uimage = 'assets/img/profile-img.jpg'; // default image if no profile or image
        }
    } else {
        $username = '';
        $position = '';
        $groupid = '';
        $groupname = '';
        $uimage = 'assets/img/profile-img.jpg';
    }
 } else {
    // No user ID provided, set defaults
    $username = '';
    $position = '';
    $groupid = '';
    $groupname = '';
    $uimage = 'assets/img/profile-img.jpg';
 }
 // Authentication check
 if(empty($userid)){
    echo "<script>alert('You are not logged in. Please log in to access this page.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
 }
 // Use group ID from user data
 $guid = $groupid;
  // *************************** APPLICATION ***************************
 // CANCEL/DELETE Application
 if (isset($_GET['btn']) && $_GET['btn'] === 'cancelApp') {
   // Handle the cancellation logic here
    $appid = $_GET['appid']; // Get the application ID from the query parameter
   echo "<script>alert('Application cancelled successfully. ID: " . $appid . "');</script>";
    $del = DeleteApplication($appid, $con);
 }
 // SUBMIT/SAVE application by UPDATING tbapplication with the form data - CLICK ON SUBMIT BUTTON
    if (isset($_POST['btnsubApplication_save']) && ($_POST['btnsubApplication_save'] === "submit" || $_POST['btnsubApplication_save'] === "update")) {  // Submit from application form in transaction.php    
      // echo "<script>alert('Hello, submit application');</script>";
        $app_id = isset($_POST['app_id']) ? $_POST['app_id'] : ''; // hidden input
       // echo "<script>alert('Application ID (hidden input): " . $app_id . "');</script>";  
        $app_no = isset($_POST['application_no']) ? $_POST['application_no'] : '';
        $reg_no = isset($_POST['reg_no']) ? $_POST['reg_no'] : '';
        $entry_point = isset($_POST['entry_point']) ? $_POST['entry_point'] : '';
        $applicant_name = isset($_POST['applicant_name']) ? $_POST['applicant_name'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';  
        $import_country = isset($_POST['import_country']) ? $_POST['import_country'] : '';
        $import_point = isset($_POST['import_point']) ? $_POST['import_point'] : '';
        $export_certificate = isset($_POST['export_certificate']) ? 1 : 0;
        $transit_certificate = isset($_POST['transit_certificate']) ? 1 : 0;
        if($export_certificate==1){
          $certificate_type = 'export';
        } elseif($transit_certificate==1) {
          $certificate_type = 'transit';
        }
        $check_multiple = isset($_POST['multiple_commodities']) ? 1 : 0;
        $check_support_document = isset($_POST['support_document']) ? 1 : 0;
        $product_name = isset($_POST['proname']) ? $_POST['proname'] : '';
        $name_oncertificate = isset($_POST['name_oncertificate']) ? $_POST['name_oncertificate'] : '';
        $scientific_name = isset($_POST['scientific_name']) ? $_POST['scientific_name'] : '';
        $product_id = ProductId($product_name, $scientific_name, $con);
        $commodity_description = isset($_POST['number_description']) ? $_POST['number_description'] : '';
        $nquantity = isset($_POST['nquantity']) ? $_POST['nquantity'] : '';
        $gquantity = isset($_POST['gquantity']) ? $_POST['gquantity'] : '';
        $unit = isset($_POST['unit']) ? $_POST['unit'] : '';
        $marks = isset($_POST['marks']) ? $_POST['marks'] : '';
        $place_origin = isset($_POST['place_origin']) ? $_POST['place_origin'] : null;  // data type - integer could not accept ''
        $conveyance = isset($_POST['conveyance']) ? $_POST['conveyance'] : null;
        $conveyance_sign = isset($_POST['conveyance_sign']) ? $_POST['conveyance_sign'] : '';
        $exporter_address = isset($_POST['exporter']) ? $_POST['exporter'] : '';  // exporter address
        $importerid = isset($_POST['importer_id']) ? $_POST['importer_id'] : ''; // hidden input for importer entity id 
        // Ensure importerid is properly handled for database operations
        if ($importerid === '' || !is_numeric($importerid)) {
            $importerid = null;
        } else {
            $importerid = (int)$importerid;
        } 
        // Handle importer info safely
        $importer_info = EntityImportInfo($importerid, $con);
        $importer_name = $importer_info ? $importer_info['title'] : '';
        $importer_address = $importer_info ? $importer_info['address'] : '';
       // $importer_address = isset($_POST['importer']) ? $_POST['importer'] : ''; // importer address
        $purpose = isset($_POST['purpose']) ? $_POST['purpose'] : '';
         $place_quarantine = isset($_POST['place_quarantine']) && $_POST['place_quarantine'] !== '' ? (int)$_POST['place_quarantine'] : null;
        //$place_treatment = isset($_POST['place_treatment']) ? $_POST['place_treatment'] : '';
        $place_treatment = isset($_POST['place_treatment']) && $_POST['place_treatment'] !== '' ? (int)$_POST['place_treatment'] : null;
        $place_quarantine_other = isset($_POST['place_quarantine_other']) ? $_POST['place_quarantine_other'] : '';
        $place_treatment_other = isset($_POST['place_treatment_other']) ? $_POST['place_treatment_other'] : '';
        $certificate_date = isset($_POST['certificate_date']) ? $_POST['certificate_date'] : '';
        //$guid = isset($_POST['guid']) ? $_POST['guid'] : '';
            // Put them into $data array (keys = DB column names)
        $data = [
            'reg_no'             => $reg_no,
            'export_point'       => $entry_point,
            'contact_person'     => $applicant_name,
            'address_person'     => $address,
            'phone'              => $phone,
            'country_import'     => $import_country,
            'import_point'       => $import_point,
            'certificate_type'   => $certificate_type,  
            'multi_item'         => $check_multiple,
            'print_support'      => $check_support_document,
            'commodity_id'      => $product_id,
            'name_oncertificate' => $name_oncertificate,
            'name_scientific'    => $scientific_name,
            'commodity_description'=> $commodity_description,
            'quantity_net'        => $nquantity,
            'quantity_gross'     => $gquantity,
            'unit_id'            => $unit,
            'marks_item'         => $marks,
            'place_origin'      => $place_origin,
            'conveyance_id'     => $conveyance,
            'conveyance_sign'   => $conveyance_sign,
            'address_exporter'  => $exporter_address,
            'address_importer'  => $importer_address,
            'purpose'           => $purpose,
            'place_quarantine'  => $place_quarantine,
            'place_treatment'   => $place_treatment,
            'date_certificate'  => $certificate_date,
            'place_quarantine_other' => $place_quarantine_other,
            'place_treatment_other'  => $place_treatment_other,
            'importerid'       => $importerid
        ]; 
        $result = ApplicationUpdate($app_id, $data, $con); // Update tbapplication with the form data
        if ($result) {
          $uploadResult = SaveApplicationAttachment('application_attachment', $app_id, $userid, $con);
          if (!$uploadResult['success']) {
            echo "<script>alert('Application saved, but attachment upload failed: " . addslashes($uploadResult['message']) . "');</script>";
          }
           // echo "<script>alert('Application updated successfully!');</script>";
        } else {
            echo "<script>alert('Failed to update application. Please check the console for details.');</script>";
        }
    } // End of if - Submission for updating application FIRST TIME (NO CHANGE IS MADE)
   // UPDATE/CHANGE on application - CLICK ON UPDATE BUTTON in transaction.php
   if(isset($_POST['btnsubApplication_save']) && $_POST['btnsubApplication_save'] === "update"){
     echo "<script>alert('Update- Application ID: $app_id');</script>";
   }
   // *************************** INSPECTION ***************************
    // SAVE inspection data - CLICK ON SAVE BUTTON in inspection form in transaction.php
    if (isset($_POST['btnSubmitInspection'])) {     
        $app_id = isset($_POST['appid']) ? $_POST['appid'] : ''; // hidden input
        $inspection_date = isset($_POST['inspection_date']) ? $_POST['inspection_date'] : null;
        $sample_no = isset($_POST['sampleno']) ? $_POST['sampleno'] : '';
        $sample_quantity = isset($_POST['sample_volume']) ? $_POST['sample_volume'] : '';
        $unit_id = isset($_POST['unit']) ? $_POST['unit'] : null;
        $sample_collected_by = isset($_POST['sample_collectedby']) ? $_POST['sample_collectedby'] : '';
        $inspected_by = isset($_POST['sample_inspectedby']) ? $_POST['sample_inspectedby'] : '';
        $certificate_fee = isset($_POST['certificate_fee']) ? $_POST['certificate_fee'] : '';
        $receipt_no = isset($_POST['receipt_no']) ? $_POST['receipt_no'] : '';
        $lot_number = isset($_POST['lot_no']) ? $_POST['lot_no'] : '';
        $inspection_method = isset($_POST['inspection_method']) && $_POST['inspection_method'] !== '' ? (int)$_POST['inspection_method'] : null;
        $pest_detected = isset($_POST['detected_pest']) ? 1 : 0;
        $treat_ability = isset($_POST['treatment_ability']) ? 1 : 0;
        $lab_required = isset($_POST['lab_analysis']) ? 1 : 0;  

        // No treatment for pest detected
         if(empty($treat_ability)){
            $treat_ability = 0;
            $treatment_method = null;
            $treatment_date = '1900-01-01';
            $chemical_used = '';
            $chemical_fortreat = '';
            $duration_temp = '';
            $concentration = '';
            $sample_inspectedby = '';
         } else {
            $treat_ability = 1;
            $treatment_method = isset($_POST['treatment_method']) && $_POST['treatment_method'] !== '' ? (int)$_POST['treatment_method'] : null;
            $treatment_date = isset($_POST['treatment_date']) ? $_POST['treatment_date'] : null;
            $chemical_used = isset($_POST['chemical_used']) ? $_POST['chemical_used'] : '';
            $chemical_fortreat = isset($_POST['chemical_fortreat']) ? $_POST['chemical_fortreat'] : '';
            $duration_temp = isset($_POST['duration_temp']) ? $_POST['duration_temp'] : '';
            $concentration = isset($_POST['concentration']) ? $_POST['concentration'] : '';
            $sample_inspectedby = isset($_POST['sample_inspectedby']) ? $_POST['sample_inspectedby'] : '';
         }
        $additional_info = isset($_POST['additional_info']) ? $_POST['additional_info'] : '';
        $treatment_reason = isset($_POST['reason']) ? $_POST['reason'] : '';
        $post_treatment_details = isset($_POST['post_details']) ? $_POST['post_details'] : '';
         // Put them into $inspection_data array (keys = DB column names)
        $inspection_data = [
            'application_id' => $app_id,
            'inspection_date' => $inspection_date,
            'sample_no' => $sample_no,
            'sample_quantity' => $sample_quantity,
            'unit_id' => $unit_id,
            'sample_collected_by' => $sample_collected_by,
            'inspected_by' => $inspected_by,
            'certificate_fee' => $certificate_fee,
            'receipt_no' => $receipt_no,
            'lot_number' => $lot_number,
            'inspection_method' => $inspection_method,
            'pest_detected' => $pest_detected,
            'treat_ability' => $treat_ability,
            'lab_required' => $lab_required,
            'treatment_method' => $treatment_method,
            'treatment_date' => $treatment_date,
            'chemical_used' => $chemical_used,
            'chemical_fortreat' => $chemical_fortreat,
            'duration_temp' => $duration_temp,
            'concentration' => $concentration,
            'sample_inspectedby' => $sample_inspectedby,
            'additional_info' => $additional_info,
            'treatment_reason' => $treatment_reason,
            'post_treatment_details' => $post_treatment_details,
            'enabled' => 'yes'
        ];
        //echo "<script>alert('Save inspection data -UPDATE: pk - Application ID: $app_id');</script>";
         // ADD NEW inspection data
         if($_POST['btnSubmitInspection'] === 'submit'){
            $result = InspectionAdd($inspection_data, $con);
            if ($result) {
                echo "<script>alert('Inspection data saved successfully!');</script>";
            } else {
                echo "<script>alert('Failed to save inspection data. Please try again.');</script>";
            }
          } elseif($_POST['btnSubmitInspection'] === 'update'){  // UPDATE inspection data
            $result = InspectionUpdate($app_id, $inspection_data, $con);
            if ($result) {
                echo "<script>alert('Inspection data updated successfully!');</script>";
            } else {
                echo "<script>alert('Failed to update inspection data. Please try again.');</script>";
            }
          }     
    } // end of if - submission of inspection form
    // *************************** CERTIFICATE ***************************
    // SAVE certificate data - CLICK ON SAVE BUTTON in certificate form in transaction.php
    if (isset($_POST['btnSubmitCertificate'])) {  
        $certificate_id = isset($_POST['certificate_id']) ? $_POST['certificate_id'] : ''; // hidden input
        $app_id = isset($_POST['appid_certificate']) ? $_POST['appid_certificate'] : ''; // hidden input from Certificate form
        $certificate_no = isset($_POST['certificate_no']) ? $_POST['certificate_no'] : '';
        $app_no = isset($_POST['application_no']) ? $_POST['application_no'] : '';
        $carbonpaper_id = isset($_POST['carbonpaper_id']) ? $_POST['carbonpaper_id'] : '';
        $approved_by = isset($_POST['approved_by']) ? $_POST['approved_by'] : '';
        $approver_position = isset($_POST['approver_position']) ? $_POST['approver_position'] : '';
        $place_issue = isset($_POST['place_issue']) ? $_POST['place_issue'] : '';
        $consignment_value = isset($_POST['consignment_value']) ? $_POST['consignment_value'] : '';
        $value_currency = isset($_POST['value_currency']) ? $_POST['value_currency'] : '';
        $additional_scientificname = isset($_POST['additional_scientificname']) ? $_POST['additional_scientificname'] : '';
        $additional_declaration = isset($_POST['additional_declaration']) ? $_POST['additional_declaration'] : '';
        $datetime_created = CertificateInfo($app_id, $con)['datetime_created']; // keep the original created date
        $datetime_updated = CertificateInfo($app_id, $con)['datetime_updated']; // keep the original updated date
        $created_uid = CertificateInfo($app_id, $con)['created_uid']; // keep the original created uid
        $updated_uid = CertificateInfo($app_id, $con)['updated_uid']; // update uid
        $date_issue = isset($_POST['date_issue']) ? $_POST['date_issue'] : null;
        $cert_status = CertificateInfo($app_id, $con)['certificate_status']; // keep the original certificate status
        $enabled = CertificateInfo($app_id, $con)['enabled']; // keep the original enabled status
        // These inputs are used for presentation on the form only - NOT saved in tbcertificate
        $import_country = isset($_POST['import_country']) ? $_POST['import_country'] : '';
        $import_entrypoint = isset($_POST['import_entrypoint']) ? $_POST['import_entrypoint'] : '';
        $export_entrypoint = isset($_POST['export_entrypoint']) ? $_POST['export_entrypoint'] : '';
        $exporter_name = isset($_POST['exporter_name']) ? $_POST['exporter_name'] : '';
        $importer_name = isset($_POST['importer_name']) ? $_POST['importer_name'] : '';
        $exporter_address = isset($_POST['exporter_address']) ? $_POST['exporter_address'] : '';
        $importer_address = isset($_POST['importer_address']) ? $_POST['importer_address'] : '';   
       // Put them into $certificate_data array (keys = DB column names)
        $certificate_data = [
            'application_id' => $app_id,
            'certificate_no' => $certificate_no,
            'carbonpaper_id' => $carbonpaper_id,
            'approved_by' => $approved_by,
            'position_approved' => $approver_position,
            'place_issued' => $place_issue,
            'consignment_value' => $consignment_value,
            'value_currency' => $value_currency,
            'additional_scientificname' => $additional_scientificname,
            'additional_declaration' => $additional_declaration,
            'datetime_created' => $datetime_created,
            'datetime_updated' => $datetime_updated,
            'created_uid' => $userid,
            'updated_uid' => $userid,
            'gid' => $guid,
            'date_issued' => $date_issue,
            'certificate_status' => $cert_status,
            'enabled' => $enabled
        ];    
         // SUBMIT AND UPDATE ARE THE SAME- certificate data because certificate no is generated when application is submitted
         if($_POST['btnSubmitCertificate'] === 'submit' || $_POST['btnSubmitCertificate'] === 'update'){
           $result = CertificateUpdate($certificate_id, $certificate_data, $con);
            
            // Also update importer information in tbapplication
            if ($result && $app_id) {
                $importer_id = isset($_POST['importer_id']) ? $_POST['importer_id'] : null;
                
                // Update both importerid and address_importer
                $update_app_sql = "UPDATE tbapplication SET importerid = $1, address_importer = $2 WHERE id = $3";
                $update_app_result = pg_query_params($con, $update_app_sql, [$importer_id, $importer_address, $app_id]);
                
                if (!$update_app_result) {
                    error_log("Failed to update importer info in tbapplication for app_id: $app_id - " . pg_last_error($con));
                }
            }
            
            if ($result) {
                $appid_for_cert = isset($_POST['appid_certificate']) ? $_POST['appid_certificate'] : 0;
                echo "<script>
                    if(confirm('Certificate data submit/updated successfully! Would you like to view and print the certificate?')) {
                        window.open('certificate_view.php?appid=" . intval($appid_for_cert) . "', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
                    }
                </script>";
            } else {
                echo "<script>alert('Failed to update certificate data. Please try again.');</script>";
            }    
          }      
    } // end of if - submission of certificate form
?>
<!DOCTYPE html>
<html lang="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lo' : 'en'; ?>">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo isset($translations['dashboard']) ? $translations['dashboard'] : 'Dashboard'; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet"> 
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="stylecss/lang.css" rel="stylesheet">
  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 7 2025 with Bootstrap v5.3.5
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>
<body class="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lang-lao' : 'lang-en'; ?>">
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block"><?php echo isset($translations['e-Phytosanitary']) ? $translations['e-Phytosanitary'] : 'e-Phytosanitary'; ?></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- Language Switcher -->
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/flags/<?php echo ($lang === 'en') ? 'english' : 'lao'; ?>.png" alt="<?php echo ($lang === 'en') ? 'English' : 'ລາວ'; ?>" style="width: 24px; height: 16px;">
            <span style="font-size: 14px;"><?php echo ($lang === 'en') ? 'English' : 'ລາວ'; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo htmlspecialchars($langHrefLa); ?>">
                <img src="assets/img/flags/lao.png" alt="Lao" style="width: 24px; height: 16px; margin-right: 10px;">
                <span>ລາວ</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo htmlspecialchars($langHrefEn); ?>">
                <img src="assets/img/flags/english.png" alt="English" style="width: 24px; height: 16px; margin-right: 10px;">
                <span>English</span>
              </a>
            </li>
          </ul>
        </li>
    <!-- End Language Switcher -->
        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $uimage; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $username; ?></span>
          </a><!-- End Profile Iamge Icon -->
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $username; ?></h6>
              <span><?php echo $position; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>">
                <i class="bi bi-person"></i>
                <span><?php echo isset($translations['My Profile']) ? $translations['My Profile'] : 'My Profile'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>">
                <i class="bi bi-gear"></i>
                <span><?php echo isset($translations['Account Settings']) ? $translations['Account Settings'] : 'Account Settings'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span><?php echo isset($translations['Need Help?']) ? $translations['Need Help?'] : 'Need Help?'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="index.php?logout=true">
                <i class="bi bi-box-arrow-right"></i>
                <span><?php echo isset($translations['Sign Out']) ? $translations['Sign Out'] : 'Sign Out'; ?></span>
              </a>
            </li>
          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->
      </ul>
    </nav><!-- End Icons Navigation -->
  </header><!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="#">
          <i class="bi bi-grid"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav --> 
      <li class="nav-item">
        <a class="nav-link collapsed" href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-file-earmark-text"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></span>
        </a>
      </li><!-- End Application Nav --> 
       <li class="nav-item">
        <a class="nav-link collapsed" href="inspection.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-journal-check"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></span>
        </a>
      </li><!-- End Inspection Nav --> 
       <li class="nav-item">
        <a class="nav-link collapsed" href="certificate.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-journal-album"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Certificate']) ? $translations['Certificate'] : 'Certificate'; ?></span>
        </a>
      </li><!-- End Certificate Nav --> 

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo htmlspecialchars('entity.php?entity=export&uid='.$userid.'&lang='.$lang); ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></span>
        </a>
      </li><!-- End Import Entity/Company form Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span><?php echo isset($translations['Master data']) ? $translations['Master data'] : 'Master data'; ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         <li>
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></span>
            </a>
          </li>
        <?php if($groupname == "admin"){ ?><!-- Admin group check -->
          <li>
            <a href="masterdata.php?part=conveyance&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=districts&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Districts']) ? $translations['Districts'] : 'Districts'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Entity Type']) ? $translations['Entity Type'] : 'Entity Type'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection method'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></span>
            </a>
          </li>
           <li>
            <a href="masterdata.php?part=pest&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=productgroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=productunit&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product Unit']) ? $translations['Product Unit'] : 'Product Unit'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Provinces']) ? $translations['Provinces'] : 'Provinces'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Treatment Method']) ? $translations['Treatment Method'] : 'Treatment Method'; ?></span>
            </a>
          </li>
         <?php } // End of Admin group check ?>
        </ul>
      </li><!-- End Master Data Nav -->

      <!-- Monitoring and Reporting -->
       <li class="nav-heading"><?php echo isset($translations['MONITORING AND REPORTING']) ? $translations['MONITORING AND REPORTING'] : 'MONITORING AND REPORTING'; ?></li>
        <li class="nav-item">
        <a class="nav-link collapsed" href="monitor_report.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Certificate tracking']) ? $translations['Certificate tracking'] : 'Certificate tracking'; ?></span>
        </a>
      </li><!-- End Monitoring and Reporting Nav -->
    
      <li class="nav-heading"><?php echo isset($translations['USERS MANAGEMENT']) ? $translations['USERS MANAGEMENT'] : "Users' Management"; ?></li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->

      <?php if($groupname == "admin"){ ?><!-- Admin group check -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-people"></i>
          <span><?php echo isset($translations['Users group']) ? $translations['Users group'] : 'Users group'; ?></span>
        </a>
      </li><!-- End Users group -->
       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-shield-lock"></i>
          <span><?php echo isset($translations['Group permits']) ? $translations['Group permits'] : 'Group permits'; ?></span>
        </a>
      </li><!-- End Permission: User Group and Module -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person-plus"></i><span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li>  
      <?php } // End of Admin group check ?>
      <!-- pk**: End of User Admin-->
    </ul>
  </aside><!-- End Sidebar-->
  <main id="main" class="main">
    <div class="pagetitle">
      <h1><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></a></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <?php
    // Get chart data from the function
    $chartData = ChartDocTracking($guid, $con);  // this function in supports.php
    
    // Debug: Log the data
    error_log("Chart Data Result: " . print_r($chartData, true));
    
    // Ensure we always have valid JSON
    if (!$chartData) {
        $chartData = [
            'success' => false,
            'error' => 'No data returned from ChartDocTracking'
        ];
    }
    
    $chartDataJson = json_encode($chartData, JSON_NUMERIC_CHECK);
    
    // Debug: Log the JSON
    error_log("Chart Data JSON: " . $chartDataJson);
    
    // Check for JSON encoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Encode Error: " . json_last_error_msg());
        $chartDataJson = json_encode([
            'success' => false,
            'error' => 'JSON encoding failed: ' . json_last_error_msg()
        ]);
    }

    // Monthly pest detected line-chart data (last 3 months)
    $monthlyPestData = MonthlyPestDetectedChartData($guid, $con);
    $defaultLastThreeMonths = [];
    for ($i = 2; $i >= 0; $i--) {
      $defaultLastThreeMonths[] = date('M', strtotime("first day of -{$i} month"));
    }
    if (!$monthlyPestData || !isset($monthlyPestData['months']) || !isset($monthlyPestData['series'])) {
      $monthlyPestData = [
        'success' => false,
        'months' => $defaultLastThreeMonths,
        'series' => [],
        'year' => (int)date('Y')
      ];
    }
    $monthlyPestDataJson = json_encode($monthlyPestData, JSON_NUMERIC_CHECK);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $monthlyPestDataJson = json_encode([
        'success' => false,
        'months' => $defaultLastThreeMonths,
        'series' => [],
        'year' => (int)date('Y')
      ]);
    }

    // Monthly pest category bar-chart data (last 3 months)
    $monthlyPestCategoryData = MonthlyPestCategoryChartData($guid, $con);
    if (!$monthlyPestCategoryData || !isset($monthlyPestCategoryData['months']) || !isset($monthlyPestCategoryData['series'])) {
      $monthlyPestCategoryData = [
        'success' => false,
        'months' => $defaultLastThreeMonths,
        'series' => [],
        'year' => (int)date('Y')
      ];
    }
    $monthlyPestCategoryDataJson = json_encode($monthlyPestCategoryData, JSON_NUMERIC_CHECK);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $monthlyPestCategoryDataJson = json_encode([
        'success' => false,
        'months' => $defaultLastThreeMonths,
        'series' => [],
        'year' => (int)date('Y')
      ]);
    }
    ?>
    
    <!-- Charts Section -->
    <section class="section">
      <div class="row">  
        <!-- Chart 1: Application Status Chart -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Document status']) ? $translations['Document status'] : 'Document status'; ?> <span>| <?php echo isset($translations['Total']) ? $translations['Total'] : 'Total'; ?></span></h5>
              <!-- Donut Chart -->
              <div id="donutChart" style="min-height: 250px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  // Get PHP data
                  const chartData = <?php echo $chartDataJson; ?>;
                  
                  // Debug: Log the data
                  console.log('Chart Data:', chartData);
                  console.log('Chart Data Type:', typeof chartData);
                  console.log('Has success?', chartData ? chartData.success : 'no chartData');
                  console.log('Has data?', chartData && chartData.data ? 'yes' : 'no');
                  
                  let chartDataArray = [];
                  if (chartData && chartData.success && chartData.data) {
                    console.log('Using real data:', chartData.data);
                    console.log('Application count:', chartData.data.application);
                    console.log('Inspection count:', chartData.data.inspection);
                    console.log('Certificate count:', chartData.data.certificate);
                    
                    const appCount = parseInt(chartData.data.application) || 0;
                    const inspCount = parseInt(chartData.data.inspection) || 0;
                    const certCount = parseInt(chartData.data.certificate) || 0;
                    
                    // Check if all values are zero
                    if (appCount === 0 && inspCount === 0 && certCount === 0) {
                      console.log('All values are zero - using sample data for visualization');
                      chartDataArray = [
                        { value: 10, name: 'Applications' },
                        { value: 7, name: 'Inspections' },
                        { value: 5, name: 'Certificates' }
                      ];
                    } else {
                      chartDataArray = [
                        { value: appCount, name: 'Applications' },
                        { value: inspCount, name: 'Inspections' },
                        { value: certCount, name: 'Certificates' }
                      ];
                    }
                  } else {
                    console.log('Using fallback data. Error:', chartData ? chartData.error : 'No data');
                    // Fallback data if function fails
                    chartDataArray = [
                      { value: 5, name: 'Applications' },
                      { value: 3, name: 'Inspections' },
                      { value: 2, name: 'Certificates' }
                    ];
                  }
                  
                  console.log('Final chart data array:', chartDataArray);
                  console.log('Chart data values:', chartDataArray.map(d => `${d.name}: ${d.value}`));
                  
                  const chartElement = document.querySelector("#donutChart");
                  console.log('Chart element:', chartElement);
                  
                  if (!chartElement) {
                    console.error('Chart element #donutChart not found!');
                    return;
                  }
                  
                  console.log('Chart element dimensions:', {
                    width: chartElement.offsetWidth,
                    height: chartElement.offsetHeight,
                    clientWidth: chartElement.clientWidth,
                    clientHeight: chartElement.clientHeight
                  });
                  
                  try {
                    const chart = echarts.init(chartElement);
                    console.log('ECharts instance created:', chart);
                    
                    const option = {
                      tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b}: {c} ({d}%)'
                      },
                      legend: {
                        top: '1%',
                        left: 'center'
                      },
                      series: [{
                        name: 'Documents',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        avoidLabelOverlap: false,
                        minShowLabelAngle: 0,
                        label: {
                          show: true,
                          position: 'outside',
                          formatter: '{c}',
                          fontSize: 14,
                          fontWeight: 'bold',
                          minMargin: 5,
                          distanceToLabelLine: 5
                        },
                        emphasis: {
                          label: {
                            show: true,
                            fontSize: '16',
                            fontWeight: 'bold'
                          }
                        },
                        labelLine: {
                          show: true,
                          length: 15,
                          length2: 25,
                          minTurnAngle: 90
                        },
                        data: chartDataArray
                      }]
                    };
                    
                    console.log('Setting chart option:', option);
                    chart.setOption(option);
                    console.log('Chart initialized successfully');
                    
                    // Force resize after a short delay
                    setTimeout(() => {
                      chart.resize();
                      console.log('Chart resized');
                    }, 100);
                    
                  } catch (error) {
                    console.error('Error initializing chart:', error);
                  }
                });
              </script>
              <!-- End Donut Chart -->
            </div>
          </div>
        </div>
        <!-- Chart 2: Monthly Applications Trend -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Monthly Pest Trends <span>| Last 3 Months</span></h5>
              <!-- Line Chart -->
              <div id="reportsChart" style="min-height: 250px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const pestLineData = <?php echo $monthlyPestDataJson; ?>;
                  const legendData = Array.isArray(pestLineData.series)
                    ? pestLineData.series.map(item => item.name)
                    : [];

                  const lineSeries = Array.isArray(pestLineData.series)
                    ? pestLineData.series.map(item => ({
                        name: item.name,
                        type: 'line',
                        data: Array.isArray(item.data) ? item.data : [],
                        smooth: true
                      }))
                    : [];

                  echarts.init(document.querySelector("#reportsChart")).setOption({
                    tooltip: {
                      trigger: 'axis'
                    },
                    legend: {
                      data: legendData
                    },
                    toolbox: {
                      show: true,
                      feature: {
                        dataView: { show: true, readOnly: false },
                        magicType: { show: true, type: ['line', 'bar'] },
                        restore: { show: true },
                        saveAsImage: { show: true }
                      }
                    },
                    xAxis: {
                      type: 'category',
                      boundaryGap: false,
                      data: Array.isArray(pestLineData.months) ? pestLineData.months : []
                    },
                    yAxis: {
                      type: 'value'
                    },
                    series: lineSeries
                  });
                });
              </script>
              <!-- End Line Chart -->
            </div>
          </div>
        </div>
        <!-- Chart 3: Pest Category Trends -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Pest Category Trends <span>| Last 3 Months</span></h5>
              <!-- Column Chart -->
              <div id="columnChart" style="min-height: 250px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const pestCategoryData = <?php echo $monthlyPestCategoryDataJson; ?>;
                  const categoryLegendData = Array.isArray(pestCategoryData.series)
                    ? pestCategoryData.series.map(item => item.name)
                    : [];

                  const barSeries = Array.isArray(pestCategoryData.series)
                    ? pestCategoryData.series.map(item => ({
                        name: item.name,
                        type: 'bar',
                        label: {
                          show: true,
                          position: 'top'
                        },
                        emphasis: {
                          focus: 'series'
                        },
                        data: Array.isArray(item.data) ? item.data : []
                      }))
                    : [];

                  echarts.init(document.querySelector("#columnChart")).setOption({
                    tooltip: {
                      trigger: 'axis',
                      axisPointer: {
                        type: 'shadow'
                      }
                    },
                    legend: {
                      data: categoryLegendData
                    },
                    grid: {
                      left: '3%',
                      right: '4%',
                      bottom: '3%',
                      containLabel: true
                    },
                    xAxis: [{
                      type: 'category',
                      data: Array.isArray(pestCategoryData.months) ? pestCategoryData.months : []
                    }],
                    yAxis: [{
                      type: 'value'
                    }],
                    series: barSeries
                  });
                });
              </script>
              <!-- End Column Chart -->
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Charts Section -->

    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8" style="width: 100%;">
          <div class="row">           
            <!-- Dashboard - main -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <h5 class="card-title"><?php echo isset($translations['Document list']) ? $translations['Document list'] : 'Document list'; ?> <span>| <?php echo isset($translations['Recent']) ? $translations['Recent'] : 'Recent'; ?></span></h5>
                  <table class="table datatable" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col"><?php echo isset($translations['Application No']) ? $translations['Application No'] : 'Application No'; ?></th>
                        <th scope="col"><?php echo isset($translations['Exporter']) ? $translations['Exporter'] : 'Exporters'; ?></th>
                        <th scope="col"><?php echo isset($translations['Submission date']) ? $translations['Submission date'] : 'Submission date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></th>
                        <th scope="col"><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></th>
                        <th scope="col"><?php echo isset($translations['Certificate']) ? $translations['Certificate'] : 'Certificate'; ?></th>
                        <th scope="col"><?php echo isset($translations['Certificate status']) ? $translations['Certificate status'] : 'Certificate status'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php ApplicationList($guid, $con, $lang, $userid); ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Dashboard - main -->
          </div>
        </div><!-- End Left side columns -->
        <!-- Right side columns *****************PK************************ -->
      </div>
      
    </section> <!-- End Dashboard Section -->
    
  <!-- Modal for Certificate Status -->
  <div class="modal fade" id="certificateStatusModal" tabindex="-1" aria-labelledby="certificateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="certificateStatusModalLabel">Certificate Print Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="certificateStatusContent">
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal for Certificate Status -->
  
  <script>
    // Reload page when modal is closed
    document.getElementById('certificateStatusModal').addEventListener('hidden.bs.modal', function () {
      location.reload();
    });
  </script>

  
  </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <!-- PK: no need for footer for this page
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>DOA</span></strong>. All Rights Reserved
    </div>
  </footer>
  -->
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script> 
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  
  <script>
    // Function to view certificate status
    function viewCertificateStatus(appid) {
      var modal = new bootstrap.Modal(document.getElementById('certificateStatusModal'));
      modal.show();
      
      document.getElementById('certificateStatusContent').innerHTML = 
        '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
      
      fetch('main.php?action=get_certificate_status&appid=' + appid)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            let html = '<div class="table-responsive">';
            
            if (data.logs && data.logs.length > 0) {
              html += '<table class="table table-bordered table-striped">';
              html += '<thead><tr>';
              html += '<th>Print Count</th>';
              html += '<th>Status</th>';
              html += '<th>Current Carbon No</th>';
              html += '<th>Original Carbon No</th>';
              html += '<th>Printed date</th>';
              html += '<th>Printed By</th>';
              html += '</tr></thead><tbody>';
              
              data.logs.forEach(function(log) {
                html += '<tr>';
                html += '<td>' + log.print_count + '</td>';
                html += '<td><span class="badge bg-' + (log.current_status === 'Ongoing' ? 'info' : log.current_status === 'Printed/Updated' ? 'warning' : 'success') + '">' + log.current_status + '</span></td>';
                html += '<td>' + (log.current_carbonpaper_id || '-') + '</td>';
                html += '<td>' + (log.original_carbonpaper_id || '-') + '</td>';
                html += '<td>' + log.print_timestamp + '</td>';
                html += '<td>' + (log.user_name ? log.user_name + ' ' + log.user_surname : 'User #' + log.updated_by) + '</td>';
                html += '</tr>';
              });
              
              html += '</tbody></table>';
              
              var lastLog = data.logs[data.logs.length - 1];
              html += '<div class="alert alert-info mt-3">';
              html += '<strong>Latest Status:</strong> ' + lastLog.current_status + ' | ';
              html += '<strong>Total Prints:</strong> ' + lastLog.print_count;
              html += '</div>';
            } else {
              html += '<div class="alert alert-warning">No print history found for this certificate.</div>';
            }
            
            html += '</div>';
            document.getElementById('certificateStatusContent').innerHTML = html;
          } else {
            document.getElementById('certificateStatusContent').innerHTML = 
              '<div class="alert alert-danger">' + data.error + '</div>';
          }
        })
        .catch(error => {
          document.getElementById('certificateStatusContent').innerHTML = 
            '<div class="alert alert-danger">Error loading certificate status: ' + error + '</div>';
        });
    }
  </script>

</body>
</html>