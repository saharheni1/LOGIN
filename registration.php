<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");// This code checks whether the "user" session variable is set. If it is set, it means the user is already logged in, so the code redirects the user to the "index.php" page (which could be the main page of your website or any other page that you consider the default page for logged-in users)
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        //The if (isset($_POST["submit"])) statement checks if the form has been submitted by the user, and if so, retrieves the values of the fullname, email, password, and repeat_password fields from the $_POST superglobal array.
        if (isset($_POST["submit"])) {
           $fullName = $_POST["fullname"];
           $email = $_POST["email"];
           $password = $_POST["password"];
           $passwordRepeat = $_POST["repeat_password"];
           //The code then uses the password_hash() function to create a hashed version of the user's password for secure storage in the database.
           $passwordHash = password_hash($password, PASSWORD_DEFAULT);
           //After this, the code initializes an empty $errors array to store any validation errors that may occur during the form submission process.
           $errors = array();//This code initializes an empty array $errors to store any validation errors that may occur during the form submission process.
           
           if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
            array_push($errors,"All fields are required");
           }
           if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
           }
           if (strlen($password)<8) {
            array_push($errors,"Password must be at least 8 charactes long");
           }
           if ($password!==$passwordRepeat) {
            array_push($errors,"Password does not match");
           }
           require_once "database.php";
           $sql = "SELECT * FROM users WHERE email = '$email'";
           $result = mysqli_query($conn, $sql);
           $rowCount = mysqli_num_rows($result);
           //This code executes an SQL query to check if the email provided by the user already exists in the database. It uses the `mysqli_query()` function to execute the query and passes the database connection `$conn` and the SQL query `$sql` as arguments.

//The query selects all rows from the `users` table where the `email` column matches the email provided by the user. The code then uses the `mysqli_num_rows()` function to count the number of rows returned by the query. If the count is greater than 0, it means that the email already exists in the database, so the code adds an error message "Email already exists!" to the `$errors` array using the `array_push()` function. 

// This is an important validation check to ensure that a user cannot create multiple accounts with the same email address.
           if ($rowCount>0) {
            array_push($errors,"Email already exists!");
           }
           if (count($errors)>0) {
            foreach ($errors as  $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
           }else{
            
            $sql = "INSERT INTO users (full_name, email, password) VALUES ( ?, ?, ? )";
            $stmt = mysqli_stmt_init($conn);
            //This code initializes a new mysqli_stmt object and assigns it to the $stmt variable. The mysqli_stmt_init() function is used to initialize a new statement object, which is then used to prepare and execute SQL statements with parameterized queries.
            $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt,"sss",$fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>You are registered successfully.</div>";
            }else{
                die("Something went wrong");
            }
           }
          

        }
        ?>
        <form action="registration.php" method="post">
        <h1 class="form-title">Registration</h1>
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="emamil" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
        <div class="here"><p>Already Registered <a href="login.php" class="log">Login Here</a></p></div>
      </div>
    </div>
</body>
</html>