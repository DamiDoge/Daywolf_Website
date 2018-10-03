<?php
require_once "configDB.php";
header('Content-Type: application/json');

//Define variables
$user_name = "";
$password = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $input_name = trim($_POST["name"]);
    //Do not allow empty user names.
    if(empty($input_name))
    {
        echo makejson("ERROR: No name detected");
    }
    //Checks if spaces are existant within the username.
    elseif(strpos($input_name, ' ') !== false)
    {
        echo makejson("ERROR: No spaces allowed in username");
    }
    //Check for invalid characters in the username. Filter returns false if character contains invalid characters.
    elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9\-\_]+$/"))))
    {
        echo makejson("ERROR: Invalid username");
    }
    //If previous checks passed then ready the username for insertion.
    else
    {
        $user_name = $input_name;
    }
    
    $input_pass = trim($_POST["password"]);
    //First sanatize the password to prevent code injection.
    $input_pass = filter_var($input_pass, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP);
    //Do not allow empty passwords
    if(empty($input_pass))
    {
        echo makejson("ERROR: No password detected");
    }
    //Encode password before placing in the database.
    $password = password_hash($input_pass, PASSWORD_DEFAULT);
    
    //If hash function worked then store the user and password in the user database.
    if($password !== false && $user_name != "")
    {
        //Prepare an insert statement
        $sql = "INSERT INTO Users (user, password) VALUES (?,?)";
        
        if($stmt = $mysqli->prepare($sql)){
            //Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_user, $param_pass);
            //Set parameters
            $param_user = $user_name;
            $param_pass = $password;
            //Attempt to execute the statement
            if($stmt->execute())
            {
                //Successful insertion
                echo makejson("Login Created");
            }
            else
            {
                echo makejson("ERROR: Could not create user");
            }
        }
        $stmt->close();
    }
    $mysqli->close();
}
function makejson($message)
{
    $data = ['message' => $message];
    return json_encode( $data );
}
?>