<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>Loget-SEARCH-</title>
</head>
<body bgcolor="#f0f8ff" text="#000000">
<?php
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

date_default_timezone_set('Asia/Tokyo');
$kn = $_GET["item"];
$item = $kn;
$sfromJID = $_GET["search_fromJID"];
$stoJID = $_GET["search_toJID"];
if(strpos($stoJID,'#') !== false ){
        $stoJID = trim("$stoJID","#");
}
$sword = $_GET["search_word"];
//日付の取得
$sy1 = $_GET["se_year1"];
if($sy1 == ""){$sy1 = 0;}
$sm1 = $_GET["se_month1"];
if($sm1 == ""){$sm1 = 0;}
$sd1 = $_GET["se_day1"];
if($sd1 == ""){$sd1 = 0;}
$sy2 = $_GET["se_year2"];
if($sy2 == ""){$sy2 = 0;}
$sm2 = $_GET["se_month2"];
if($sm2 == ""){$sm2 = 0;}
$sd2 = $_GET["se_day2"];
if($sd2 == ""){$sd2 =0;}
//取得した日付をunixtimeに変換
$jns1 = mktime(0,0,0,$sm1,$sd1,$sy1);//(時、分、秒、月、日、年)で右から省略可能。
$jns2 = mktime(0,0,0,$sm2,$sd2,$sy2);
if($jns1 == 943920000){//すべて0だと943920000となる
        $ns1 ="";
}else{
        $ns1 = $jns1."000";
}
if($jns2 == 943920000){
        $ns2 ="";
}else{
        $ns2 = $jns2."000";
}
if($sfromJID == "" and $stoJID == "" and $sword == "" and ($sy1 == "" or $sm1 == "" or $sd1 == "") and ($sy2 == "" or $sm2 == "" or $sd2 == "")){
?>
未入力の項目があります。<br>
ブラウザのバックボタンで戻り、入力内容を確認してください。
<?php //
}else{
date_default_timezone_set('Asia/Tokyo');
$db = mysql_connect("localhost","root","fukuoka");
mysql_select_db("openfire");
mysql_set_charset("utf8");

//入力判定（ページング用）
if($sfromJID != "" and $stoJID != "" and $sword != ""){//ooo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1 > $ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ns2'";
                        }
                        else{//通常
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate <='$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%'";
                }
        }
        elseif($sfromJID != "" and $stoJID != "" and $sword == ""){//oox
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1 > $ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate <= '$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%'";
                }
        }
        elseif($sfromJID != "" and $stoJID == "" and $sword == ""){//oxx
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
								$psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and sentDate <= '$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%'";
                }
        }
        elseif($sfromJID != "" and $stoJID == "" and $sword != ""){//oxo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate <= '$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%'";
                }
        }
        elseif($sfromJID == "" and $stoJID != "" and $sword != ""){//xoo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate <= '$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%'";
                }
        }
        elseif($sfromJID == "" and $stoJID != "" and $sword == ""){//xox
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
								$psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%' and sentDate <= '$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where toJID like '%$stoJID%'";
                }
        }
        elseif($sfromJID == "" and $stoJID == "" and $sword != ""){//xxo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where body like '%$sword%' and sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where body like '%$sword%' and sentDate <= '$ns2'";
                }
                else{
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where body like '%$sword%'";
                }
        }
        elseif($sfromJID == "" and $stoJID == "" and $sword == ""){//xxx
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where sentDate <= '$ans1' and sentDate >= '$ns2'";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where sentDate >= '$ns1' and sentDate <= '$ans2'";
                                                }
                                                else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where sentDate >= '$ns1' and sentDate <= '$ans2'";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where sentDate >= '$ns1'";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                                $psql = "SELECT COUNT(*) AS reccnt FROM ofMessageArchive where sentDate <= '$ns2'";
                }
				        }

//ページング処理
//総レコード数を取得する
//確認用；echo "ページング".$psql."<br>";
$pres = mysql_query($psql) or die ("データ抽出エラー");
$prow = mysql_fetch_array($pres, MYSQL_ASSOC);
$reccnt = $prow["reccnt"];

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

