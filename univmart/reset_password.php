<?php
include 'conn.php';

session_start();

$email_address = $new_password = $confirm_password = "";
$messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_address = trim($_POST['email_address']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the passwords match
    if ($new_password !== $confirm_password) {
        $messages[] = "Passwords do not match. Please try again.";
    } else {
        // Check if the email exists in the database
        $sql = "SELECT id_profile FROM profile WHERE email_address = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email_address);  // Use $email_address here
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If email exists, hash the new password and update the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $sql_update = "UPDATE profile SET password = ? WHERE email_address = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ss", $hashed_password, $email_address); // Use $email_address here

            if ($stmt_update->execute()) {
                $messages[] = "Your password has been reset successfully.";
                header("Location: home.php");
                exit;
            } else {
                $messages[] = "There was an error updating your password. Please try again later.";
            }

            // Close both statements
            $stmt_update->close();
        } else {
            $messages[] = "No account found with that email address.";
        }

        // Close the first statement
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UNIVMART</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
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
        input[type="email"], input[type="password"] {
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
            background-color: #191970;
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
            <h1>"Reset Your Password"</h1>
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

            <form method="POST" action="reset_password.php">
                <input type="email" name="email_address" placeholder="Email Address" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
            </form>

            <div class="signin">
                <p>Remembered your password? <a href="home.php">Sign In</a></p>
            </div>
        </div>
    </div>
</body>
</html>
