<?php
require_once 'includes/db.php';             // 1 · CONNECT — loads $conn

// 2 · QUERY — ask the database for every route, in order
$sql = "SELECT route_name, collection_centre FROM route ORDER BY route_id";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Routes - Ereteti Dairy Sacco</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <!-- placeholder: swap for img/favicon.png -->
  <link rel="icon" type="image/png"
        href="/images/favicon-edairy.png">
        <script src="js/main.js" defer></script>
</head>
<body>

  <header>
    <h1>
      <!-- placeholder: swap for img/logo.png -->
      <img src="/images/logo-edairy.png"
           alt="Logo for the SACCO" class="logo" width="200" height="200">
     Our Routes
    </h1>
    <p>Milk collected twice a day, on three routes
       out of Ngong.</p>
  </header>

  <nav>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="routes.html">Routes</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="register.html">Join</a></li>
      <li><a href="deliveries.html">Deliveries</a>
      </li>
    </ul>
  </nav>
  <main>
    <label for="route-search">Find a route or a centre</label>
<input type="search" id="route-search" placeholder="e.g. Ngong">
<p id="route-count" aria-live="polite"></p>

<?php
// 3 · FETCH the next row, and 4 · SHOW it — once for every row
while ($row = mysqli_fetch_assoc($result)) {
    echo '<section>';
    echo '<h2>' . $row['route_name'] . ' route</h2>';
    echo '<p>Milk is cooled at ' . $row['collection_centre'] . '.</p>';
    echo '</section>';
}
?>
    </main>
    <aside>
<h2>Notices</h2>
<ul>
<li>Milk is paid at <strong>KSh 52.00 a litre</strong> from
1 October 2026.</li>
<li>Annual General Meeting: Saturday 17 October, 10 a.m., at the
Kiserian collection centre.</li>
<li>New members: <a href="register.html">apply to join</a>. You need
a national ID and a bank account.</li>
</ul>
</aside>
    <footer>
    <p>Ereteti Dairy Sacco, PO Box 4021, Ngong.</p>
    <p>Site built by Japhet Kinanga, DBT Year 1, 2026.</p>
</footer>
</body>

</html>
