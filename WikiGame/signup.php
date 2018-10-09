<?php
//add our database connection script
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

//process the form
if(isset($_POST['signupBtn'])) {
    //initialise the array to store any error message from the form
    $form_errors = array();

    //form validation
    $required_fields = array('email', 'username', 'password');

    //call the function to check empty field and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    //fields that requires checking for minimum length
    $fields_to_check_length = array('username' => 4, 'password' => 6);

    //call the function to check minimum required length and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

    //email validation / merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_email($_POST));

    //check if error array is empty, if yes process form data and insert record
    if (empty($form_errors)){

        //collect form data and store in variables
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        //hashing the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {

            //create SQL insert statement
            $sqlInsert = "INSERT INTO users (username, email, password, join_date)
                  VALUES (:username, :email, :password, now(), 0)";

            //use PDO prepared to sanitize data
            $statement = $db->prepare($sqlInsert);

            //add the data into the database
            $statement->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));

            //check if one new row was created
            if ($statement->rowCount() == 1) {
                $result = "<p style='padding: 20px; border: 1px solid black; color: green;'> Registration successful</p>";
            }
        }catch (PDOException $ex){
            $result = "<p style='padding: 20px; border: 1px solid black; color: red;'> An error occurred: " . $ex->getMessage() . " </p>";
        }
    }
    else{
        if(count($form_errors) == 1){
            $result = "<p style='color: red;'> There was 1 error in the form<br>";
        }else{
            $result = "<p style='color: red;'> There were " .count($form_errors). " errors in the form <br>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Register Page</title>
</head>
<body>
<h2>Wiki Game</h2>

<h3>Registration Form</h3>


<?php if(isset($result)) echo $result; ?>
<?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
<form method="post" action="">
    <table>
        <tr><td>Email:</td> <td> <input type="text" value="" name="email"></td></tr>
        <tr><td>Username:</td> <td> <input type="text" value="" name="username"></td></tr>
        <tr><td>Password:</td> <td> <input type="password" value="" name="password"></td></tr>
        <tr><td></td><td><input style="float: right"; type="submit" name="signupBtn" value="Sign up"></td></tr>

    </table>
</form>

<p><a href="index.php">Back</a></p>
</body>
</html>