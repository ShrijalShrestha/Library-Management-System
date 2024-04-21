<?php
    // Start the session
    session_start();

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "library";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve book details and customer name from $_POST
        $bookName = $_POST['bookName'];
        $customer = $_POST['customer'];

        // Prepare SQL statement to delete borrowing details and corresponding fine details
        $deleteSql = "DELETE b, f FROM borrow AS b 
                      LEFT JOIN fine AS f ON b.IdNo = f.transactionId
                      WHERE b.title = ? AND b.customer = ?";
        
        // Prepare and bind parameters
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("ss", $bookName, $customer);

        // Execute SQL statement
        if ($stmt->execute()) {
            // Redirect back to the borrowing page after successfully returning the book
            header('Location: script.php');
            exit(); // Ensure that no more output is sent after the header redirection
        } else {
            echo "Error deleting record: " . $stmt->error;
        }

        // Close prepared statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
?>
