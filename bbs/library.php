<?php

// htmlspecialcharsを省略
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// DB接続
function dbconnect() {
    $dns = 'mysql:dbname=min_bbs;host=localhost';
	$username = 'root';
	$password = 'root';
	$option = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dns, $username, $password, $option);

    return $pdo;
}

?>