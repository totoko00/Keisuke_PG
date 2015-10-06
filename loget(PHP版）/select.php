<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>Loget-ALL-</title>
</head>
<body bgcolor="#f0f8ff" text="#000000">
<h2>ログデータの表示</h2><br>データベースに保存されてあるチャットログを表示します<br>
<?php
$kn = $_GET["item"];
date_default_timezone_set('Asia/Tokyo');
$db = mysql_connect("localhost","root","fukuoka");
mysql_select_db("openfire");
mysql_set_charset("utf8");
$item = $kn;
// 抽出条件の設定
					   // フォームで設定された条件を元に WHEREとOREDER を作成する。
function setwhere(){
// 空の変数の初期化
    $order_str = "";
    $where_str = "";

    // 日付で並び替えをする為のオーダー
    $order_str = " ORDER BY sentDate DESC";

    // キーワードが入力されていた場合、SQL文で発言者・キーワード・取得者から検索をする為>の構文
    if(!empty($_POST['keyword'])){
                $where_str =" WHERE (((fromJID) like '%".mysql_real_escape_string($_POST['keyword'])."%') OR ".
        "((body) like '%".mysql_real_escape_string($_POST['keyword'])."%') OR ".
        "((toJID) like '%".mysql_real_escape_string($_POST['keyword'])."%'))";
    }
    return $where_str.$order_str;
}
// XSS
 function h($str='') {
  if(is_array($str)) {
   $h = function_exists("h") ? "h" : array(&$this, "h");
   return array_map($h, $str);
	}else {
   if(!is_numeric($str)) {
    $str = htmlspecialchars($str, ENT_QUOTES, "UTF-8"); // 文字コードは適宜変更
   }
   return $str;
  }
 }
// SQL Injection サニタイズ処理
 function q($str='') {
  if(is_array($str)) {
   $q = function_exists("q") ? "q" : array(&$this, "q");
   return array_map($q, $str);
  }else {
   if(get_magic_quotes_gpc()) {
    $str = stripslashes($str);
   }
   if(!is_numeric($str)) {
    $ver = explode('.', phpversion());
    if(intval($ver[0].$ver[1])>=43) {
     $str = mysql_real_escape_string($str);
    }else {
     $str = addslashes($str);
     $pre = array('/\n/m', '/\r/m', '/\x1a/m');
     $after = array('\\\n', '\\\r', '\Z');
					 $str = preg_replace($pre, $after, $str);
    }
   }
   return $str;
  }
 }
//ページング処理
//総レコード数を取得する
$sql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive";
$res = mysql_query($sql) or die ("データ抽出エラー");
$row = mysql_fetch_array($res, MYSQL_ASSOC);
$reccnt = $row["reccnt"];

//取り出す最大レコード数 $lim = 20 にすれば、1ページにデータを20件表示する
if(!empty($_POST['nitem'])){
        $item = $_POST['nitem'];
        $lim = $item;
        }else{
        $lim = $item;
        }
$p = 1;
//最初と最後のページ番号を定義
$first = 1;
$last = ceil($reccnt / $lim);
				//表示するページ位置を取得
$p = intval(@$_GET['p']);
if ($p < $first) {
    $p = $first;
}
elseif ($p > $last) {
    $p = $last;
}

//表示するレコード位置を取得
$st = ($p - 1) * $lim;

//前後のページ移動数と表示数
//$page = 10 にすれば、現在のページの前後10ページへのリンク番号を表示
//$page = 10 にすれば、現在のページの前後10ページ目に移動できる
$page = 3;

//前後$pageページ移動した際のページ番号を取得
$prev = $p - $page;
$next = $p + $page;

//前後1ページ移動した際のページ番号を取得
$prev1 = $p - 1;
$next1 = $p + 1;
				//SELECTコマンドを実行して、$stレコード目から$lim件の各データを取得し、大きい順に並べる
$sql = "SELECT * FROM ofMessageArchive ORDER BY sentDate DESC LIMIT $st, $lim;";
$res = mysql_query($sql) or die ("データ抽出エラー");

?>
<br><br>
<input type="button" value="トップ" style="WIDTH:200px" onClick="location.href='index.php'"><br>
<br>
<form name="form1" method="post" action="">
<table border="1">
<tr>
    <td>絞込みワード(発言者、内容、発言場所)</td>
    <td><label for="keyword"></label>
    <input type="text" name="keyword" id="keyword"></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" name="search" id="search" value="抽出"></td>
</tr>
</table><br><br>
現在1ページあたりの表示件数は<?php echo "<font size=+2><Font Color=#ff0000>{$item}件</Font></font>"; ?>です。
<form name="form2" method="post" action="">
<table border="1">
		<tr><td>
        <p>１ページあたりの表示件数：<br>
        <input type="radio" name="nitem" value="20">：２０件
        <input type="radio" name="nitem" value="30">：３０件
        <input type="radio" name="nitem" value="100">：１００件
        </p></td>
        <td bgcolor="#CCCCCC"><input type="submit" value="件数変更"></td>
        </tr>
</table></form><br><br>
<table border="1">
        <tr bgcolor="#CCCCCC">
                <td style="height:40px" align="center"><big><b>日付</b></big></td>
                <td style="height:40px" align="center"><big><b>発言者</b></big></td>
                <td style="height:40px" align="center"><big><b>内容</b></big></td>
                <td style="height:40px" align="center"><big><b>発言場所（チャットルーム等）</b></big></td>
        </tr>
<?php
$sql1 = "SELECT fromJID, toJID, sentDate, body FROM ofMessageArchive".setwhere()." LIMIT $st, $lim;";
$res1 = mysql_query($sql1);
//取得したデータを一件ずつ表示
while($row = mysql_fetch_array($res1, MYSQL_ASSOC)) {
?>
<tr>
<td style="height:35px"><?php echo date("Y/m/d H:i",$row["sentDate"]/1000); ?></td>
<td style="height:35px"><font color=#ff0000><?php echo $row["fromJID"]; ?></font></td>
<td width="650" style="height:35px"><?php echo nl2br(h($row["body"])) ?></td>
<td style="height:35px"><font color=#0000ff><b><i><?php echo $row["toJID"]; ?></i></b></font></td>
</tr>
<?php
}
mysql_close($db);
//$lim と $page の数値を変更して、自由にご利用して下さい。
//ページ表示設定関係
//最初のページへ移動
if ($p >= 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$first&item=$item\">(先頭)&ensp;</a>";
}
//1ページ前のページ
if ($p > 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$prev1&item=$item\"><-前 </a>";
}
//各ページ番号への移動リンクを表示
for ($cnt = $p - $page; $cnt <= $last; $cnt++) {
    if ($cnt < 1) {
        $cnt = 1;
    }
		$pageno = "<a href=\"".$_SERVER["PHP_SELF"]."?p=$cnt&item=$item\"> $cnt </a>";

    //表示番号を指定数に区切る。ページ番号と現在のページが同一の場合はリンク無し
にする
    if ($cnt <= $p + $page) {
        if ($cnt == $p) {
            $pageno = "<font size=\"4\"> [$p] </font>";
        }
        echo $pageno;
    }
}
//1ページ後のページ
if (($next1 - 1) * $lim < $reccnt) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$next1&item=$item\"> 次-></a>";
}
//最後のページへ移動
if ($p < $last) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$last&item=$item\">&ensp;(最後)</a>";
}
?>
</table></form>
<?php
//ページ表示設定関係
//最初のページへ移動
if ($p >= 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$first&item=$item\">(先頭)&ensp;</a>";
}
//1ページ前のページ
if ($p > 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$prev1&item=$item\"><-前 </a>";
}
//各ページ番号への移動リンクを表示
for ($cnt = $p - $page; $cnt <= $last; $cnt++) {
    if ($cnt < 1) {
        $cnt = 1;
    }
    $pageno = "<a href=\"".$_SERVER["PHP_SELF"]."?p=$cnt&item=$item\"> $cnt </a>";

    //表示番号を指定数に区切る。ページ番号と現在のページが同一の場合はリンク無しにする
	if ($cnt <= $p + $page) {
        if ($cnt == $p) {
            $pageno = "<font size=\"4\"> [$p] </font>";
        }
        echo $pageno;
    }
}
//1ページ後のページ
if (($next1 - 1) * $lim < $reccnt) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$next1&item=$item\"> 次-></a>";
}
//最後のページへ移動
if ($p < $last) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$last&item=$item\">&ensp;(最後)</a>";
}
?>
<br><br>
<input type="button" value="トップ" style="WIDTH:200px" onClick="location.href='index.php'">
</body>
</html>