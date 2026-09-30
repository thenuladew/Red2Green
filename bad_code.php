<?php
$user_input = $_GET['id'];
$query = "SELECT * FROM data WHERE id = " . $user_input;
mysqli_query($conn, $query);
?>
