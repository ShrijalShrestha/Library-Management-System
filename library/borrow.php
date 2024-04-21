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
    $author = $_POST['author'];
    $genreId = $_POST['genreId'];
    $number = $_POST['number'];
    $returnDate = $_POST['returnDate'];
    $customerName = $_POST['customer'];
    $employeeName = $_POST['employee'];

    // Prepare SQL statement to insert borrowing details into the database
    $borrowSql = "INSERT INTO borrow (title, author, genre_id, customer, employeeName, returnDate) 
                VALUES ('$bookName', '$author', '$genreId', '$customerName', '$employeeName', '$returnDate')";

    // Execute SQL statement to insert borrowing details
    if ($conn->query($borrowSql) === TRUE) {
        // Get the ID of the last inserted borrow record
        $borrowId = $conn->insert_id;

        // Decrease the available number of books
        $number--;

        // Prepare SQL statement to update the number of available books in the database
        $updateSql = "UPDATE book SET `No. of Books` = $number WHERE title = '$bookName'";

        // Execute SQL statement to update the number of available books
        if ($conn->query($updateSql) === TRUE) {
            // Check if the customer already exists
            $checkCustomerSql = "SELECT * FROM customer WHERE name = '$customerName'";
            $result = $conn->query($checkCustomerSql);

            if ($result->num_rows == 0) {
                // Prepare SQL statement to insert customer details into the database
                $customerSql = "INSERT INTO customer (name, email, address) 
                                VALUES ('$customerName', 'customer@gmail.com', 'India')";

                // Execute SQL statement to insert customer details
                if ($conn->query($customerSql) !== TRUE) {
                    echo "Error: " . $customerSql . "<br>" . $conn->error;
                }
            }

            // Redirect to script.php after successful insertion
            header('Location: script.php');
            exit(); // Ensure script execution stops after redirection
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Error: " . $borrowSql . "<br>" . $conn->error;
    }
    
}

// Close connection
$conn->close();
?>
