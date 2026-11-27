<?php
require_once 'includes/db.php';

// Delete is a form, sent by POST — never a link
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['delivery_id'])) {
    header('Location: deliveries.php');
    exit;
}

$delivery_id = (int) $_POST['delivery_id'];
$date = trim($_POST['delivery_date'] ?? '');

$stmt = mysqli_prepare($conn, "DELETE FROM milk_delivery WHERE delivery_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $delivery_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) !== 1) {
    header('Location: deliveries.php?date=' . urlencode($date) . '&error=' . urlencode('That delivery could not be deleted.'));
    exit;
}

header('Location: deliveries.php?date=' . urlencode($date) . '&deleted=1');
exit;
