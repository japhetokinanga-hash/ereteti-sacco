<?php
require_once 'includes/db.php';

// Only a form can reach this page: a direct visit goes back to the list
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    header('Location: deliveries.php');
    exit;
}

$member = strtoupper(trim($_POST['member_no'] ?? ''));
$date = trim($_POST['delivery_date'] ?? '');
$session = strtoupper(trim($_POST['session'] ?? ''));
$litres = (float) trim($_POST['litres'] ?? '');

// Check them on the server: browser checks can be switched off
if (!preg_match('/^ER-\d{4}$/', $member)) {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode('Member number must look like ER-0002.'));
    exit;
}
if ($date === '') {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode('Date must not be blank.'));
    exit;
}
if ($session !== 'MORNING' && $session !== 'EVENING') {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode('Session must not be blank.'));
    exit;
}
if ($litres <= 0 || $litres > 60) {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode('Litres must be more than 0 and at most 60.'));
    exit;
}

// Find the farmer for that member number
$stmt = mysqli_prepare($conn, "SELECT farmer_id FROM farmer WHERE member_no = ?");
mysqli_stmt_bind_param($stmt, 's', $member);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$farmer = mysqli_fetch_assoc($result);

if (!$farmer) {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode('No farmer has the member number ' . $member . '.'));
    exit;
}

$farmer_id = (int) $farmer['farmer_id'];

// Already delivered in this session? Check first, so a duplicate
// is a message on the page, not a database error
$stmt = mysqli_prepare($conn, "SELECT delivery_id FROM milk_delivery WHERE farmer_id = ? AND delivery_date = ? AND session = ?");
mysqli_stmt_bind_param($stmt, 'iss', $farmer_id, $date, $session);
mysqli_stmt_execute($stmt);
if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode($member . ' has already delivered in the ' . strtolower($session) . ' session on ' . $date . '.'));
    exit;
}

// Insert: accepted is not listed, so its default (1) fills it in
$stmt = mysqli_prepare($conn, "INSERT INTO milk_delivery (farmer_id, delivery_date, session, litres) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'issd', $farmer_id, $date, $session, $litres);
try {
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode($member . ' has already delivered in the ' . strtolower($session) . ' session on ' . $date . '.'));
        exit;
    }
    throw $e;
}

header('Location: deliveries.php?date=' . urlencode($date) . '&saved=1');
exit;
