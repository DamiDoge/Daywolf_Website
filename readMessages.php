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
    $sql = "SELECT * FROM Messages WHERE sender = ? OR reciever = ? ORDER BY timestamp";
    if(isset($_POST["timestamp"]))
    {
        $sql = "SELECT * FROM Messages WHERE (sender = ? OR reciever = ?) AND timestamp > ? ORDER BY timestamp";
    }

    if($stmt = $mysqli->prepare($sql))
    {
        //Bind parameters
        $stmt->bind_param("ss", $param_sender, $param_reciever);
        //Set parameters
        $param_sender = $username;
        $param_reciever = $username;
        if(isset($_POST["timestamp"]))
        {
            $stmt->bind_param("sss", $param_sender, $param_reciever, $param_time);
            $param_time = $_POST["timestamp"];
        }

        if($stmt->execute())
        {
            //Store the results from the MySQL query.
            $result = $stmt->get_result();
            //Retrieve list of messages as an associative array.
            while($message = $result->fetch_array(MYSQLI_ASSOC))
            {
                $messages[] = $message;
            }
            //mysqli_free_result($messages);
            echo json_encode($messages);
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