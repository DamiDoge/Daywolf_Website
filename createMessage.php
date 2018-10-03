<?php
require_once "configDB.php";
require "authenticate.php";
header('Content-Type: application/json');

//Define variables
$sender = "";
$reciever = "";
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"]))
{
    $tryauth = authenticate($_POST["username"], $_POST["password"]);
    if($tryauth === true)
    {
        $input_sender= trim($_POST["username"]);
        //Do not allow empty user names.
        if(empty($input_sender))
        {
            echo makejson("ERROR: No name detected");
        }
        //Check for invalid characters in the username. Filter returns false if character contains invalid characters.
        elseif(!filter_var($input_sender, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9\-\_]+$/"))))
        {
            echo makejson("ERROR: Invalid username");
        }
        //If previous checks passed then ready the username for insertion.
        else
        {
            $sender = $input_sender;
        }

        $input_reciever= trim($_POST["reciever"]);
        //Do not allow empty user names.
        if(empty($input_reciever))
        {
            echo makejson("ERROR: No name detected");
        }
        //Check for invalid characters in the username. Filter returns false if character contains invalid characters.
        elseif(!filter_var($input_reciever, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9\-\_]+$/"))))
        {
            echo makejson("ERROR: Invalid username");
        }
        //If previous checks passed then ready the username for insertion.
        else
        {
            $reciever = $input_reciever;
        }

        $input_message = filter_var($_POST["message"], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP);
        //Do not allow empty messages.
        if(empty($input_message))
        {
            echo makejson("ERROR: No message detected");
        }
        //If previous checks passed then ready the message for insertion.
        else
        {
            $message = $input_message;
        }
    
    
        //Prepare an insert statement
        $sql = "INSERT INTO Messages (sender, reciever, message) VALUES (?,?,?)";
        
        if($stmt = $mysqli->prepare($sql))
        {
            //Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_sender, $param_reciever, $param_message);
            //Set parameters
            $param_sender = $sender;
            $param_reciever = $reciever;
            $param_message = $message;
            //Attempt to execute the statement
            if($stmt->execute())
            {
                //Successful insertion
                echo makejson("Message sent");
            }
            else
            {
                echo makejson("ERROR: Could not create message.");
            }
            $stmt->close();
        }
    $mysqli->close();
    }
    else
    {
        echo makejson($tryauth);
    }
}

function makejson($message)
{
    $data = ['message' => $message];
    return json_encode( $data );
}
?>