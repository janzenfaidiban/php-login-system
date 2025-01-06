<?php
require_once 'auth.php';
checkAuth();

// Your protected page content here
echo "Welcome, " . htmlspecialchars($_SESSION['username']); 
echo '<br><a href="logout.php">Logout</a>'; 