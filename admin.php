<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page | Alifu</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="admin.php?page=apartments">Apartments</a></li>
                <li><a href="admin.php?page=reservations">Reservations</a></li>
                <li><a href="admin.php?page=spa_procedures">Spa Procedures</a></li>
                <li><a href="admin.php?page=users">Users</a></li>
                <li><a href="admin.php?page=user_spa_reservations">User Spa Reservations</a></li>
                <li><a href="/backend/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <h1>Admin Page</h1>
    <main>
        <div class="container">
            <?php
            // Include the appropriate table functionality based on the selected page
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                
                // Map the page value to the corresponding PHP file
                $pageMappings = [
                    'apartments' => 'apartments.php',
                    'reservations' => 'reservations.php',
                    'spa_procedures' => 'spa_procedures.php',
                    'users' => 'users.php',
                    'user_spa_reservations' => 'user_spa_reservations.php',
                ];
                
                // Check if the mapped PHP file exists and include it
                if (isset($pageMappings[$page]) && file_exists($pageMappings[$page])) {
                    include $pageMappings[$page];
                } else {
                    echo 'Invalid page.';
                }
            }
            ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
</body>
</html>
