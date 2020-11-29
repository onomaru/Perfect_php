<?php

//DB接続のためのmysqli_connect()
$link = mysqli_connect('localhost', 'root', 'root', 'oneline_bbs');
if (!$link) {
    //exit()と同様
    //直近の MySQLi 関数のコールが成功あるいは失敗した際のエラーメッセージを返す
    die('データベースに接続できません' . mysqli_error($link));
}

//mysqli_select_db ( mysqli $link , string $dbname )
//データベース接続に対してクエリを実行する際に使用する、 デフォルトのデータベースを設定します。
//この関数は、接続のデフォルトデータベースを変更する際にのみ使用します。
//デフォルトデータベースは、mysqli_connect() の 4 番目の引数でも指定可能です。
//mysqli_select_db($link, 'oneline_bbs');

$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = null;
    //変数に値がセットされているかつNULLでない場合にTRUEを戻り値として返します。
    //それ以外は、FALSEを戻り値として返します。isset
    //成功した場合に string の長さ、 string が空の文字列だった場合に 0 を返します。strlen
    if (!isset($_POST['name']) || !strlen($_POST['name'])) {
        $errors['name'] = '名前を入力してください。';
    } elseif (strlen($_POST['name']) > 40) {
        $errors['name'] = '名前は40文字以内で入力してください。';
    } else {
        $name = $_POST['name'];
    }

    $comment = null;
    if (!isset($_POST['comment']) || !strlen($_POST['comment'])) {
        $errors['comment'] = 'ひとことを入力してください。';
    } elseif (strlen($_POST['comment']) > 200) {
        $errors['comment'] = 'ひとことは40文字以内で入力してください。';
    } else {
        $comment = $_POST['comment'];
    }

    if (count($errors) === 0) {
        //mysqli_real_escape_string($link, $name)
        //この関数を使用して、SQL 文中で使用できる正当な形式の SQL 文字列を作成します。
        //文字列 escapestr が、エスケープされた SQL に変換されます。その際、接続で使用している現在の文字セットが考慮されます。
        
        $sql =
        "INSERT INTO post (name, comment, create_at)
        VALUES ('"
        . mysqli_real_escape_string($link, $name) . "','"
        . mysqli_real_escape_string($link, $comment) . "','"
        . date('Y-m-d H:i:s') . "')";

        // echo $sql.PHP_EOL;

        //データベースに対してクエリ query を実行します。
        var_dump(mysqli_query($link, $sql));
        mysqli_close($link);
        header('Location: http://' .$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
}

//投稿された内容を取得するsqlを制作して結果を取得
$sql = "SELECT * FROM `post` ORDER BY `create_at` DESC";
$result = mysqli_query($link, $sql);

//取得した結果を$postsに格納
$posts = array();
if ($result !== false && mysqli_num_rows($result)) {
    while ($post = mysqli_fetch_assoc($result)) {
        $posts[] = $post;
    }
}

//取得結果を開放。接続を閉じる
mysqli_free_result($result);
mysqli_close($link);

include('views/bbs_view.php');
