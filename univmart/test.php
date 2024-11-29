<?php
include('conn.php');
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = trim($_POST['post_text']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stocks']);
    $picture = null;

    // Sanitize input
    $description = htmlspecialchars($description);

    // Handle file upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['post_image']['tmp_name'];
        $file_name = $_FILES['post_image']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($file_ext), $allowed_extensions)) {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            exit;
        }

        // Generate unique file path
        $picture = 'uploads/' . uniqid() . '.' . $file_ext;

        // Create uploads directory if it doesn't exist
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $picture)) {
            echo "Failed to upload file.";
            exit;
        }
    }

    // Get user's profile ID
    $sql = "SELECT id_profile FROM profile WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id_profile);
    $stmt->fetch();
    $stmt->close();

    if ($id_profile) {
        // Insert post into the database
        $sql = "INSERT INTO create_post (id_profile, description, picture, price, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdi", $id_profile, $description, $picture, $price, $stock);

        if ($stmt->execute()) {
            // Redirect back to the newsfeed with success
            $_SESSION['post_success'] = "Post created successfully!";
            header("Location: newsfeed.php");
            exit;
        } else {
            echo "Error creating post: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "User profile not found.";
    }
}

// Fetch posts to display
$sql = "SELECT p.description, p.picture, p.price, p.stock, pr.username 
        FROM create_post p 
        INNER JOIN profile pr ON p.id_profile = pr.id_profile 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsfeed</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
        }

        .header {
            position: fixed;
            top: 0;
            left: 200px;
            right: 0;
            background-color: black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .header .brand {
            font-size: 24px;
            font-weight: bold;
            color: #ff6600;
        }

        .header .icon-container {
            display: flex;
        }

        .header .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            margin-left: 20px;
            color: white;
            font-size: 14px;
        }

        .header .icon-item img {
            width: 40px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .header .icon-item img:hover {
            transform: scale(1.2);
            filter: brightness(1.3);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 200px;
            background-color: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 100px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
            font-size: 16px;
            text-decoration: none;
            margin-bottom: 80px;
        }

        .sidebar img {
            width: 60px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .sidebar img:hover {
            transform: scale(1.2);
            filter: brightness(1.3);
        }

        .sidebar .icon-item span {
            color: white;
            font-size: 14px;
        }

        .main-content {
            margin-left: 200px;
            padding: 80px 20px;
            width: calc(100% - 200px);
        }

        .post, .post-form {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
        }

        .post img {
            width: 100%;
            border-radius: 5px;
            margin: 15px 0;
        }

        .post-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 1000px;
            margin-left: auto;  
            margin-right: auto; 
        }

        .post-form .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .post-form .profile-pic {
            width: 50px; 
            height: 50px; 
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        .post-form .username {
            font-weight: bold;
            font-size: 16px;
        }

        .post-form textarea {
            width: 50%;
            height: 80px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .post-form .post-input {
            width: 20%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .post-form .upload-label {
            display: block;
            font-size: 14px;
            color: #555;
            cursor: pointer;
            padding: 5px;
            text-align: center;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-bottom: 15px;
            width: 10%
        }

        .post-form .upload-label:hover {
            background-color: #e0e0e0;
        }

        .post-form .upload-input {
            display: none;
        }

        .post-form button {
            width: 20%;
            padding: 12px;
            font-size: 16px;
            background-color: #ff6600;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .post-form button:hover {
            background-color: #0056b3;
        }

        .post-container {
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .post-container .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .post-container .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        .post-container .post-content {
            margin-top: 15px;
        }

        .post-container button {
            margin-top: 15px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
        }

        .post-container button:hover {
            background-color: #ff6600;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="icon-item">
            <img src="static/profile.png" alt="Profile" title="Profile">
            <span>Profile</span>
        </a>
        <a href="#" class="icon-item">
            <img src="static/notification.png" alt="Notifications" title="Notifications">
            <span>Notifications</span>
        </a>
        <a href="#" class="icon-item">
            <img src="static/messages.png" alt="Messages" title="Messages">
            <span>Messages</span>
        </a>
        <a href="home.php" class="icon-item">
            <img src="static/logout.png" alt="Logout" title="Logout">
            <span>Logout</span>
        </a>
    </div>

    <!-- Header with Icons -->
    <div class="header">
        <div class="brand">UNIVMART</div>
        <div class="icon-container">
            <a href="#" class="icon-item">
                <img src="static/home.png" alt="Home" title="Home">
                <span>Home</span>
            </a>
            <a href="#" class="icon-item">
                <img src="static/search.png" alt="Search" title="Search">
                <span>Search</span>
            </a>
            <a href="#" class="icon-item">
                <img src="static/cart.png" alt="Cart" title="Cart">
                <span>Cart</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="post-form">
            <div class="user-info">
                <img src="https://via.placeholder.com/50" alt="User" class="profile-pic">
                <span class="username"><?php echo htmlspecialchars($username); ?></span>
            </div>
            <form action="newsfeed.php" method="post" enctype="multipart/form-data">
                <textarea name="post_text" placeholder="Write something..." required></textarea>
                <label class="upload-label">
                    Upload
                    <input type="file" name="post_image" class="upload-input" accept="image/*" required>
                </label>
                <input type="number" name="price" class="post-input" placeholder="Price" required>
                <input type="number" name="stocks" class="post-input" placeholder="Stock" required>
                <button type="submit">Post</button>
            </form>
        </div>

        <!-- Display Posts -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post-container">
                    <div class="user-info">
                        <img src="https://via.placeholder.com/50" alt="User" class="profile-pic">
                        <span><?php echo htmlspecialchars($row['username']); ?></span>
                    </div>
                    <div class="post-content">
                        <?php if (!empty($row['picture'])): ?>
                            <img src="<?php echo htmlspecialchars($row['picture']); ?>" alt="Post Image">
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>Price:</strong> $<?php echo htmlspecialchars($row['price']); ?></p>
                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?></p>
                        <button>Add to Cart</button>
                        <button>Message Seller</button>
                        <button>Purchase</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts yet!</p>
        <?php endif; ?>
    </div>

</body>
</html>
