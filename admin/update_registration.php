<?php
include("../config.php");
session_start();

if($_SESSION['user']['role'] != 'admin'){
    die("Access Denied");
}

if(isset($_POST['update'])){
    // Sanitize user inputs to prevent basic SQL Injection bugs
    $registration_id = mysqli_real_escape_string($conn, $_POST['registration_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "UPDATE registrations SET status='$new_status' WHERE id='$registration_id'";

    if(mysqli_query($conn, $sql)){
        $success = "Registration status updated successfully!";
    }
}

/*
========================================
SACRED HEART ECCLESIASTICAL NOTIFICATION SYSTEM
========================================
Processes member status updates and broadcasts 
automated notifications via Africa's Talking API.
========================================
*/

/* 1. SECURE CONTROLLER BOUNDARY */
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

/* 2. SANITIZE DATA ARRAYS */
if(!isset($_GET['id']) || !isset($_GET['status'])){
    $_SESSION['error'] = "The database could not process the execution parameters.";
    header("Location: approve_registrations.php");
    exit();
}

$registration_id = intval($_GET['id']);
$status = trim($_GET['status']);

/* 3. BOUNDARY RESTRICTIONS */
$allowed = ['Approved', 'Rejected'];

if(!in_array($status, $allowed)){
    $_SESSION['error'] = "The requested registry status configuration is invalid.";
    header("Location: approve_registrations.php");
    exit();
}

/* 4. EXTRACT PARISHIONER & EVENT DOSSIER */
$query = $conn->prepare("
    SELECT 
        registrations.id,
        registrations.status,
        users.name,
        users.phone,
        events.title,
        events.event_date
    FROM registrations
    JOIN users ON registrations.user_id = users.id
    JOIN events ON registrations.event_id = events.id
    WHERE registrations.id = ?
");

if (!$query) {
    $_SESSION['error'] = "Archival query preparation failed.";
    header("Location: approve_registrations.php");
    exit();
}

$query->bind_param("i", $registration_id);
$query->execute();
$result = $query->get_result();

if($result->num_rows == 0){
    $_SESSION['error'] = "The specified parishioner registry record could not be found.";
    $query->close();
    header("Location: approve_registrations.php");
    exit();
}

$data = $result->fetch_assoc();
$query->close();

$user_name   = $data['name'];
$phone       = trim($data['phone']);
$event_title = $data['title'];
$event_date  = date("l, F d, Y", strtotime($data['event_date']));

/* 5. EXECUTE DATABASE STATUS UPDATE */
$update = $conn->prepare("
    UPDATE registrations
    SET status = ?
    WHERE id = ?
");

if (!$update) {
    $_SESSION['error'] = "Database write pipeline failed.";
    header("Location: approve_registrations.php");
    exit();
}

$update->bind_param("si", $status, $registration_id);
$update_success = $update->execute();
$update->close();

if($update_success){

    /*
    ========================================
    6. REFINED LITURGICAL MESSAGING COPY
    ========================================
    */
    if($status == "Approved"){
        $message = "Peace be with you $user_name. Your registration for '$event_title' on $event_date has been APPROVED. We look forward to welcoming you. - Sacred Heart Catholic Church";
        $_SESSION['success'] = "Parish record signed. Approval SMS dispatched successfully.";
    } else {
        $message = "Hello $user_name. We regret to inform you that your registration for '$event_title' on $event_date could not be approved at this time. Please contact the parish administration office for details. - Sacred Heart Catholic Church";
        $_SESSION['warning'] = "Record updated. Rejection notification dispatched via SMS.";
    }

    /*
    ========================================
    7. AFRICA'S TALKING API INTELLIGENCE
    ========================================
    */
    // Config values should ideally live securely in your global config file:
    $username = "sandbox"; // Swap out with your live Africa's Talking username
    $apiKey   = "YOUR_API_KEY"; // Swap out with your production application API Key

    // Only attempt delivery if a phone number exists
    if (!empty($phone)) {
        $sms_data = array(
            'username' => $username,
            'to'       => $phone,
            'message'  => $message
        );

        // Adjust domain endpoint if working outside the regional sandbox cluster
        $url = ($username === "sandbox") 
            ? "https://api.sandbox.africastalking.com/version1/messaging"
            : "https://api.africastalking.com/version1/messaging";

        $headers = array(
            "apiKey: $apiKey",
            "Content-Type: application/x-www-form-urlencoded",
            "Accept: application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Safeguard against server hangs

        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // Optional debugging hook if alerts fail during tests
        if ($response === false) {
            $_SESSION['error'] = "Database updated successfully, but SMS gateway failed: " . $curl_error;
        }
    } else {
        $_SESSION['warning'] = "Registry record modified, but no valid phone number was found on this member profile.";
    }

} else {
    $_SESSION['error'] = "The system failed to alter the registration entry within the database.";
}

/* 8. RETURN BACK TO CORE LEDGER BLOCK */
header("Location: approve_registrations.php");
exit();
?>