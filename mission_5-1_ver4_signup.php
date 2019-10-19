<?php
    //セッションデータを初期化
    //セッションIDの新規発行、又は、既存のセッションを読み込む
    //$_SESSIONを読み込む
    // セッション開始
    session_start();
    
   //DB接続
	$dsn = 'mysql:dbname=******db;host=localhost';
	$user = '********';
	$password = '*********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	
	if (!empty($_POST["username"]) and !empty($_POST["password"]) and !empty($_POST["password2"]) and $_POST["password"] == $_POST["password2"]){
        $password = $_POST["password"];
        if(strlen($password) < 6){
            echo "6文字以内です。"."<br>";
            echo "パスワードは6文字以上の英数字にしてください。";
            echo '<hr>';
        }else if(strlen($password) >= 6){
            if(preg_match("/^[a-zA-Z0-9]+$/", $password)){

	    $sql = 'SELECT * FROM 51ver4signup';
		$stmt = $pdo->query($sql); 
		$results = $stmt->fetchAll(); 
		//テーブルが空の時はそのまま新規登録
		if($results == FALSE){		     
	    // 入力したユーザ名とパスワードを格納 //パスワードの暗号化
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $sql = 'INSERT INTO 51ver4signup(username, password) VALUES (:username, :password)'; 
		$sql = $pdo -> prepare($sql); 
		$sql -> bindParam(':username', $username, PDO::PARAM_STR); 
		$sql -> bindParam(':password', $password, PDO::PARAM_STR);
		$sql -> execute(); 		 
		echo "登録しました"."<br>";
		echo "あなたのユーザー名は".$username."です"."<br>";
		echo "パスワードは";
		echo $_POST["password"];
		echo"です"."<br>";
		echo "大切に保管してください"."<br>";
		echo '<hr>';
		//しかし一度に複数同時にテーブルに書き込まれてしまう（テーブルに登録されている名前の数だけ）
		//多分論理値の使い方が間違っている気がする
		
		//テーブルが空ではない時
	        }else if($results == TRUE){		  
	        //forreachはあくまで名前の確認用なので、確認用の変数を一つ用意して、名前が重複していたらtrue、していなかったらfalseとする。その変数がfalse(名前が重複していなかったら)なら書き込みをする
	        $exist_flag=false;	            
	            
		     foreach ($results as $row){
		        //配列の名前に同じものがある時は警告を出す
		        if($_POST["username"] == $row['username']){
		                $exist_flag=true;
		            echo "既に使われたユーザー名です";
		            echo '<hr>';
                }
            }
		//配列の名前に同じものがない時は新規登録　          
		if($exist_flag == false){         
	    // 入力したユーザ名とパスワードを格納 //パスワードの暗号化
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $sql = 'INSERT INTO 51ver4signup(username, password) VALUES (:username, :password)'; 
		$sql = $pdo -> prepare($sql); 
		$sql -> bindParam(':username', $username, PDO::PARAM_STR); 
		$sql -> bindParam(':password', $password, PDO::PARAM_STR);
		$sql -> execute(); 		 
		echo "登録しました"."<br>";
		echo "あなたのユーザー名は".$username."です"."<br>";
		echo "パスワードは";
		echo $_POST["password"];
		echo"です"."<br>";
		echo "大切に保管してください"."<br>";
		echo '<hr>';
	        }
	     }
	  }
	 }
	}

	 	    
	 if(!empty($_POST["username"]) and !empty($_POST["password"]) and !empty($_POST["password2"]) and $_POST["password"] != $_POST["password2"]) {
	    echo "確認用パスワードが一致しません";
        echo '<hr>';
     }
 ?>


<html>
    <head>
            <meta charset="UTF-8">
            <title>新規登録</title>
    </head>
    <body>
        <h1>新規登録画面</h1>
        <form action="mission_5-1_ver4_signup.php" method="POST">
                <legend>新規登録</legend>
                <input type="text" name="username" placeholder= ユーザー名 required size="32" ><br>
                <input type="password" name="password" placeholder= パスワード(6文字以上英数字) required size="32"  maxlength="32"><br>
                <input type="password" name="password2" placeholder=  パスワード(確認用) required size="32"  maxlength="32"><br>
                <input type="submit" value="新規登録">
        </form>
        <!--スレッド削除-->		
		<form method="POST" action="mission_5-1_ver4_signup.php">
			<input type="password" name="password3" placeholder = "パスワード" required size="32" >
			<input type="submit"  value="スレッド削除" /><br>
		</form>	
<?php
	//スレッド削除
	if (isset($_POST["password3"])){
	    if($_POST["password3"] == "4141"){
		$sql ='DROP TABLE IF EXISTS 51ver4signup';
		$pdo->query($sql);
		echo "スレッドを削除しました";
		}else echo "パスワードが違います";
	 }
?>
        <hr>
        <a href="mission_5-1_ver4_main.php" target="_self">掲示板に戻る</a><br>
        <a href="mission_5-1_ver4_login.php" target="_self"> ログイン</a><br>
        <hr>
    </body>
</html>

<?php
    //テーブル作成  //スレッド削除処理よりも下に配置しなければいけない
	$sql = "CREATE TABLE IF NOT EXISTS 51ver4signup"  //テーブル名には"-"は使えない
	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."username TEXT,"
	."password TEXT"
	.");";
	$pdo->query($sql);
	
    if(isset($pdo)){
		$sql = 'SELECT * FROM 51ver4signup';
		$stmt = $pdo->query($sql); 
		$results = $stmt->fetchAll(); 
		foreach ($results as $row){
			echo "{$row['id']}. ユーザー名:{$row['username']} パスワード:{$row['password']}"."<br>";
	 		$pdo=null; 
		}
	}
?>