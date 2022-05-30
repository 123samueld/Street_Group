<?php
include('./includes/class-autoloader.inc.php');

$homeowner_file = new homeowner_Model;
return $homeowner_file->csv_to_array();
