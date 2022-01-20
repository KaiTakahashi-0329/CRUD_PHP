<?php

session_start();
require('library.php');

if(isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit();
}

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
var_dump($post_id);
if(!$post_id) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = dbconnect();
    $stmt = $pdo ->prepare('DELETE FROM posts where id = :id and member_id = :member_id limit 1');
    $stmt->bindValue('id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue('member_id', $id, PDO::PARAM_INT);
    $stmt->execute();

} catch(PDOException $e) {
    $e->getMessage();
    die($e->getMessage());
}

header('Location: index.php');
exit();

?>