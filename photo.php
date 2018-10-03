<?php
$photoURLS = scandir('photos/');
array_splice($photoURLS, 0, 2);
shuffle($photoURLS);
echo json_encode($photoURLS);
?>