<?php
require_once "configDB.php";

function authenticate($input_name, $input_pass)
{

    /*connect to the MySQL database if possible*/
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if($mysqli === false)
    {
        die("ERROR: Could not connect. " . $mysqli->connect_error);
    }

    //Define variables
    $user_name = "";
    $password = "";

    $input_name = trim($input_name);
    //Do not allow empty user names.
    if(empty($input_name))
    {
        return "ERROR: No name detected";
    }
    //Check for invalid characters in the username. Filter returns false if character contains invalid characters.
    elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9\-\_]+$/"))))
    {
        return "ERROR: Invalid username";
    }
    //If previous checks passed then ready the username for insertion.
    else
    {
        $user_name = $input_name;
    }
    
    $input_pass = trim($input_pass);
    //First sanatize the password to prevent code injection.
    $input_pass = filter_var($input_pass, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP);
    //Do not allow empty passwords
    if(empty($input_pass))
    {
        return "ERROR: No password detected";
    }

    else
    {
        //Prepare a statement to grab users credentials.
        $sql = "SELECT * FROM Users WHERE user = ?";

        if($stmt = $mysqli->prepare($sql))
        {
            //Bind variables to statement
            $stmt->bind_param("s", $param_name);
            $param_name = $input_name;
            //Execute the statement
            if($stmt->execute())
            {
                $result = $stmt->get_result();

                if($result->num_rows == 1)
                {
                    //Store returned row into an associative array
                    $row = $result->fetch_assoc();
                    $base_name = $row["user"];
                    $base_pass = $row["password"];
                    $messpass = $base_pass;
                    //check that the user and the password match records
                    if(password_verify($input_pass, $base_pass))
                    {
                        return true;
                    }
                    else
                    {
                        return "Incorrect username or password";
                    }
                }
                if($result->num_rows == 0)
                {
                    return "Incorrect username or password";
                }
                else
                {
                    return "ERROR: Multiple of a person exist. WTF.";
                }
            }
            else
            {
                return "ERROR: Search failed";
            }
        }
        //MySQL cleanup
        $stmt->close();
        $mysqli->close();
    }
}
?>