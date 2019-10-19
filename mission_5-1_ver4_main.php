<?php
	//ユーザー登録・ログイン機能実装を目指す	
	//セッションデータを初期化
    //セッションIDの新規発行、又は、既存のセッションを読み込む
    //$_SESSIONを読み込む
    session_start();
   
   // ログイン状態チェック
	if (!isset($_SESSION["NAME"])){
    	header("Location: mission_5-1_ver4_logout.php");
    	exit;
	}

	//DB接続
	$dsn = 'mysql:dbname=******db;host=localhost';
	$user = '********';
	$password = '*********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		
	//データベースに投稿を保存(コメント)
	if(isset($_POST["comment"]) and isset($_POST["password1"]) and empty($_POST["edit1"])){
		if(empty($_POST["name1"])){ 
		//空白文字（スペースなど）は文字有りとして認識される
			$name = "名無しさん";
		}else $name = htmlspecialchars($_POST["name1"]);
		$comment = htmlspecialchars($_POST["comment"]);
		$date=date("Y/m/d H:i:s");
		$password = $_POST["password1"];
		$sql = 'INSERT INTO 51ver4(name, comment, date, password) VALUES (:name, :comment, :date, :password)'; 
		$sql = $pdo -> prepare($sql); 
		$sql -> bindParam(':name', $name, PDO::PARAM_STR); 
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':date', $date, PDO::PARAM_STR);
		$sql -> bindParam(':password', $password, PDO::PARAM_STR);
		$sql -> execute(); 
	}
	
	//画像保存用フォルダの作成
	if(!file_exists('51ver4img')) mkdir('51ver4img');
	
	//データベースに投稿を保存(画像)
	if(isset($_FILES["image"]) and isset($_POST["password2"]) and empty($_POST["edit2"])){ 
		if(file_exists('51ver4img')){
			if(empty($_POST["name2"])){ 
				$name = "名無しさん";
			}else $name = htmlspecialchars($_POST["name2"]);
			$date=date("Y/m/d H:i:s");
			$image_path='51ver4img/'.date("YmdHis").".jpg";
			move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
			$password = $_POST["password2"];
			$sql = 'INSERT INTO 51ver4(name, img, date, password) VALUES (:name, :img, :date, :password)'; 
			$sql = $pdo -> prepare($sql); 
			$sql -> bindParam(':name', $name, PDO::PARAM_STR); 
			$sql -> bindParam(':img', $image_path, PDO::PARAM_STR);
			$sql -> bindParam(':date', $date, PDO::PARAM_STR);
			$sql -> bindParam(':password', $password, PDO::PARAM_STR);
			$sql -> execute(); 
		}
	}

	//データベースから投稿を削除
	if (isset($_POST["delete"]) and isset($_POST["password3"])){
		$id = $_POST["delete"];
		$password = $_POST["password3"];
		$sql = 'SELECT * FROM 51ver4';
		$stmt = $pdo->query($sql); 
		$results = $stmt->fetchAll();
		foreach($results as $row){
			if($id == $row['id']){
				if($password == $row['password']){
					$sql = 'delete from 51ver4 where id=:id';
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id, PDO::PARAM_INT);
					$stmt->execute();
				}
			}	
		}
	}
			
	//編集番号選択＆投稿フォームに移す　
	if (isset($_POST["editnum"]) and isset($_POST["password4"])){
		$id = $_POST["editnum"];
		$password = $_POST["password4"];
		$sql = 'SELECT * FROM 51ver4';
		$stmt = $pdo->query($sql); 
		$results = $stmt->fetchAll();
		foreach($results as $row){
			if($id == $row['id']){
				if($password == $row['password']){
					if(!isset($row['img'])){
						$iddata1 = $row['id'];
						$namedata1 = $row['name'];
						$commentdata = $row['comment'];
						$passdata1 = $row['password'];
					}else if(isset($row['img'])){
						$iddata2 = $row['id'];
						$namedata2 = $row['name'];
						$passdata2 = $row['password'];					
					}
				}
			}
		}
	}
	
	//投稿を編集(コメント)
	if (isset($_POST["edit1"])){
		$id = $_POST["edit1"]; 
		$name = $_POST["name1"];
		$comment = $_POST["comment"]; 
		$sql = 'update 51ver4 set name=:name,comment=:comment where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute(); 
	}	
	
	//投稿を編集(画像)
	if (isset($_POST["edit2"])){
		$id = $_POST["edit2"]; 
		$name = $_POST["name2"];
		$sql = 'update 51ver4 set name=:name where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute(); 
	}	

	?>

	<!DOCTYPE html>
	<html lang="ja">
	<head>
	<meta charset="utf-8">
	</head>
	<body>
					
		<a href="mission_5-1_ver4_signup.php" target="_self">新規登録</a> 
		<a href="mission_5-1_ver4_login.php" target="_self">ログイン</a> 
		<a href="mission_5-1_ver4_logout.php" target="_self">ログアウト</a><br>
			</p>
	<hr>	
	
		<!--名前＆コメント　formタグを分けることで、requiredの誤作動を防げる-->
		<form method="POST" action="mission_5-1_ver4_main.php">
			<input type="text" name="name1" placeholder = "名前" value =<?php if(isset($namedata1)){echo $namedata1;}?>><br>	
			<!--textareaでvalueを使うと入力フォームに投稿を表示できない（inputはできた）-->
			<textarea name="comment" placeholder = "コメント" rows="2" required><?php if(isset($commentdata)){echo $commentdata;}?></textarea><br>
			<input type="password" name="password1" placeholder = "パスワード" required value=<?php if(isset($passdata1)){echo $passdata1;}?>>
			<input type="hidden"  name="edit1"   value =<?php if(isset($iddata1)){echo $iddata1;}?>> 
			<input type="submit" value="送信"><br><br>
		</form>
		
		<!--画像アップロード-->
			<form method="POST" action="mission_5-1_ver4_main.php" enctype="multipart/form-data">
			<input type="text" name="name2" placeholder = "名前" value =<?php if(isset($namedata2)){echo $namedata2 ;}?>><br>
			画像ファイルの添付:<input type="file" name="image"><br>
			<input type="password" name="password2" placeholder = "パスワード" required value=<?php if(isset($passdata2)){echo $passdata2;}?>>
			<input type="hidden"  name="edit2"   value =<?php if(isset($iddata2)){echo $iddata2;}?>> 
			<input type="submit" value="アップロード"><br><br>
		</form>
		
		<!--削除-->
		<form method="POST" action="mission_5-1_ver4_main.php">
			<input type="text"  name="delete" placeholder = "削除対象番号" required/><br>
			<input type="password" name="password3" placeholder = "パスワード" required>
			<input type="submit"  name="deletoNo" value="削除" /><br><br>
		</form>
		
		<!--編集-->
		<form method="POST" action="mission_5-1_ver4_main.php">
			<input type="text"  name="editnum"   placeholder = "編集対象番号" required /><br>
			<input type="password" name="password4" placeholder = "パスワード" required>
			<input type="submit"  value="編集" /><br><br>
		</form>
		
		<!--スレッド削除-->		
		<form method="POST" action="mission_5-1_ver4_main.php">
			<input type="password" name="password5" placeholder = "パスワード" required>
			<input type="submit"  value="スレッド削除" /><br>
		</form>	
		
	<?php
	//スレッド削除
	if (isset($_POST["password5"])){
	    if($_POST["password5"] == "4141"){
		$sql ='DROP TABLE IF EXISTS 51ver4';
		$pdo->query($sql);
		echo "スレッドを削除しました";
		}else echo "パスワードが違います";
	 }
	?>
	
	<hr>
	</body>
	</html>
<?php	
	//テーブル作成  //スレッド削除処理よりも下に配置しなければいけない
	$sql = "CREATE TABLE IF NOT EXISTS 51ver4" //テーブル名には"-"は使えない
	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name TEXT,"
	."comment TEXT,"
	."img TEXT," 
	."date TEXT,"
	."password TEXT"
	.");";
	$pdo->query($sql);

	//ブラウザに表示　
	if(isset($pdo)){
		$sql = 'SELECT * FROM 51ver4';
		$stmt = $pdo->query($sql); 
		$results = $stmt->fetchAll(); 
		foreach ($results as $row){
			if(isset($row['img'])){
				echo "{$row['id']}. {$row['name']} {$row['date']}"."<br>";
				echo  "<img src= {$row['img']} width= 256 height= 256>"."<br>";
			}else
			echo "{$row['id']}. {$row['name']} {$row['date']}"."<br>"."{$row['comment']}"."<br>";
	 		$pdo=null; 
		}
	}
?>