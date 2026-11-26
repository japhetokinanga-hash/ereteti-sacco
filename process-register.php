<?php
// post, because the form sends personal details that should not appear in the address bar.
$page_title = 'Application received';
include 'includes/header.php';

$routes = ['1' => 'Kiserian', '2' => 'Ngong', '3' => 'Rongai'];

echo '<main>';

if (isset($_POST['first_name'])) {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $phone = $_POST['phone'];
    $route = $routes[$_POST['route']] ?? '';

    echo '<h2>Thank you, ' . $first . ' ' . $last . '</h2>';
    echo '<p>We have your application to join the ' . $route . ' route.</p>';
    echo '<p>The office will call you on ' . $phone . ' when your member number is ready.</p>';

    echo '<ul>';
    foreach ($_POST as $field => $value) {
        echo '<li>' . $field . ': ' . $value . '</li>';
    }
    echo '</ul>';
} else {
    echo '<h2>No application</h2>';
    echo '<p>This page answers the membership form. Fill it in first.</p>';
    echo '<p><a href="register.php">Go to the membership form</a></p>';
}

echo '</main>';

include 'includes/footer.php';
?>
