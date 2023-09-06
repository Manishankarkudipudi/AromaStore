<?php
// Include config file
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/aromafoods/";
require_once($path . 'connect.php');

// Initialize the session
session_start();



if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    $url = 'http://' . $_SERVER['HTTP_HOST']; // Get server
    $url .= "/aromafoods/login.php";
    header('Location: ' . $url, TRUE, 302);
}

if (isset($_GET['deleteOrder'])) {
    $order_seq = $_GET['deleteOrder'];

    // Prepare delete statement
    $stmt = $connection->prepare("DELETE FROM orders WHERE order_seq = ? AND user_uuid = ?");
    $stmt->bind_param("is", $order_seq, $_SESSION["uuid"]);

    if ($stmt->execute()) {
        $_SESSION['delete_success'] = "Order deleted successfully.";
    } else {
        $_SESSION['delete_error'] = "Error deleting order. Try again later.";
    }
    

    $stmt->close();
}


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])){
    // Check if user has orders
    $stmt = $connection->prepare("SELECT * FROM orders WHERE user_uuid = ?");
    $stmt->bind_param("s", $_SESSION["uuid"]);
    $stmt->execute();
    $result = $stmt->get_result();
    //var_dump ($result->num_rows);
    //var_dump($result);
    //exit();
    if($result->num_rows > 0){
        // If orders exist, store a success message in session data.
        $_SESSION["checkout_success"] = "Your order has been successfully placed.";
    }
    else{
        // If no orders exist, store an error message in session data.
        $_SESSION["checkout_error"] = "No orders found. Please add items to your cart.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Online Aroma Foods </title>
</head>
<body>
    <?php require($path . 'header.php') ?>

    <div class="wrappercart">
        <h2>Cart</h2>
        <div class="cart-products">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order Id</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_uuid = $_SESSION['uuid'];
                    $sql = "SELECT o.order_seq, o.quantity, p.title, p.price FROM orders o INNER JOIN products p ON o.product_id = p.id WHERE o.user_uuid = ?  ORDER BY o.order_seq ASC";
                    if ($stmt = $connection->prepare($sql)) {
                        $stmt->bind_param("s", $user_uuid);
                        if ($stmt->execute()) {
                            $result = $stmt->get_result();
                            if ($result->num_rows == 0) {
                                echo "No Orders found for the user in our database.";
                                exit();
                            }
                            $counter = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>" . $counter . "</td><td>" . $row["title"] . "</td><td>" . $row["price"] . "</td><td>" . $row["quantity"] . "</td>";
                                echo "<td><a href='cart.php?deleteOrder=" . $row["order_seq"] . "'><span style='color: red; cursor: pointer;'>✖</span></a></td></tr>";
                                $counter++;
                            }
                            
                            $result->free();
                        } else {
                            echo "Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        echo "Error preparing statement: " . $connection->error;
                    }
                    $connection->close();
                    ?>
                </tbody>
            </table>
        </div>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="submit" name="checkout" class="btn btn-danger btn-checkout" value="Checkout">
        </form>
        <!-- Display messages -->
        <?php
        if(isset($_SESSION["checkout_success"])){
            echo '<div class="alert alert-success">';
            echo '<strong>Success! </strong>';
            echo $_SESSION["checkout_success"];
            echo ' View your <a href="invoice.php">invoice</a>.';
            echo '</div>';
            // Unset the success message
            unset($_SESSION["checkout_success"]);
        }
        if(isset($_SESSION["checkout_error"])){
            echo '<div class="alert alert-danger">';
            echo '<strong>Error! </strong>';
            echo $_SESSION["checkout_error"];
            echo '</div>';
            // Unset the error message
            unset($_SESSION["checkout_error"]);
        }
        if (isset($_SESSION["delete_success"])) {
            echo '<div class="alert alert-success">';
            echo '<strong>Success! </strong>';
            echo $_SESSION["delete_success"];
            echo '</div>';
            unset($_SESSION["delete_success"]);
        }
        if (isset($_SESSION["delete_error"])) {
            echo '<div class="alert alert-danger">';
            echo '<strong>Error! </strong>';
            echo $_SESSION["delete_error"];
            echo '</div>';
            unset($_SESSION["delete_error"]);
        }
        ?>
    </div>
    
    <?php require($path . 'footer.php') ?>
</body>
</html>
