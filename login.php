<?php
session_start();

// If logged in, redirect
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$path = $_SERVER['DOCUMENT_ROOT'] . "/aromafoods/";
require_once($path . 'connect.php');

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Email validation
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Password validation
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, user_uuid, email, password, role FROM users WHERE email = ?";
        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $user_uuid, $email, $hashed_password, $role);
                    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["uuid"] = $user_uuid;
                        $_SESSION["email"] = $email;
                        $_SESSION["role"] = $role;
                        header("location: /aromafoods/");
                    } else {
                        $password_err = "The password you entered was not valid.";
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $connection->close();
}
?>

<?php require($path . 'header.php'); ?>

<link rel="stylesheet" href="<?php echo $server; ?>css/test.css">

<div class="wrapper mx-auto">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>">
    <span class="error-msg"><?php echo $email_err; ?></span>
</div>

<div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" class="form-control">
    <span class="error-msg"><?php echo $password_err; ?></span>
</div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
        <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        <p>Forgot Password? <a href="reset-password.php">Reset Password</a>.</p>
    </form>
</div>

<?php require($path . 'footer.php'); ?>
