<?php 
session_start();
require('library.php');

$error = [];
$email = '';
$password = '';


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if($email === '' || $password === '') {
        $error['login'] = 'blank';
    } else {
        // ログインチェック
        try {

            $pdo = dbconnect();

            $stmt = $pdo ->prepare('SELECT id, name, password FROM members WHERE email = :email LIMIT 1');
            $stmt->bindValue('email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if(password_verify($password, $result['password'])) {
                session_regenerate_id();
                $_SESSION['id'] = $result['id'];
                $_SESSION['name'] = $result['name'];
                header('Location: index.php');
                exit();

            } else {
                $error['login'] = 'failed';
            }

        } catch(PDOException $e) {
            $e->getMessage();
            die($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <title>ログインする</title>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>ログインする</h1>
    </div>
    <div id="content">
        <div id="lead">
            <p>メールアドレスとパスワードを記入してログインしてください。</p>
            <p>入会手続きがまだの方はこちらからどうぞ。</p>
            <p>&raquo;<a href="join/">入会手続きをする</a></p>
        </div>
        <form action="" method="post">
            <dl>
                <dt>メールアドレス</dt>
                <dd>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($email); ?>"/>
                    <?php if(isset($error['login']) && $error['login'] === 'blank'): ?>
                        <p class="error">* メールアドレスとパスワードをご記入ください</p>
                    <?php endif; ?>
                    <?php if(isset($error['login']) && $error['login'] === 'failed'): ?>
                        <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    <input type="password" name="password" size="35" maxlength="255" value="<?php echo h($password); ?>"/>
                </dd>
            </dl>
            <div>
                <input type="submit" value="ログインする"/>
            </div>
        </form>
    </div>
</div>
</body>
</html>
