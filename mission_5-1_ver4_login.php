<?php
    session_start();
    
    //DB接続
	$dsn = 'mysql:dbname=******db;host=localhost';
	$user = '********';
	$password = '*********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    // ログインボタンが押された場合
    if (isset($_POST["login"])) {
    //ユーザ名とパスワードの入力チェック
         if (isset($_POST["username"]) and isset($_POST["password"])) {
            // 入力したユーザ名とパスワードを格納
            $username = $_POST["username"];
            $password = $_POST["password"];
            //テーブルに保存されたユーザー名とパスワードが一致するかどうか確認
            $sql = 'SELECT * FROM 51ver4signup';
		    $stmt = $pdo->query($sql); 
		    $results = $stmt->fetchAll(); 
		    
		    $check_flag=false;	     
		    
		    foreach ($results as $row){
		        //ハッシュ化したパスワードと入力されたパスワードの一致を確認
		        if($username == $row['username'] and password_verify($password, $row['password'])){		            
		                $check_flag=true;       
                        session_regenerate_id(true);
                        $_SESSION["NAME"] = $row['username'];
                        header("Location: mission_5-1_ver4_main.php");  // メイン画面へ遷移
                        exit();   // 処理終了                
                    }
            }//foreachをくくる      
                        if($check_flag==false){
                        echo  "ユーザーIDあるいはパスワードに誤りがあります。";
                }
            }
        }
    
    

                         
?>


<html>
    <head>
            <meta charset="UTF-8">
            <title>ログイン</title>
    </head>
    <body>
        <h1>ログイン画面</h1>
        <form action="" method="POST">
                <legend>ログイン</legend>
                <input type="text" name="username"  placeholder= ユーザー名 required><br>
                <input type="password" name="password"  placeholder= パスワード required>
                <input type="submit"  name= "login" value="ログイン">
        </form>
        <hr>
        <a href="mission_5-1_ver4_main.php" target="_self">掲示板に戻る</a>
        <a href="mission_5-1_ver4_signup.php" target="_self">新規登録の方はこちら</a>
    </body>
</html>