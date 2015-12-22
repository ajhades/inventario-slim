<?php
$page_title = 'All sale';
require_once('includes/load.php');

$id = $_GET['id'];
$update = update_status_venta($id); 

echo $update;
?>



