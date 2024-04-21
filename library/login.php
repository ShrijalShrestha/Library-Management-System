<?php
$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    header("Location: login.php?error=Email and password are required");
    exit();
}

$con = new mysqli("localhost", "root", "", "library");

if ($con->connect_error) {
    die("Failed to connect: " . $con->connect_error);
} else {
    $stmt = $con->prepare("SELECT * FROM employee WHERE LoginEmail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        // Compare the passwords directly
        if ($password === $data['LoginPassword']) {
            // Start the session
            session_start();
            // Store user data in the session
            $_SESSION['name'] = $data['Name'];
            // Redirect to the home page
            header("Location: script.php");
            exit(); // Ensure script execution stops after redirection
        } else {
            header("Location: index.php?error=Incorrect password");
            exit();
        }
    } else {
        header("Location: index.php?error=User not found");
        exit();
    }
}

?>
