<?php
//Get the file name of every track in the music folder.
$musicURLS = scandir('music/');
//Remove the folder itself and the .. from the array
array_splice($musicURLS, 0, 2);
//Pull a random element from the array of tracks
$randkey = array_rand($musicURLS, 1);
//Send it back to the client in JSON so that the HTML can play the track.
echo json_encode($musicURLS[$randkey]);
?>