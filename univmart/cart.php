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
            background-color: #000000;
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
            <a href="cart.php" class="icon-item">
                <img src="static/cart.png" alt="Cart" title="Cart">
                <span>Cart</span>
            </a>
        </div>
    </div>

</body>
</html>
