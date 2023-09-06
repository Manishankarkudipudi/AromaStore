<?php
// Start session
session_start();


// Include config file
require_once 'connect.php';

$cartCount = 0;  // Initialize the cart count to zero
$quantity = 1;



if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if user is logged in
    if(isset($_SESSION['uuid'])) {
        $order_id = md5(uniqid(rand(), true)); // Generate a UUID for order_id
        $user_uuid = $_SESSION['uuid'];
        $product_id = $_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $purchase_date = date("Y-m-d H:i:s"); // Current datetime

        // Check if user_uuid exists in users table
        $userCheck = $connection->prepare("SELECT * FROM users WHERE user_uuid = ?");
        $userCheck->bind_param("s", $user_uuid);
        $userCheck->execute();
        $result = $userCheck->get_result();
        if($result->num_rows === 0) exit('No rows');
        $userCheck->close();

        // Check if product_id exists in products table
        $productCheck = $connection->prepare("SELECT * FROM products WHERE id = ?");
        $productCheck->bind_param("i", $product_id);
        $productCheck->execute();
        $result = $productCheck->get_result();
        if($result->num_rows === 0) exit('No rows');
        $productCheck->close();

        //check if product already exists in users cart 
        $checkQuery = "SELECT quantity FROM orders WHERE user_uuid = ? AND product_id = ?";
        $checkStmt = $connection->prepare($checkQuery);
        $checkStmt->bind_param("si", $user_uuid, $product_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $existingOrder = $result->fetch_assoc();
        $checkStmt->close();

        // after assigning the variables, print them out to make sure they're correctly set
       // var_dump($order_id, $user_uuid, $product_id, $quantity, $purchase_date, $title, $price);
        //exit; // halt the script here, so it doesn't redirect
        //string(32) "9f01a1769bda865bede8aa09b6554ab7" string(36) "25c0454b-91ea-4131-b198-7bbbef178c26" string(1) "2" int(1) string(19) "2023-08-07 21:01:48" string(9) "Face Wash" string(2) "25"
        
        if ($existingOrder) {
            // Update the existing order by incrementing the quantity
            $newQuantity = $existingOrder['quantity'] + $quantity;  // Here $quantity is from the form input
            $updateQuery = "UPDATE orders SET quantity = ? WHERE user_uuid = ? AND product_id = ?";
            $updateStmt = $connection->prepare($updateQuery);
            $updateStmt->bind_param("isi", $newQuantity, $user_uuid, $product_id);
        
            if ($updateStmt->execute()) {
                // Product quantity updated successfully
                header("location: index.php");
            } else {
                die("Execute failed: (" . $updateStmt->errno . ") " . $updateStmt->error);
            }
            $updateStmt->close();
        
        } else {

                // Prepare an INSERT statement
                $query = "INSERT INTO orders (order_id, user_uuid, product_id, quantity, purchase_date) VALUES (?, ?, ?, ?, ?)";
                if($stmt = $connection->prepare($query)){
                    if ($stmt === false) {
                        die($connection->error);
                    }
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("ssiis", $order_id, $user_uuid, $product_id, $quantity, $purchase_date);

                    // Attempt to execute the prepared statement
                    
                    if($stmt->execute()){
                        // Redirect to product page
                        header("location: index.php");
                    } else{
                        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
                    }
                }
            } 
        // Close statement
        $stmt->close();
    }
    else{
        // Redirect to login page
        header("location: login.php");
    }
}


if($stmt->execute()){
    // Get the new cart count after adding the product
    $stmtCount = $connection->prepare("SELECT COUNT(*) as count FROM orders WHERE user_uuid = ?");
    $stmtCount->bind_param("s", $user_uuid);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    if($rowCount = $resultCount->fetch_assoc()){
        $cartCount = $rowCount['count'];
    }
    $stmtCount->close();

    // Return the new cart count as JSON
    echo json_encode(['cartCount' => $cartCount]);
    exit;
} 


// Close connection
$connection->close();
?>
