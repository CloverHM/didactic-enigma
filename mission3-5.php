<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_3-5</title>
    </head>
    <body>
    <?php
        //投稿機能
        $filename = "mission3-5.txt";
            
        //編集実行機能
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && empty($_POST["delete"])){
            
            $name = $_POST["name"];//名前
            $comment = $_POST["comment"];//コメント
            $date = date("Y/m/d H:i:s");//日付
                
            $edit = $_POST["edit"];//編集番号指定用フォーム
            $pass = $_POST["pass"];//名前コメントパスワード

            //カウント                
            if(file_exists($filename)){
                $lines=file($filename,FILE_IGNORE_NEW_LINES);
                $data=explode("<>",end($lines));//endは配列の1番最後の要素
                $num=$data[0]+1;
            }else{
                $num=1;
            }
                
            $newData=$num."<>".$name."<>".$comment."<>".$date."<>".$pass."<>";
                
            $fp=fopen($filename,"a");
                
            if(!empty($_POST["edit"]) && !empty($_POST["pass"])){
                    
                if(file_exists($filename)){
                    $lines=file($filename,FILE_IGNORE_NEW_LINES);
                    
                    ftruncate($fp, 0);//空に
                    
                    for($i = 0; $i < count($lines); $i++){
                        $line = explode("<>",$lines[$i]);
                        $num1 = $line[0];
                        $password = $line[4];
                        
                        if($num1 == $_POST["edit"] && $password == $_POST["pass"]){
                            fwrite($fp,$edit."<>".$name."<>".$comment."<>".$date."<>".$pass."<>".PHP_EOL);
                                
                        }else{
                            fwrite($fp,$lines[$i].PHP_EOL);
                        }
                    }
                }
            }else{
                fwrite($fp,$newData.PHP_EOL);
            }
                fclose($fp);
        }
            
            
        //編集選択機能
        if(!empty($_POST["editnum"]) && !empty($_POST["editpass"])){
                
            $edit = $_POST["edit"];//編集番号指定用フォーム
            $editnum = $_POST["editnum"];//編集対象番号
        
        //ファイル読み込み関数で、ファイルの中身を1行1要素として配列変数に代入する
            if(file_exists($filename)){
                $lines=file($filename,FILE_IGNORE_NEW_LINES);
        //ファイルを開き、        
                $fp=fopen($filename,"a");
        //先ほどの配列の要素数（＝行数）だけループさせる
                for($i = 0; $i < count($lines); $i++){
        //ループ処理内：区切り文字「<>」で分割して、投稿番号を取得
                    $line = explode("<>",$lines[$i]);
                    $num1 = $line[0];
                    $password = $line[4];
        //同・ループ処理内：投稿番号と編集対象番号を比較。イコールの場合はその投稿の「名前」と「コメント」を取得
                    if($num1 == $editnum && $password == $_POST["editpass"]){
                        $newname = $line[1];
                        $newcomment = $line[2];
                        $newnum = $line[0];
                    }
                }
                fclose($fp);
            }
        }
                
        //削除機能 
        if(!empty($_POST["delete"]) && !empty($_POST["delpass"])){
                
            $delete = $_POST["delete"];//削除
            $delpass = $_POST["delpass"];//削除パスワード

        //ファイル読み込み関数で、ファイルの中身を1行1要素として配列変数に代入する
            if(file_exists($filename)){
                $lines=file($filename,FILE_IGNORE_NEW_LINES);
        //ファイルを開く
                $fp=fopen($filename,"a");
        //ファイルの中身を空に
                ftruncate($fp, 0);
        //配列の要素数（＝行数）だけループさせる
                for($i = 0; $i < count($lines); $i++){
        //【ループ処理内：区切り文字「<>」で分割して、投稿番号を取得】
                    $line = explode("<>",$lines[$i]);
                    $num1 = $line[0];
                    $password = $line[4];
        //【同・ループ処理内：投稿番号と削除対象番号を比較。等しくない場合は、ファイルに追加書き込みを行う
                    if($num1 == $delete && $password == $_POST["delpass"]){
                        fwrite($fp,"");
                    }else{
                        fwrite($fp,$lines[$i].PHP_EOL);
                    }
                        
                }
                fclose($fp);
            }
        }
            
    ?>
        
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php  if(!empty($editnum)){if($password==$_POST["editpass"]){echo $newname;}} ?>">
        <br><input type="text" name="comment" placeholder="コメント" value="<?php  if(!empty($editnum)){if($password==$_POST["editpass"]){echo $newcomment;}} ?>">
<!--「編集番号指定用フォーム」を用意-->
        <input type="hidden" name="edit" placeholder="編集番号指定用フォーム"value="<?php if(!empty($editnum)){if($password==$_POST["editpass"]){echo $newnum;}} ?>">
<!--名前コメント　パスワード-->            
        <br><input type="password" name="pass" placeholder="パスワード">
        <input type="submit" name="submit">
            
<!--「削除番号指定用フォーム」を用意-->
        <br><br><input type="text" name="delete" placeholder="削除対象番号">
<!--削除　パスワード-->            
        <br><input type="password" name="delpass" placeholder="パスワード">
        <input type="submit" name="delsubmit" value="削除">
            
<!--「編集対象番号」の入力と「編集」ボタンが1つある-->
        <br><br><input type="text" name="editnum" placeholder="編集対象番号">
<!--編集　パスワード-->            
        <br><input type="password" name="editpass" placeholder="パスワード">
        <input type="submit" name="editsubmit" value="編集">
    </form>
        
    <?php
        
        if(file_exists($filename)){
            $lines=file($filename,FILE_IGNORE_NEW_LINES);
                    
            for($i = 0; $i < count($lines); $i++){
                $line = explode("<>",$lines[$i]);
                $password = $line[4];
                $num1 = $line[0];
            }
        }
            
        //表示
        if(file_exists($filename)){
    //【ファイルを1行ずつ読み込み、配列変数に代入する】
            $lines=file($filename,FILE_IGNORE_NEW_LINES);
    //【ファイルを読み込んだ配列を、配列の数（＝行数）だけループさせる】
            foreach($lines as $line){
    //【ループ処理内：区切り文字「<>」で分割して、それぞれの値を取得】
                $elements=explode("<>",$line);
    //【同・ループ処理内：上記で取得した値をecho等を用いて表示】
                echo $elements[0]." ".$elements[1]." ".$elements[2]." ".$elements[3];
                    
                echo "<br>";
            }
                    
        }
    ?>
    </body>
</html>