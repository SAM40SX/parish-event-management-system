<?php
require('../TCPDF-main/TCPDF-main/tcpdf.php');
include("../config.php");

/* COUNTS */
$events_count = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM events")
)['total'];

$users_count = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM users")
)['total'];

/* GET EVENTS */
$events = mysqli_query($conn,"
    SELECT *
    FROM events
    ORDER BY event_date DESC
");

/* GET USERS */
$users = mysqli_query($conn,"
    SELECT *
    FROM users
    ORDER BY id DESC
");

$pdf = new TCPDF();
$pdf->SetCreator('Parish Event Management System');
$pdf->SetAuthor("Samuel Ng'ang'a Mwihaki");
$pdf->SetTitle('Parish Event Management Report');
$pdf->AddPage();

/* BUILD HTML */
$html = '

<h1 style="text-align:center;color:#002147;">
PARISH EVENT MANAGEMENT SYSTEM
</h1>

<h3 style="text-align:center;color:#d4af37;">
Church System Development Report
</h3>

<hr>

<p><strong>Student Name:</strong> Samuel Ng\'ang\'a Mwihaki</p>
<p><strong>Admission Number:</strong> 115950</p>
<p><strong>Project Supervisor:</strong> David Mwangi</p>
<p><strong>Project Title:</strong> Parish Event Management System</p>
<p><strong>Date Generated:</strong> '.date("F d, Y h:i A").'</p>

<h2>Project Introduction</h2>

<p>
The Parish Event Management System is a web-based church administration
platform developed to assist church leaders in managing parish events,
attendance tracking, member registration, announcements and reporting.
</p>

<h2>Project Objectives</h2>

<ul>
<li>Automate church event management.</li>
<li>Digitize member registration.</li>
<li>Improve church communication.</li>
<li>Track attendance records.</li>
<li>Generate management reports.</li>
</ul>

<h2>System Statistics</h2>

<table border="1" cellpadding="6">
<tr style="background-color:#002147;color:white;">
<th>Total Events</th>
<th>Total Members</th>
</tr>

<tr>
<td>'.$events_count.'</td>
<td>'.$users_count.'</td>
</tr>
</table>

<br><br>

<h2>Registered Members</h2>

<table border="1" cellpadding="5">

<tr style="background-color:#002147;color:white;">
<th width="10%">ID</th>
<th width="35%">Name</th>
<th width="35%">Email</th>
<th width="20%">Role</th>
</tr>
';

/* USERS TABLE */
while($user = mysqli_fetch_assoc($users)){

    $html .= '
    <tr>
        <td>'.$user['id'].'</td>
        <td>'.htmlspecialchars($user['name']).'</td>
        <td>'.htmlspecialchars($user['email']).'</td>
        <td>'.(isset($user['role']) ? $user['role'] : 'member').'</td>
    </tr>';
}

$html .= '
</table>

<br><br>

<h2>Parish Events</h2>

<table border="1" cellpadding="5">

<tr style="background-color:#002147;color:white;">
<th width="25%">Title</th>
<th width="30%">Description</th>
<th width="15%">Date</th>
<th width="10%">Time</th>
<th width="20%">Location</th>
</tr>
';

/* EVENTS TABLE */
while($event = mysqli_fetch_assoc($events)){

    $html .= '
    <tr>
        <td>'.htmlspecialchars($event['title']).'</td>
        <td>'.htmlspecialchars($event['description']).'</td>
        <td>'.htmlspecialchars($event['event_date']).'</td>
        <td>'.htmlspecialchars($event['event_time']).'</td>
        <td>'.htmlspecialchars($event['location']).'</td>
    </tr>';
}

$html .= '
</table>

<br><br>

<div style="border:1px dashed #d4af37;padding:10px;text-align:center;">
<i>"Everything should be done decently and in good order."</i><br>
<b>1 Corinthians 14:40</b>
</div>

<br><br><br>

<table width="100%">
<tr>
<td width="45%">
_________________________<br>
<strong>Samuel Ng\'ang\'a Mwihaki</strong><br>
Student Developer
</td>

<td width="10%"></td>

<td width="45%">
_________________________<br>
<strong>David Mwangi</strong><br>
Project Supervisor
</td>
</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('Parish_Event_Management_Report.pdf','D');
exit();
?>