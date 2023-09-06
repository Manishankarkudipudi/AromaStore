<?php 

$server = 'http://' . $_SERVER['SERVER_NAME'] . '/aromafoods/';

$user_logged = false;


// Include your database connection
include_once 'connect.php'; 

$cartCount = 0; // default cart count

// If user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $user_logged = true;

    $user_uuid = $_SESSION['uuid']; 
    
    // Query to get count of items in cart for the logged-in user
    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM orders WHERE user_uuid = ?");
    $stmt->bind_param("i", $user_uuid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $cartCount = $row['count'];
    }
    $stmt->close();
}



?>
<!DOCTYPE html>
<html>
<head>
	<title>Aroma Foods </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $server; ?>css/test.css">

    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->

	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>





 

    <style type="text/css">
    body{ font: 14px sans-serif; }
    .wrapper{ width: 350px; padding: 20px; }

    /* Custom Navbar Styles */
    .navbar.navbar-expand-lg {
        height: 100px;
        background-color: #001f3f !important;  /* Using !important to ensure our style is applied */
    }
    .navbar.navbar-expand-lg .navbar-brand, 
    .navbar.navbar-expand-lg .nav-link {
        color: #fff; /* Making the text color white for better visibility on dark blue */
    }
    body {
    overflow-x: hidden;
}


</style>

</head>
<body>
<div class="content-wrapper d-flex flex-column">
            <nav class="navbar navbar-expand-lg w-100 bg-light">
            <a class="navbar-brand larger-font" href="<?php echo $server; ?>index.php">
                <img src="<?php echo $server; ?>img/products/icon.png" alt="Grocery Store Logo" style="width: 30px; height: 30px;"> Aroma Foods
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse justify-content-between" id="navbar">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item cart mr-4">
            <a class="cart-button bg-success" href="<?php echo $server; ?>cart.php">
                <i class="fa fa-shopping-cart text-white" style="font-size: 18px;"></i>
                <!-- Cart count is dynamically updated by JavaScript -->
                <span class="cart-count"><?= $cartCount ?></span>
            </a>
        </li>

        <?php 
        if ($user_logged) { ?>
            <li class="nav-item">
                <a class="nav-link btn signout-button" style="color: white;" href="<?php echo $server; ?>logout.php">
                    <span><i class="fa fa-sign-out" style="color: white;"></i></span> Sign Out
                </a>
            </li>
        <?php } else { ?>
            <li class="nav-item mr-sm-2">
                <a class="nav-link" style="background-color: #ff4d4d; color: white; padding: 10px 16px; border-radius: 20px;" href="<?php echo $server; ?>login.php">
                    <span><i class="fa fa-sign-in text-white"></i></span> Sign In
                </a>
            </li>
        <?php } ?>
    </ul>
</div>

        </nav>

      
    <div class="container-fluid page-container flex-grow-1">
            <!-- Rest of your page content -->
        </div>
</div>


<script src="<?php echo $server; ?>js/script.js"></script>
</body>
</html>