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

try {
    $pdo = dbconnect();
} catch(PDOException $e) {
    $e->getMessage();
    die($e->getMessage());
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    try {
        $stmt = $pdo ->prepare('INSERT INTO posts (message, member_id) values (:message, :member_id)');
        $stmt->bindValue('message', $message, PDO::PARAM_STR);
        $stmt->bindValue('member_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        header('Location: index.php');
        exit();

    } catch(PDOException $e) {
        $e->getMessage();
        die($e->getMessage());
    }
}

try {

    $stmt = $pdo->prepare('SELECT p.id, p.member_id, p.message, p.created, m.name, m.picture FROM posts p, members m WHERE p.member_id = m.id ORDER BY id desc');
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $e->getMessage();
    die($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css"/>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">
        <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
        <form action="" method="post">
            <dl>
                <dt><?php echo h($name); ?>さん、メッセージをどうぞ</dt>
                <dd>
                    <textarea name="message" cols="50" rows="5"></textarea>
                </dd>
            </dl>
            <div>
                <p>
                    <input type="submit" value="投稿する"/>
                </p>
            </div>
        </form>

        <?php foreach($result as $item): ?>
            <div class="msg">
                <img src="member_picture/<?php echo h($item['picture']); ?>" width="48" height="48" alt=""/>
                <p><?php echo h($item['message']); ?> <span class="name">（<?php echo h($item['name']); ?>）</span></p>
                <p class="day"><a href="view.php?id=<?php echo h($item['id']); ?>"><?php echo h($item['created']); ?></a>
                <?php if($_SESSION['id'] === $item['member_id']): ?>
                    [<a href="delete.php?id=<?php echo h($item['id']); ?>" style="color: #F33;">削除</a>]
                <?php endif; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>

</html>