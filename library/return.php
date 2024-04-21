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

        // Prepare SQL statement to delete borrowing details from the database
        $sql = "DELETE FROM borrow WHERE title='$bookName' AND customer='$customer'";
        $updateSql = "UPDATE book SET `No. of Books` =  `No. of Books` + 1 WHERE title = '$bookName'";

        // Execute SQL statement
        if ($conn->query($sql) === TRUE && $conn->query($updateSql) === TRUE) {
            // Redirect back to the borrowing page after successfully returning the book
            header('Location: script.php');
            exit(); // Ensure that no more output is sent after the header redirection
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close connection
    $conn->close();
?>
