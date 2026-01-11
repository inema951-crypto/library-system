<?php
if (isset($_SESSION['full_name'])) {
    $name = htmlspecialchars($_SESSION['full_name']);
    $hour = date("H");
    if ($hour < 12) {
        $greeting = "What's up?";
    } elseif ($hour < 18) {
        $greeting = "Bon aprèm!";
    } else {
        $greeting = "Enjoy your evening";
    }
    echo "<div class='welcome-message'><h2>$greeting, $name</h2></div>";
} else {
    echo "<div class='welcome-message'><h2>Welcome, Guest!</h2></div>";
}
?>
