<?php
require_once 'C:/xampp/htdocs/aromafoods/vendor/autoload.php';

// Include config file
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/aromafoods/";
require_once($path . 'connect.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use Ramsey\Uuid\Uuid;
 
// Define variables and initialize with empty values
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";
$name_err = "";
$name= "";

 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check for empty name
 if(empty(trim($_POST["name"]))){
     $name_err = "Please enter your name.";
     echo "Name error set.";  // Debugging line
 } elseif (preg_match('/\d/', $_POST["name"])) {
     $name_err = "Name should not contain numbers.";
     echo "Name contains numbers.";  // Debugging line
 } else {
     $name = trim($_POST["name"]);
 }
 
     // Validate email
     if(empty(trim($_POST["email"]))){
         $email_err = "Please enter an email.";
     } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
         $email_err = "Please enter a valid email format.";
     } else {
         // Prepare a select statement
         $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($connection, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already registered.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    

   
// Check input errors before inserting in database
if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($name_err)){
    
    // Prepare an insert statement
    $sql = "INSERT INTO users (id, user_uuid, name, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if($stmt = mysqli_prepare($connection, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "issssss", $param_id, $param_uuid, $param_name, $param_email, $param_password, $param_phone, $param_role);

        // Set parameters
        $param_id = null; // Auto-increment id
        $param_uuid = Uuid::uuid4()->toString(); // Generate new UUID
        $param_name = $name;
        $param_email = $email;
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
        $param_phone = null; // Set to NULL for now, update this if you're taking phone input
        $param_role = "customer"; // Set default role to customer
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.\n";
                print_r($stmt->error_list);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($connection);
}
?>
 
<?php require($path . 'header.php') ?>
<link rel="stylesheet" href="<?php echo $server; ?>css/test.css">

    <div class="wrapper mx-auto">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
        <span class="help-block"><?php echo $name_err; ?></span>
            </div>

    
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" required>
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" required>
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
    <div class="mb-3">
        <input type="submit" class="btn btn-primary btn-lg btn-block" value="Submit">
    </div>
    <div>
        <input type="reset" class="btn btn-default btn-lg btn-block" value="Reset">
    </div>
</div>


            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    

<?php require($path . 'footer.php') ?>
