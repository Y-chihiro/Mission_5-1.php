<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>
    <body>

<?php

//データベース接続設定
$dsn='データベース名';//データベース名
//$dsnの式の中にスペースはNG!
$user='ユーザー名';//ユーザー名
$password='パスワード';//パスワード
$pdo=new PDO($dsn,$user,$password,
    array( PDO::ATTR_ERRMODE=> PDO::ERRMODE_WARNING));
//データベース操作でエラーが発生したときに警告が表示される
//デフォルトだとエラーが発生しても、何も表示されない

//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS tbtest_51"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "ts TEXT,"
	. "password TEXT"
	.");";
	$stmt = $pdo->query($sql);
	
//変数の定義
$editnum="";
$editname="";
$editcomment="";
$edit="";

//削除機能	
if(!empty($_POST["deleteNo"]) && !empty($_POST["delpassword"])){
//削除番号とパスワードが入力されたら
    $delete=$_POST["deleteNo"];
    $delpass=$_POST["delpassword"];
    $sql="SELECT * FROM tbtest_51";
    //テーブルを抽出
    $results=$pdo->query($sql);
    foreach($results as $row){
        $delID=$row["id"];
        $delPASS=$row["password"];
        if($delID==$delete && $delPASS==$delpass){
        //削除番号とパスワードが一致したら
            $deleteNo="";
            $sql="DELETE FROM tbtest_51 WHERE id=:id"; 
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);                          
            $stmt->execute();
        }
       
    }
}
elseif(!empty($_POST["deleteNo"]) && empty($_POST["delpassword"])){
//削除番号は入力されたが、パスワードが入力されなかったとき
    echo "パスワードを入力してください";
}
elseif(empty($_POST["deleteNo"]) && !empty($_POST["delpassword"])){
//削除番号が入力されなかったとき
    echo "削除したい投稿番号を入力してください";
}


//編集選択    
if(isset($_POST["editNo"]) && isset($_POST["editpassword"])){
//編集番号とパスワードが入力されたとき
    $edit=$_POST["editNo"];
    $editpass=$_POST["editpassword"];
    $editnum="";
    $editname="";
    $editcomment="";
    $sql="SELECT * FROM tbtest_51";
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
        $editID=$row["id"];
        $editNAME=$row["name"];
        $editCOMMENT=$row["comment"];
        $editPASS=$row["password"];
        if($editID==$edit && $editPASS==$editpass){
        //編集番号とパスワードが一致したら
            $editnum=$editID;
            $editname=$editNAME;
            $editcomment=$editCOMMENT;
        }
    }
}
elseif(!empty($_POST["editNo"]) && empty($_POST["editpassword"])){
    echo "パスワードを入力してください";
}
elseif(empty($_POST["editNo"]) && !empty($_POST["editpassword"])){
    echo "編集したい投稿番号を入力してください";
}
//編集機能
if(!empty($_POST["blank"]) && !empty($_POST["name"]) &&
!empty($_POST["comment"]) && !empty($_POST["password"])){
    $hiddenNo=$_POST["blank"];
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $ts=date("Y/m/d H:i:s");
    $pass=$_POST["password"];
    $sql='UPDATE tbtest_51 SET name=:name,comment=:comment,
    ts=:ts,password=:password WHERE id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':name',$name,PDO::PARAM_STR);
	$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
	$stmt->bindParam(':ts',$ts,PDO::PARAM_STR);
	$stmt->bindParam(':password',$pass,PDO::PARAM_STR);
	$stmt->bindParam(':id',$hiddenNo,PDO::PARAM_INT);
	$stmt->execute();
}

//新規投稿
if(!empty($_POST["name"]) && !empty($_POST["comment"]) && 
!empty($_POST["password"]) && empty($_POST["blank"])) {
//名前とコメントが空でなく、編集番号表示スペースが空のとき
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $ts=date("Y/m/d H:i:s");
    $pass=$_POST["password"];
    $sql=$pdo->prepare("INSERT INTO tbtest_51 (name,comment,ts,password)          
        VALUES('$name','$comment','$ts','$pass')");
    $sql->execute();
}
elseif(!empty($_POST["name"]) && !empty($_POST["comment"]) &&
empty($_POST["password"])){
    echo "パスワードを入力してください";
}
elseif(empty($_POST["name"]) && (!empty($_POST["comment"]) || 
isset($_POST["password"]))){
    echo "名前を入力してください";
}
elseif(empty($_POST["comment"]) && (!empty($_POST["name"]) && 
isset($_POST["password"]))){
    echo "コメントを入力してください";
}


?>

<h2>好きな色は？</h2>

<h3>入力・編集フォーム</h3>
    <form action="mission_5-1.php" method="POST">
        名前：<input type="text" name="name" placeholder="名前"
        value="<?php if($editname!=""){echo "$editname";} ?>"><br>
        コメント：<input type="text" name="comment" placeholder="好きな色"
        value="<?php if($editcomment!=""){echo "$editcomment";} ?>"><br>
        パスワード：<input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="send"><br>
        <input type="hidden" name="blank" 
        value="<?php if($edit!=""){echo "$edit";} ?>"><br><br>
    </form> 
    <h3>削除フォーム</h3> <!--フォーム(<form></form>)は分ける-->
    <form action="mission_5-1.php" method="POST">
        削除番号：<input type="num" name="deleteNo" placeholder="削除対象番号"><br>
        パスワード：<input type="text" name="delpassword" placeholder="パスワード">
        <input type="submit" name="delete" value="削除"><br><br>
    </form>
    <form action="mission_5-1.php" method="POST">
        編集番号：<input type="text" name="editNo" placeholder="編集対象番号"><br>
        パスワード：<input type="text" name="editpassword" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
    </form>
    <br>
    <h3>投稿一覧</h3>
    
<?php

//表示機能
$sql ="SELECT * FROM tbtest_51";
	$stmt=$pdo->query($sql);
	$results=$stmt->fetchAll();
	foreach($results as $row){
	//$rowの中にはテーブルのカラム名が入る
		echo $row["id"]." ";
		echo $row["name"]." ";
		echo $row["comment"]." ";
		echo $row["ts"]."<br>";
		echo "<hr>";
	}

?>

</body>
</html>