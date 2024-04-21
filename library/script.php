<?php
session_start();
if (isset($_SESSION['name'])) {
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SRM Library</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">SRM Library</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input class="form-control mr-sm-2" id="searchTxt" type="search" placeholder="Search" name="search"
                    aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" value="search">Search</button>
            </form>
        </div>
    </nav>
    

    <div class="container my-3">
        <h1 style="color: green;">SRM Library</h1>
        <hr>
        <?php
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
            if(isset($_POST['search'])) {
                $search = $_POST['search'];
            
                // Prepare a statement to select books matching the search query
                $stmt = $conn->prepare("SELECT b.*, g.genre_name FROM book AS b 
                JOIN genre AS g ON b.genre_id = g.genre_id WHERE title LIKE ?");
                // Check if the statement was prepared successfully
                if ($stmt === false) {
                    http_response_code(500);
                    echo "Error: Unable to prepare statement";
                    exit();
                }
            
                // Bind parameters
                $search_param = "%$search%";
                $stmt->bind_param("s", $search_param);
                
                // Execute the statement
                if ($stmt->execute()) {
                    // Get result
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        // Output table header if any search book is in stock
                        $anyInStock = false;
                        while($row = $result->fetch_assoc()) {
                            if($row["No. of Books"] > 0){
                                $anyInStock = true;
                                break;
                            }
                        }
                        if($anyInStock) {
                            echo '<h4>Found results</h4>';
                            echo '<table class="table" border=1 cellspacing=0>
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>ISBN</th>
                                            <th>Genre</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        }
                        else {
                            echo "No results found";
                        }
                        while($row = $result->fetch_assoc()) {
                            if($row["No. of Books"]>0){
                                echo "<tr>
                                        <td>".$row["title"]."</td>
                                        <td>".$row["author"]."</td>
                                        <td>".$row["isbn"]."</td>
                                        <td>".$row["genre_name"]."</td>
                                    </tr>";
                            // You can display more book details here
                            }
                        }
                        echo '</tbody></table>';
                    } else {
                        echo "No results found";
                    }
                } else {
                    http_response_code(500);
                    echo "Error: Unable to execute statement";
                }
            
                // Close the statement
                $stmt->close();
            }
            
        ?>
        <h2>Add new books</h2>
        <form id="libraryForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group row">
                <label for="bookId" class="col-sm-2 col-form-label">Book ID</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bookId" name="book_id" placeholder="Book id">
                </div>
            </div>
            <div class="form-group row">
                <label for="bookName" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bookName" name="title" placeholder="Book Name">
                </div>
            </div>
            <div class="form-group row">
                <label for="author" class="col-sm-2 col-form-label">Author</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="author" name="author" placeholder="Author">
                </div>
            </div>
            <div class="form-group row">
                <label for="isbn" class="col-sm-2 col-form-label">ISBN</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="isbn" name="isbn" placeholder="ISBN">
                </div>
            </div>
            <div class="form-group row">
                <label for="isbn" class="col-sm-2 col-form-label">Number of Book</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="number" name="number" placeholder="No.of books">
                </div>
            </div>
            <div class="form-group row">
                <label for="genreId" class="col-sm-2 col-form-label">Genre</label>
                <div class="col-sm-10">
                    <select class="form-control" id="genreId" name="genre_id">
                        <option value="1">1-Fiction</option>
                        <option value="2">2-Non-fiction</option>
                        <option value="3">3-Mystery</option>
                        <option value="4">4-History</option>
                        <option value="5">5-Thriller</option>
                        <option value="6">6-Novel</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" id="submitBtn" class="btn btn-primary" name="submit">Add Book</button>
                </div>
            </div>
            <hr>
            <br>
        </form>

            <!-- PHP Code to Handle Form Submission and Display Added Books -->
        <?php
        $employee = $_SESSION['name'];
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
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
            // Retrieve form data
            $book_id = $_POST['book_id'];
            $title = $_POST['title'];
            $author = $_POST['author'];
            $isbn = $_POST['isbn'];
            $genre_id = $_POST['genre_id'];
            $number = $_POST['number'];

            // SQL query to insert data into the BOOK table
            $sql = "INSERT INTO book (book_id, title, author, isbn, genre_id, `No. of Books`) VALUES ('$book_id', '$title', '$author', '$isbn', '$genre_id', '$number')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success' role='alert'>Book added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }

        // Query to retrieve added books with their genres
        $sql = "SELECT b.*, g.genre_name 
            FROM book AS b 
            JOIN genre AS g ON b.genre_id = g.genre_id";
    $result = $conn->query($sql);

    echo "<h2>Available Books</h2>";
    if ($result->num_rows > 0) {
        echo "<div class='container mt-3'>";
        echo "<table class='table table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th scope='col'>ID</th>";
        echo "<th scope='col'>Title</th>";
        echo "<th scope='col'>Author</th>";
        echo "<th scope='col'>ISBN</th>";
        echo "<th scope='col'>Genre</th>";
        echo "<th scope='col'>No. of Books</th>";
        echo "<th scope='col'>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while($row = $result->fetch_assoc()) {
            if($row["No. of Books"] > 0){
                echo "<tr>";
                echo "<td>" . $row["book_id"] . "</td>";
                echo "<td>" . $row["title"] . "</td>";
                echo "<td>" . $row["author"] . "</td>";
                echo "<td>" . $row["isbn"] . "</td>";
                echo "<td>" . $row["genre_name"] . "</td>";
                echo "<td>" . $row["No. of Books"] . "</td>";
                echo "<td>
                <form action='borrow.php' method='post'>
                    <input type='hidden' name='bookName' value='" . $row["title"] . "'>
                    <input type='hidden' name='author' value='" . $row["author"] . "'>
                    <input type='hidden' name='genreId' value='" . $row["genre_id"] . "'>
                    <input type='hidden' name='number' value='" . $row["No. of Books"] . "'>
                    <input type='hidden' name='returnDate' value='" . date('Y-m-d', strtotime('+7 day')) . "'>
                    <input type='hidden' name='employee' value='" . $employee . "'>
                    <input name='customer' placeholder='Enter Customer name' required>
                    <input type='submit' class='btn btn-primary' value='Borrow'>
                </form>
                </td>
                </tr>";
            }
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "<hr>";
        echo "<br>";
    } else {
        echo "<div class='alert alert-info' role='alert'>No books available now.</div>";
    }

    $sql = "SELECT b.*, g.genre_name 
        FROM borrow AS b 
        JOIN genre AS g ON b.genre_id = g.genre_id";
    $result = $conn->query($sql);

    echo "<h2>Borrowed Books</h2>";
    if ($result->num_rows > 0) {
        echo "<div class='container mt-3'>";
        echo "<table class='table table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th scope='col'>Name</th>";
        echo "<th scope='col'>Author</th>";
        echo "<th scope='col'>Genre</th>";
        echo "<th scope='col'>Borrowed By</th>";
        echo "<th scope='col'>Return Date</th>";
        echo "<th scope='col'>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            $returnDate = new DateTime($row["returnDate"]);
            $currentDate = new DateTime();
            if($currentDate > $returnDate) {
                $difference = $currentDate->diff($returnDate)->days; // Difference in days
                $fine = $difference * 10;
            } else {
                $fine = 0;
            }

            // Check if there are any existing fine records for this transaction
            $checkFineSql = "SELECT * FROM fine WHERE transactionId = '" . $row['IdNo'] . "'";
            $fineResult = $conn->query($checkFineSql);

            if ($fineResult->num_rows == 0) {
                // Insert a new fine record
                $insertFineSql = "INSERT INTO fine (transactionId, amount, last_date) 
                                VALUES ('" . $row['IdNo'] . "', $fine, '" . $row['returnDate'] . "')";
                if ($conn->query($insertFineSql) !== TRUE) {
                    echo "Error inserting fine record: " . $conn->error;
                }
            } else {
                // Update existing fine record
                $updateFineSql = "UPDATE fine SET amount = $fine WHERE transactionId = '" . $row['IdNo'] . "'";
                if ($conn->query($updateFineSql) !== TRUE) {
                    echo "Error updating fine record: " . $conn->error;
                }
            }

            // Output the table row
            echo "<tr>
                <td>" . $row["title"] . "</td>
                <td>" . $row["author"] . "</td>
                <td>" . $row["genre_name"] . "</td> 
                <td>" . $row["customer"] . "</td>
                <td>" . $row["returnDate"] . "</td>
                <td>
                <form action='return.php' method='post'>
                <input type='hidden' name='bookName' value='" . $row["title"] . "'>
                <input type='hidden' name='customer' value='" . $row["customer"] . "'>
                <input type='submit' class='btn btn-primary' value='Return'>
                </form>
                </td>
                </tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "<hr>";
        echo "<br>";
    } else {
        echo "<div class='alert alert-info' role='alert'>No books borrowed.</div>";
    }


    $sql = "SELECT * FROM borrow";
    $result = $conn->query($sql);

    echo "<h2>Fine Management</h2>";
    if ($result->num_rows > 0) {
        echo "<div class='container mt-3'>";
        echo "<table class='table table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th scope='col'>Book name</th>";
        echo "<th scope='col'>Borrowed By</th>";
        echo "<th scope='col'>Fine</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["title"] . "</td>";
            echo "<td>" . $row["customer"] . "</td>";
            // Calculate fine based on the return date
            $returnDate = new DateTime($row["returnDate"]);
            $currentDate = new DateTime();
            if($currentDate > $returnDate) {
                $difference = $currentDate->diff($returnDate)->days; // Difference in days
                $fine = $difference * 10;
            } else {
                $fine = 0;
            }
            echo "<td>" . $fine . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "<br/>";
    } else {
        echo "<div class='alert alert-info' role='alert'>No fines.</div>";
    }

    // Close connection
    $conn->close();
    ?>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    
</body>
</html>
<?php
} else {
  header("Location: index.php");
  exit();
}
?>
