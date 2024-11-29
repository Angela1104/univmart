<?php
include 'conn.php';

session_start();

$messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM profile WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['id_profile'] = $user['id_profile'];
            $_SESSION['username'] = $user['username'];
            header("Location: newsfeed.php");
            exit;
        } else {
            $messages[] = "Incorrect password.";
        }
    } else {
        $messages[] = "No user found with that username.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNIVMART</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #000000;
        }
        .container {
            display: flex;
            width: 1000px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .left-section {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .logo {
            width: 500px;
            height: 500px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .left-section h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 25px;
            color: #191970;
            margin-top: 20px;
            font-weight: normal;
        }
        .right-section {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            background-color: #ff6600;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .signup {
            margin-top: 10px;
        }
        .signup a {
            color: #191970;
            text-decoration: none;
        }
        .signup a:hover {
            text-decoration: underline;
        }
        .forgot-password {
            margin-top: 10px;
        }
        .forgot-password a {
            color: #191970;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .messages ul {
            list-style-type: none;
            padding: 0;
        }
        .messages li {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin: 5px 0;
        }
        .messages.success li {
            background-color: #d4edda;
            color: #155724;
        }
        .messages.error li {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <img src="static/logo.jpg" alt="UNIVMART Logo">
            </div>
            <h1>"Where Students Trade with Ease"</h1>
        </div>

        <div class="right-section">
            <?php if (!empty($messages)): ?>
                <div class="messages">
                    <ul>
                        <?php foreach ($messages as $message): ?>
                            <li><?php echo htmlspecialchars($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="home.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div class="forgot-password">
                <p><a href="reset_password.php">Forgot Password?</a></p>
            </div>            
            <div class="signup">
                <p>New account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>
</body>
</html>
