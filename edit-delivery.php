<?php
require_once 'includes/db.php';

$page_title = 'Edit a delivery';
$error = '';
$delivery = null;

// Saving the form: only litres and accepted can be changed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_id = (int) ($_POST['delivery_id'] ?? 0);
    $litres = (float) trim($_POST['litres'] ?? '');
    $accepted = isset($_POST['accepted']) ? 1 : 0;

    // Fetch the delivery again, so we know its date and member number
    $stmt = mysqli_prepare($conn, "SELECT d.delivery_id, d.delivery_date, d.litres, d.accepted, f.member_no FROM milk_delivery d JOIN farmer f ON f.farmer_id = d.farmer_id WHERE d.delivery_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $delivery_id);
    mysqli_stmt_execute($stmt);
    $delivery = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$delivery) {
        $error = 'No delivery with that number.';
    } elseif ($litres <= 0 || $litres > 60) {
        $error = 'Litres must be more than 0 and at most 60.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE milk_delivery SET litres = ?, accepted = ? WHERE delivery_id = ?");
        mysqli_stmt_bind_param($stmt, 'dii', $litres, $accepted, $delivery_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {
            header('Location: deliveries.php?date=' . urlencode($delivery['delivery_date']) . '&updated=1');
            exit;
        }
        $error = 'That delivery could not be changed.';
    }
} else {
    // Showing the form: find the delivery by its number
    $delivery_id = (int) ($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "SELECT d.delivery_id, d.delivery_date, d.litres, d.accepted, f.member_no FROM milk_delivery d JOIN farmer f ON f.farmer_id = d.farmer_id WHERE d.delivery_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $delivery_id);
    mysqli_stmt_execute($stmt);
    $delivery = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$delivery) {
        $error = 'No delivery with that number.';
    }
}

include 'includes/header.php';
echo '<main>';

if ($error !== '' && !$delivery) {
    echo '<p>' . htmlspecialchars($error) . '</p>';
    echo '<p><a href="deliveries.php">Back to deliveries</a></p>';
} else {
    echo '<h2>Edit a delivery</h2>';

    if ($error !== '') {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }

    echo '<form action="edit-delivery.php" method="post">';
    echo '<input type="hidden" name="delivery_id" value="' . (int) $delivery['delivery_id'] . '">';

    echo '<label for="member">Member number</label>';
    echo '<input type="text" id="member" name="member_no" value="' . htmlspecialchars($delivery['member_no']) . '" readonly>';

    echo '<label for="delivery-date">Date</label>';
    echo '<input type="date" id="delivery-date" name="delivery_date" value="' . htmlspecialchars($delivery['delivery_date']) . '" readonly>';

    echo '<label for="litres">Litres</label>';
    echo '<input type="number" id="litres" name="litres" value="' . htmlspecialchars($delivery['litres']) . '" min="0.01" step="0.01" required>';

    echo '<label for="accepted">';
    echo '<input type="checkbox" id="accepted" name="accepted" value="1"' . ($delivery['accepted'] == 1 ? ' checked' : '') . '>';
    echo ' Accepted</label>';

    echo '<button type="submit">Save changes</button>';
    echo ' <a href="deliveries.php?date=' . htmlspecialchars($delivery['delivery_date']) . '">Cancel</a>';
    echo '</form>';
}

echo '</main>';
include 'includes/footer.php';
