<?php
$page_title = 'Apply for membership';
include 'includes/header.php';
?>

  <main>
    <section>
      <h2>Apply for Membership</h2>
      <p>Any dairy farmer within reach of one of our three routes may apply to join Ereteti. You will need your national ID number and a phone we can reach you on. Once your membership number is issued you may deliver from that day, and you are paid once a month for the litres we accept.</p>
      <form action="process-register.php" method="post">
        <fieldset>
          <legend>Your Details</legend>
          <label for="first-name">First name</label>
          <input type="text" id="first-name" name="first_name" required>
          <label for="last-name">Last name</label>
          <input type="text" id="last-name" name="last_name" required>
          <label for="phone">Phone number</label>
          <input type="tel" id="phone" name="phone" pattern="07[0-9]{8}" title="Ten digits, starting 07" placeholder="07XXXXXXXX" required>
        </fieldset>
        <fieldset>
          <legend>Your route</legend>
          <label for="route">Route</label>
          <select id="route" name="route" required>
            <option value="">Choose a route</option>
            <option value="1">Kiserian route</option>
            <option value="2">Ngong route</option>
            <option value="3">Rongai route</option>
          </select>
        </fieldset>
        <button type="submit">Apply to join</button>
      </form>
    </section>
  </main>

<?php include 'includes/footer.php'; ?>
