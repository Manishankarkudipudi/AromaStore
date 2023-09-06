<?php
require_once('fpdf/fpdf.php');
require 'vendor/autoload.php';

define('FPDF_FONTPATH', 'fpdf/font');
$pdf = new \setasign\Fpdi\Fpdi();


// Include your database connection file
require_once('connect.php');


// Initialize the session
session_start();



// Initialize PDF instance
$pdf = new \setasign\Fpdi\Fpdi();
$pdf->AddPage();
// Set a font
$pdf->SetFont('Arial', '', 12);

// Set the source file
$file = 'invoice.pdf';
// Specify the path to your logo
$logoPath = 'img/logo.jpg';  // Adjust this path to where your image is stored


// Add the logo to the PDF
$pdf->Image($logoPath, 100, 15, 60);  // Adjust coordinates (10, 10) and size (30) as needed


$pageCount = $pdf->setSourceFile($file);

// Import the first page of the source PDF
$tpl = $pdf->importPage(1);
$size = $pdf->getTemplateSize($tpl);

// Calculate margins to center the template
$centerX = ($pdf->GetPageWidth() - $size['width']) / 2;
$centerY = ($pdf->GetPageHeight() - $size['height']) / 2;

// Use the imported page and adjust the positioning to center
$pdf->useTemplate($tpl, $centerX, $centerY, $size['width'], $size['height']);

$totalPrice = 0;

// Fetch user's order details
$user_uuid = $_SESSION['uuid'];
$sql = "SELECT p.title, p.price, o.quantity FROM orders o INNER JOIN products p ON o.product_id = p.id WHERE o.user_uuid = ?";
if ($stmt = $connection->prepare($sql)) {
    $stmt->bind_param("s", $user_uuid);
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Set a position below the header for displaying the fetched data
        $yPosition = 125; // Adjust this according to where you want to start showing the data on the PDF
        while ($row = $result->fetch_assoc()) {
            $lineTotal = $row["price"] * $row["quantity"];
            $totalPrice += $lineTotal;
            $pdf->SetXY(40, $yPosition); // Adjust the X, Y coordinates as per your requirements
            $pdf->Write(0, $row["title"]);
            $pdf->SetXY(100, $yPosition); // Adjust the X, Y coordinates as per your requirements
            $pdf->Write(0, $row["quantity"]);
            $pdf->SetXY(140, $yPosition); // Adjust the X, Y coordinates for price alignment
            $pdf->Write(0, "$" . number_format($row["price"], 2));
            $pdf->SetXY(180, $yPosition); // Adjust the X, Y coordinates for price alignment
            $pdf->Write(0, "$" . number_format($lineTotal, 2));
            $yPosition += 10; // Adjust this according to the desired line height for each row
        }
        // Display total price after the loop, you might want to display this at the bottom of your invoice
        $pdf->SetXY(180, 201); // Adjust X, Y position for total price alignment
        $pdf->Write(0, "$" . number_format($totalPrice, 2));
        // After your while loop to fetch order details:
        $sql_username = "SELECT name 
        FROM users 
        WHERE user_uuid = ? 
        LIMIT 1";

        if ($stmt_username = $connection->prepare($sql_username)) {
        $stmt_username->bind_param("s", $user_uuid);
        if ($stmt_username->execute()) {
        $result_username = $stmt_username->get_result();
        if ($row_username = $result_username->fetch_assoc()) {
        $username = $row_username["name"];

        // Define a position in the PDF to display the Bill name
        $pdf->SetXY(50, 72); // Adjust the X, Y coordinates accordingly
        $pdf->Write(0, $username);
        // Define a position in the PDF to display the Ship name
        $pdf->SetXY(110, 72); // Adjust the X, Y coordinates accordingly
        $pdf->Write(0, $username);
        $pdf->SetXY(37,  38 ); // Adjust the X, Y coordinates accordingly
        $pdf->Write(0, "Brantford, Canada");
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetXY(37,  22 ); // Adjust the X, Y coordinates accordingly
        $pdf->Write(0, "Aroma Foods");
        // Get today's date in d-m-y format
        $currentDate = date('d   m   y'); // format: DD-MM-YY
        // Set the font size to be smaller
        $pdf->SetFont('Arial', '', 10); // Set the font to Arial, regular, with size 10
        // Set X and Y coordinates where you want to show the date
        $pdf->SetXY(175, 78); // Adjust the X, Y coordinates accordingly
        // Write the date to the PDF
        $pdf->Write(0, $currentDate);
        }
        $result_username->free();
        } else {
        // You can choose to handle errors differently
        die("Error fetching username: " . $stmt_username->error);
        }
        $stmt_username->close();
        }
        $result->free();
    } else {
        // You can choose to handle errors differently
        die("Error fetching orders: " . $stmt->error);
    }
    $stmt->close();
}

// Output the PDF to browser
$pdf->Output('I', 'invoice.pdf');

?>