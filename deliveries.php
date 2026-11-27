<?php
require_once 'includes/db.php';

$page_title = 'Deliveries';

// The visitor chooses the day in the address, e.g. deliveries.php?date=2026-10-31
$date = $_GET['date'] ?? '2026-10-12';

// Prepared query: anything the visitor typed goes in as a value, never as SQL
$stmt = mysqli_prepare($conn, "SELECT d.delivery_id, f.member_no, f.first_name, f.last_name, d.session, d.litres, d.accepted
        FROM milk_delivery d
        JOIN farmer f ON f.farmer_id = d.farmer_id
        WHERE d.delivery_date = ?
        ORDER BY f.member_no, d.session");
mysqli_stmt_bind_param($stmt, 's', $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$count = mysqli_num_rows($result);

// Accepted total for this day, worked out on the server
$total = 0;
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    if ($row['accepted'] == 1) {
        $total = $total + (float) $row['litres'];
    }
}

include 'includes/header.php';
?>

  <main>
    <p><?php echo $count; ?> deliveries on this day.</p>

    <p>Milk received on <?php echo htmlspecialchars(date('l j F Y', strtotime($date))); ?>, from all three routes —
    Kiserian, Ngong and Rongai. Both sessions are listed: the morning
    collection from 6.30am and the evening collection from 4.00pm.</p>

<?php
if (isset($_GET['saved'])) {
    echo '<p>The delivery was recorded.</p>';
}
if (isset($_GET['updated'])) {
    echo '<p>The delivery was changed.</p>';
}
if (isset($_GET['deleted'])) {
    echo '<p>The delivery was deleted.</p>';
}
if (isset($_GET['error'])) {
    echo '<p>' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

    <h2>Choose a day</h2>
    <form action="deliveries.php" method="get">
      <label for="date">Date</label>
      <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
      <button type="submit">Show</button>
    </form>

    <h2>Record a delivery</h2>

    <form id="delivery-form" action="process-delivery.php" method="post">
      <label for="member">Member number</label>
      <input type="text" id="member" name="member_no"
             placeholder="ER-0000" required>
      <p id="member-message" class="message"
         aria-live="polite"></p>

      <label for="delivery-date">Date</label>
      <input type="date" id="delivery-date" name="delivery_date"
             value="<?php echo htmlspecialchars($date); ?>" required>

      <label for="session">Session</label>
      <select id="session" name="session" required>
        <option value="">Choose a session</option>
        <option value="MORNING">Morning</option>
        <option value="EVENING">Evening</option>
      </select>

      <label for="litres">Litres</label>
      <input type="number" id="litres" name="litres"
             min="0.01" step="0.01" required>
      <p id="litres-message" class="message"
         aria-live="polite"></p>

      <label for="accepted">
        <input type="checkbox" id="accepted" name="accepted" value="1" checked>
        Accepted
      </label>

      <button type="submit">Record delivery</button>
      <p id="form-message" aria-live="polite"></p>
    </form>

    <div class="table-scroll">
      <table>
        <caption>Milk received, <?php echo htmlspecialchars(date('l j F Y', strtotime($date))); ?></caption>

        <thead>
          <tr>
            <th scope="col">Member</th>
            <th scope="col">Name</th>
            <th scope="col">Session</th>
            <th scope="col">Litres</th>
            <th scope="col">Accepted</th>
            <th scope="col"></th>
          </tr>
        </thead>

        <tbody>
<?php
if ($count === 0) {
    echo '<tr><td colspan="6">No deliveries recorded on this day.</td></tr>';
} else {
    foreach ($rows as $row) {
        if ($row['accepted'] == 1) {
            $accepted = 'Yes';
        } else {
            $accepted = 'No';
        }
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['member_no']) . '</td>';
        echo '<td>' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . '</td>';
        echo '<td>' . htmlspecialchars(ucfirst(strtolower($row['session']))) . '</td>';
        echo '<td>' . htmlspecialchars($row['litres']) . '</td>';
        echo '<td>' . $accepted . '</td>';
        echo '<td>';
        echo '<a href="edit-delivery.php?id=' . (int) $row['delivery_id'] . '">Edit</a>';
        echo ' <form action="delete-delivery.php" method="post" onsubmit="return confirm(\'Delete this delivery?\');">';
        echo '<input type="hidden" name="delivery_id" value="' . (int) $row['delivery_id'] . '">';
        echo '<input type="hidden" name="delivery_date" value="' . htmlspecialchars($date) . '">';
        echo '<button type="submit">Delete</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
}
?>
        </tbody>

        <tfoot>
          <tr>
            <th colspan="4" scope="row">Accepted today</th>
            <td id="accepted-total"><?php echo number_format($total, 2); ?></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

<?php if ($date === '2026-10-12') { ?>
    <p>Joseph Kamau's morning load failed the alcohol test and was
    rejected, so it is not counted in the day's total. It is still
    listed: a rejected load is recorded, never deleted, because the
    farmer has a right to see what happened to their milk.</p>
<?php } ?>
  </main>

<?php include 'includes/footer.php'; ?>
