<?php
require_once "configDB.php";
require "authenticate.php";
header('Content-Type: application/json');

$messages = [];
$tryauth = authenticate($_POST["username"], $_POST["password"]);
if($tryauth === true)
{
    $username = $_POST["username"];
    //Select statement that grabs all messages user is a part of.
    $sql = "SELECT user FROM Users";

    if($stmt = $mysqli->prepare($sql))
    {

        if($stmt->execute())
        {
            //Store the results from the MySQL query.
            $result = $stmt->get_result();
            //Retrieve list of messages as an associative array.
            while($user = $result->fetch_array(MYSQLI_ASSOC))
            {
                $users[] = $user;
            }
            //mysqli_free_result($messages);
            echo json_encode($users);
        }
        else
        {
            echo makejson("ERROR: Statement didn't execute");
        }
        $stmt->close();
    }
    else
    {
        echo makejson("ERROR: Statement didn't prepare");
    }
    $mysqli->close();
}
else
{
    echo makejson($tryauth);
}
function makejson($message)
{
    $data = ['message' => $message];
    return json_encode( $data );
}
?>