//入力判定(データ出し)
        if($sfromJID != "" and $stoJID != "" and $sword != ""){//ooo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1 > $ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{//通常
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' and sentDate <='$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                else{
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and body like '%$sword%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID != "" and $stoJID != "" and $sword == ""){//oox
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1 > $ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
								$jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                else{
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and toJID like '%$stoJID%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID != "" and $stoJID == "" and $sword == ""){//oxx
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                else{
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID != "" and $stoJID == "" and $sword != ""){//oxo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
						}
                else{
                        $sql = "select * from ofMessageArchive where fromJID like '%$sfromJID%' and body like '%$sword%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID == "" and $stoJID != "" and $sword != ""){//xoo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                else{
                        $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and body like '%$sword%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID == "" and $stoJID != "" and $sword == ""){//xox
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                else{
                        $sql = "select * from ofMessageArchive where toJID like '%$stoJID%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID == "" and $stoJID == "" and $sword != ""){//xxo
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where body like '%$sword%' and sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        else{
						     $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where body like '%$sword%' and sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where body like '%$sword%' and sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where body like '%$sword%' and sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                else{
                        $sql = "select * from ofMessageArchive where body like '%$sword%' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }
        elseif($sfromJID == "" and $stoJID == "" and $sword == ""){//xxx
                if($ns1 != "" and $ns2 != ""){//oo
                        if($ns1>$ns2){//fromが未来を指した時
                                $asd1 = $sd1+1;
                                $jans1 = mktime(0,0,0,$sm1,$asd1,$sy1);
                                $ans1 = $jans1."000";
                                $sql = "select * from ofMessageArchive where sentDate <= '$ans1' and sentDate >= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                        elseif($ns1 == $ns2){//同じ日で検索した時
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                                                }
                                                else{
                                $asd2 = $sd2+1;
                                $jans2 = mktime(0,0,0,$sm2,$asd2,$sy2);
                                $ans2 = $jans2."000";
                                $sql = "select * from ofMessageArchive where sentDate >= '$ns1' and sentDate <= '$ans2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                        }
                }
                elseif($ns1 != "" and $ns2 == ""){//ox
                        $sql = "select * from ofMessageArchive where sentDate >= '$ns1' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
                elseif($ns1 == "" and $ns2 != ""){//xo
                        $sql = "select * from ofMessageArchive where sentDate <= '$ns2' ORDER BY sentDate DESC LIMIT $st, $lim;";
                }
        }

$result = mysql_query($sql);
if($reccnt == 0)
{
//確認用：echo $sql;
echo "<br><br>";
print "ログデータがありませんでした。\n";
echo"<br><br>";
}
else
{
//確認用：echo $sql."<br>";
?>
<h2><font size=+2>ログデータの表示</font></h2><br>データベースに保存されてあるチャットログを表示します<br>
<input type="button" value="再検索" style="WIDTH:200px" onClick="location.href='index.php'">
<br><br>
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
echo "<font size=+2><Font Color=#ff0000>{$reccnt}件</Font>ログが登録されています</font><br><br>";
echo "<font size=+2>検索ワード</font><br>";
echo "<font size=+1>&ensp;発言者：<B><Font Color=#ff0000>{$sfromJID}</Font></B><br>
        &ensp;発言場所(チャットルーム等)：<B><Font Color=#0000ff>{$stoJID}</Font></B><br>
        &ensp;キーワード：<B><Font Color=#008000>{$sword}</Font></B><br>
        &ensp;日付(From)：<B><Font Color=#FF8C00>{$sy1}/{$sm1}/{$sd1}</Font></B><br>
        &ensp;日付(To)：<B><Font Color=#FF00FF>{$sy2}/{$sm2}/{$sd2}</Font></B>";
echo "<br><br>";
while($row = mysql_fetch_array($result))
{
?>
<tr>
<td style="height:35px"><?php echo date("Y/m/d H:i",$row["sentDate"]/1000); ?></td>
<td style="height:35px"><font color=#ff0000><?php echo $row["fromJID"]; ?></font></td>
<td width="650" style="height:35px"><?php echo  nl2br(h($row["body"])) ?></td>
<td style="height:35px"><font color=#0000ff><b><i><?php echo strstr($row["toJID"], '@', true); ?></i></b></font></td>
</tr>
<?php
}
mysql_close($db);
}
}
if($reccnt != 0)
{
if ($p >= 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$first&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\">(先頭)&ensp;</a>";
}
//1ページ前のページ
if ($p > 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$prev1&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\"><-前 </a>";
}
//各ページ番号への移動リンクを表示
for ($cnt = $p - $page; $cnt <= $last; $cnt++) {
    if ($cnt < 1) {
        $cnt = 1;
    }
    $pageno = "<a href=\"".$_SERVER["PHP_SELF"]."?p=$cnt&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\"> $cnt </a>";

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
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$next1&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\"> 次-></a>";
}
//最後のページへ移動
if ($p < $last) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$last&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\">&ensp;(最後)</a>";

}
}
?>
</table>
<br>
<?php
if($reccnt != 0)
{
if ($p >= 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$first&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\">(先頭)&ensp;</a>";
}
//1ページ前のページ
if ($p > 1) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$prev1&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\"><-前 </a>";
}
//各ページ番号への移動リンクを表示
for ($cnt = $p - $page; $cnt <= $last; $cnt++) {
    if ($cnt < 1) {
        $cnt = 1;
    }
    $pageno = "<a href=\"".$_SERVER["PHP_SELF"]."?p=$cnt&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\"> $cnt </a>";

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
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$next1&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\"> 次-></a>";
}
//最後のページへ移動
if ($p < $last) {
    echo "<a href=\"".$_SERVER["PHP_SELF"]."?p=$last&search_fromJID=$sfromJID&search_toJID=$stoJID&search_word=$sword&se_year1=$sy1&se_month1=$sm1&se_day1=$sd1&se_year2=$sy2&se_month2=$sm2&se_day2=$sd2&item=$item\">&ensp;(最後)</a>";
}
}
?>
<br><br>
<input type="button" value="再検索" style="WIDTH:200px" onClick="location.href='index.php'">
</body>
</html>