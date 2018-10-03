<?php
require_once "configDB.php";
require "authenticate.php";
header('Content-Type: application/json');

//Define variables
$user = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"]))
{
    $tryauth = authenticate($_POST["username"], $_POST["password"]);
    if($tryauth === true)
    {
        $input_user= trim($_POST["username"]);
        //Do not allow empty user names.
        if(empty($input_user))
        {
            echo makejson("ERROR: No name detected");
        }
        //Check for invalid characters in the username. Filter returns false if character contains invalid characters.
        elseif(!filter_var($input_user, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9\-\_]+$/"))))
        {
            echo makejson("ERROR: Invalid username");
        }
        //If previous checks passed then ready the username for deletion.
        else
        {
            $user = $input_user;
        }
    
        $input_pass = trim($input_pass);
        //First sanatize the password to prevent code injection.
        $input_pass = filter_var($input_pass, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP);
        //Do not allow empty passwords
        if(empty($input_pass))
        {
            echo "ERROR: No password detected";
        }

        //Prepare an delete statement
        $sql = "DELETE FROM Users WHERE user = ?";
        
        if($stmt = $mysqli->prepare($sql))
        {
            //Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_user);
            //Set parameters. This assures that the user who authenticates is the only one that can be deleted.
            $param_user = $user;
            //Attempt to execute the statement
            if($stmt->execute())
            {
                //Successful deletion
                echo makejson("User deleted");
            }
            else
            {
                echo makejson("ERROR: Could not delete user.");
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