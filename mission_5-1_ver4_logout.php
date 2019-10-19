<?php
    session_start();
    if (isset($_SESSION["NAME"])) {
        $errorMessage = "ログアウトしました。";
    } else {
        $errorMessage = "セッションがタイムアウトしました。";
    }
    // セッションの変数のクリア
    $_SESSION = array();

    // セッションクリア
    session_destroy();
    ?>

<html>
	<hr>
    <a href="mission_5-1_ver4_main.php" target="_self">掲示板に戻る</a>
    <a href="mission_5-1_ver4_signup.php" target="_self"> 新規登録の方はこちら</a>
	<a href="mission_5-1_ver4_login.php" target="_self"> ログイン</a><br>
</html>