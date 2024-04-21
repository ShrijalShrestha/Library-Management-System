<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <link href="style.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form action="login.php" method="post">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" id="email" name="email" placeholder="Username" required/>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required/>
                <i class='bx bxs-lock'></i>
            </div>
            <?php if(isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
                <br/>
            <?php } ?>
            <button class="btn">Login</button>
        </form>
    </div>
</body>
</html>

