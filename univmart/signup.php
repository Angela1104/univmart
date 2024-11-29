<?php
include 'conn.php';

session_start();

$messages = [];
$first_name = $last_name = $username = $email_address = $birthdate = $password = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email_address = trim($_POST['email_address']);
    $birthdate = $_POST['birthdate'];
    $password = $_POST['password'];

    $sql = "SELECT id_profile FROM profile WHERE username = ? OR email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $messages[] = "Username or email already taken. Please try another one.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO profile (first_name, last_name, username, email_address, birthdate, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $username, $email_address, $birthdate, $hashed_password);

        if ($stmt->execute()) {
            header("Location: home.php");
            exit;
        } else {
            $messages[] = "There was an error creating your account. Please try again.";
        }
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
    <title>Sign Up - UNIVMART</title>
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
        input[type="text"], input[type="password"], input[type="email"], input[type="date"] {
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
        .signin {
            margin-top: 10px;
        }
        .signin a {
            color: #191970;
            text-decoration: none;
        }
        .signin a:hover {
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

            <form method="POST" action="signup.php">
                <input type="text" name="first_name" placeholder="First Name" value="<?php echo $first_name; ?>" required>
                <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $last_name; ?>" required>
                <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
                <input type="email" name="email_address" placeholder="Email Address" value="<?php echo $email_address; ?>" required>
                <input type="date" name="birthdate" placeholder="Birthdate" value="<?php echo $birthdate; ?>" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit">Create Account</button>
            </form>

            <div class="signin">
                <p>Already have an account? <a href="home.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
