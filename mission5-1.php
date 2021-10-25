<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>
    <body>
    <h1>掲示板</h1>
    <style>
        h1 {
            padding: 0.4em 0.5em;/*文字の上下 左右の余白*/
            color: #494949;/*文字色*/
            background: #f4f4f4;/*背景色*/
            border-left: solid 5px #7db4e6;/*左線*/
            border-bottom: solid 3px #d7d7d7;/*下線*/
        }
    </style>
    <?php
        //DB接続設定
        $dsn = 'データベース';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //投稿機能
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && empty($_POST["edit"])){
            
            $sql = $pdo -> prepare("INSERT INTO tbtest5(name,comment,date,pass) VALUES(:name,:comment,:date,:pass)");
            $sql -> bindParam(':name',$name,PDO::PARAM_STR);
            $sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
            $sql -> bindParam(':date',$date,PDO::PARAM_STR);
            $sql -> bindParam(':pass',$pass,PDO::PARAM_STR);
            
            $name = $_POST["name"];//名前
            $comment = $_POST["comment"];//コメント
            $pass = $_POST["pass"];//パスワード
            $date = date("Y/m/d H:i:s");//日付
            
            $sql -> execute();
            echo "---　新規投稿を受け付けました　---<br><br>";
        }
        
        //編集選択機能
        if(!empty($_POST["editnum"]) && !empty($_POST["editpass"])){
            error_reporting(E_ALL^E_NOTICE);
            
            $editnum = $_POST["editnum"];//編集対象番号
            $editpass = $_POST["editpass"];//編集パスワード
            
            $id = $editnum;
            $sql = 'SELECT * FROM tbtest5 WHERE id=:id';
            $stmt = $pdo->prepare($sql);//←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);// ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();// ←SQLを実行する。
            $results = $stmt->fetchAll();
            
            foreach($results as $row){
                if($row['id']==$editnum && $row['pass']==$editpass){
                    $NewId = $row['id'];
                    $NewName = $row['name'];
                    $NewComment = $row['comment'];
                    $NewPass = $row['pass'];
                }
            }
            
            if($row['id']!=$editnum){
                echo "";
            }elseif($row['pass']!=$editpass){
                echo "---　パスワードが違います　---<br><br>";
            
            }
        }
        
        //編集実行機能    
        if(!empty($_POST["edit"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){    
            
            $edit = $_POST["edit"];//編集番号指定用フォーム
            
            $id = $edit;//変更する投稿番号
            $name = $_POST["name"];//変更したい名前
            $comment = $_POST["comment"];//変更したいコメント
            $date = date("Y/m/d H:i:s");//日付
            $pass = $_POST["pass"];//パスワード
            
            $sql = 'UPDATE tbtest5 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name',$name,PDO::PARAM_STR);
            $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
            $stmt->bindParam(':date',$date,PDO::PARAM_STR);
            $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
            echo "---　編集しました　---<br><br>";
        }
        
        //削除機能
        if(!empty($_POST["delete"]) && !empty($_POST["delpass"])){
                
            $delete = $_POST["delete"];//削除
            $delpass = $_POST["delpass"];//削除パスワード
            
            $id = $delete;
            $sql = 'SELECT * FROM tbtest5 WHERE id=:id';
            $stmt = $pdo->prepare($sql);//←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);// ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();// ←SQLを実行する。
            $results = $stmt->fetchAll();
            foreach($results as $row){
                if($row['id']==$delete && $row['pass']==$delpass){
                    $sql = 'delete from tbtest5 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();
                    echo "---　削除しました　---<br><br>";
                }else{
                    echo "---　パスワードが違います　---<br><br>";
                }
            }
            
        }

    ?>
    
    <form action="" method="post">
        【　投稿フォーム　】<br>
            <input type="text" name="name" placeholder="名前" value="<?php if(!empty($_POST['editnum'])){if($row['id']==$_POST['editnum'] && $row['pass']==$editpass){echo $NewName;}} ?>">
                <br><input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($_POST['editnum'])){if($row['id']==$_POST['editnum'] && $row['pass']==$editpass){echo $NewComment;}} ?>">
<!--「編集番号指定用フォーム」を用意-->
            <input type="hidden" name="edit" placeholder="編集番号指定用フォーム"value="<?php if(!empty($_POST['editnum'])){if($row['id']==$_POST['editnum'] && $row['pass']==$editpass){echo $NewId;}} ?>">
<!--名前コメント　パスワード-->            
            <br><input type="password" name="pass" placeholder="パスワード">
            <input type="submit" name="submit"><br><br>
            
<!--「削除番号指定用フォーム」を用意-->
        【　削除フォーム　】<br>
            <input type="number" name="delete" placeholder="削除対象番号">
<!--削除　パスワード-->            
            <br><input type="password" name="delpass" placeholder="パスワード">
            <input type="submit" name="delsubmit" value="削除"><br><br>
            
<!--「編集対象番号」の入力と「編集」ボタンが1つある-->
        【　編集フォーム　】<br>
            <input type="number" name="editnum" placeholder="編集対象番号">
<!--編集　パスワード-->            
            <br><input type="password" name="editpass" placeholder="パスワード">
            <input type="submit" name="editsubmit" value="編集"><br><br>
    </form>
    
    <h3>投稿一覧</h3>
    <?php

        $sql = 'SELECT * FROM tbtest5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    ?>
    
    </body>
</html>