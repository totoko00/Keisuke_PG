<html>
<head>
<!-- <script src="calendarlay4.js" type="text/javascript"></script> -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>Loget-TOP-</title>
</head>
<body bgcolor="#f0f8ff" text="#000000">

<p><big>チャットログ取得用ＷＥＢアプリ</big><font size="7" color ="#40E0D0">Ｌｏ<font size="7" color ="#8B0000">ｇ</font>ｅｔ</font></p>
<h3><font color ="#ff0000">虫眼鏡のアイコン「<img src="filter_off.gif">」をクリックすると、検索ワードを直接入力できます。</font></h3>
アイコンをクリックすると、この状態(<img src="filter_on.gif">)になるので、検索ワードを入力してください。<br>
再びアイコンをクリックすると、元の状態(<img src="filter_off.gif">)に戻ります。<br><br>
<!--データベース読み込みとＳＱＬ文実行-->
<?php
date_default_timezone_set('Asia/Tokyo');
$db = mysql_connect("localhost","root","fukuoka");
mysql_select_db("openfire");
$query1 = "select name from ofMucRoom";
$res1 = mysql_query($query1);
$num = mysql_num_rows($res1);
?>
<!--LDAPへの接続-->
<?php
    //設定
    $ldap_host = "hogehoge";//LDAPサーバのホスト（セキュリティのためhogehogeとしています。）
    $ldap_port = 389; //ポート
    $ldap_dc = "dc=hoge,dc=co,dc=jp";
   // $ldap_cn = ""; //cn
    $ldap_ou1 = "People"; //ou
    $ldap_ou2 = "People";
   // $ldap_pass = ""; // パスワード
?>
<table border="1">
        <!--データの全件表示-->
        <form action="select.php" method="get">
        <tr>
                <td>全ログ表示(データベースにあるチャットログを全て閲覧できます)<br>
                <p>１ページあたりの表示件数：<br>
        <input type="radio" name="item" value="20" checked>：２０件
        <input type="radio" name="item" value="30">：３０件
        <input type="radio" name="item" value="100">：１００件
        </p></td>
                <td bgcolor="#CCCCCC"><input type="submit" value="全チャットログの表示"></td>

        </tr>
        </form>
<!--データの検索-->
        <form action="search.php" method="get">
        <tr>
                <td>
                発言者：<select name="search_fromJID">
                <option value="" selected>未入力</option>
<?php
        //接続開始
    $ldap_DN = "ou=".$ldap_ou1.",".$ldap_dc;
    $ldap_conn = ldap_connect($ldap_host, $ldap_port);

    if($ldap_conn){

        $ldap_bind  = ldap_bind($ldap_conn);

        if($ldap_bind){

            $ldap_search = ldap_search($ldap_conn, $ldap_DN,"uid=*");
            $get_entries = ldap_get_entries($ldap_conn,$ldap_search);

            //エントリ情報出力
        for ($i=0; $i<$get_entries["count"]; $i++) {
                echo '<option value="'.$get_entries[$i]["uid"][0].'">'.$get_entries[$i]["uid"][0].'</option>';
        }
        }else{
            echo "<p>バインド失敗</p>";
        }
        ldap_close($ldap_conn);
}
?>
                </select><br>
                発言場所（チャットルーム等）：<select name="search_toJID">
                <option value="" selected>未入力</option>
<?php
        //接続開始
    $ldap_DN2 = "ou=".$ldap_ou2.",".$ldap_dc;
    $ldap_conn2 = ldap_connect($ldap_host, $ldap_port);

    if($ldap_conn2){

        $ldap_bind2  = ldap_bind($ldap_conn2);

        if($ldap_bind2){

            $ldap_search2 = ldap_search($ldap_conn2, $ldap_DN2,"uid=*");
            $get_entries2 = ldap_get_entries($ldap_conn2,$ldap_search2);
            //エントリ情報出力
                        for ($j=0; $j<$get_entries2["count"]; $j++) {
                                echo '<option value="'.$get_entries2[$j]["uid"][0].'">'.$get_entries2[$j]["uid"][0].'</option>';
                        }
                if($j==$get_entries2["count"]){
                        while($row1 = mysql_fetch_array($res1)){
                                print '<option value="'.$row1["name"].'">'.$row1["name"].'(チャットルーム)'.'</option>';
                        }
        }
        }else{
            echo "<p>バインド失敗</p>";
        }
        ldap_close($ldap_conn);
}
?>
                </select><br>
                キーワード：<input type="text" name="search_word" size="80"><br>
                日付(From):
<?php
//本日の日付を取得する
    $time = time();
    $year = date("Y", $time);
    $month = date("n", $time);
    $day = date("j", $time);
    $ckm = $month -1;
if($ckm == 0){//現在の値（Toのデフォルト月）が１月だった場合１月前は前年の１２月なのでその処理を行う
    $dec = 12;
}
        //年
print("<select name=\"se_year1\">");
print("<option value=\"\">未入力</option>");
if($ckm == 0){
        $deyear = $year -1;
        for( $ii = $deyear; $ii <= $year; $ii++ ){
                if($ii == $deyear){
                        print("<option value=\"$deyear\" selected>$deyear</option>");
                }elseif( $ii == $year ){
                        print("<option value=\"$year\">$year</option>");
                }else{
                        print("<option value=\"$ii\">$ii</option>");
                }
        }
}else{
        for( $ii = 2012; $ii <= $year; $ii++ ){
                if( $ii == $year ){
				        print("<option value=\"$year\" selected>$year</option>");
                }else{
                        print("<option value=\"$ii\">$ii</option>");
                }
        }
}
print("</select>年");

        //月
print("<select name=\"se_month1\">");
print("<option value=\"\">未入力</option>");
if($ckm == 0){
        for( $jj = 1; $jj <= 12; $jj++ ){
                if($jj == $dec){
                        print("<option value=\"$dec\" selected>$dec</option>");
                }elseif( $jj == $month -1 ){
                        print("<option value=\"$jj\">$jj</option>");
                }else{
                        print("<option value=\"$jj\">$jj</option>");
                }
        }
}else{
        for( $jj = 1; $jj <= 12; $jj++ ){
                if( $jj == $month -1 ){
                        print("<option value=\"$jj\" selected>$jj</option>");
                }else{
                        print("<option value=\"$jj\">$jj</option>");
                }
        }
}

print("</select>月");

        //日
print("<select name=\"se_day1\">");
print("<option value=\"\">未入力</option>");
for( $kk = 1; $kk <=31 ; $kk++){
        if( $kk == $day ){
                print("<option value=\"$day\" selected>$day</option>");
        }
        else{
                print("<option value=\"$kk\">$kk</option>");
        }
}
print("</select>日");
?>
                        <br>
                日付（To）：
<?php
//本日の日付を取得する
    $time = time();
    $year = date("Y", $time);
    $month = date("n", $time);
    $day = date("j", $time);

        //年
print("<select name=\"se_year2\">");
print("<option value=\"\">未入力</option>");
for( $i = 2012; $i <= $year; $i++ ){
        if( $i == $year ){
                print("<option value=\"$i\" selected>$i</option>");
        }
        else{
                print("<option value=\"$i\">$i</option>");
        }
}
print("</select>年");

        //月
print("<select name=\"se_month2\">");
print("<option value=\"\">未入力</option>");
for( $j = 1; $j <= 12; $j++ ){
        if( $j == $month ){
                print("<option value=\"$month\" selected>$month</option>");
        }
        else{
                print("<option value=\"$j\">$j</option>");
        }
}
print("</select>月");

        //日
print("<select name=\"se_day2\">");
print("<option value=\"\">未入力</option>");
for( $k = 1; $k <=31 ; $k++ ){
        if( $k == $day ){
                print("<option value=\"$day\" selected>$day</option>");
        }
        else{
                print("<option value=\"$k\">$k</option>");
        }
}
print("</select>日");
?>
        <p>１ページあたりの表示件数：<br>
        <input type="radio" name="item" value="20" checked>：２０件
        <input type="radio" name="item" value="30">：３０件
        <input type="radio" name="item" value="100">：１００件
        </p>
        <td bgcolor="#CCCCCC"><input type="submit" value="閲覧する条件検索"></td>
        </tr>
        </form>
<script src="prototype.js" type="text/javascript"></script>
<script src="incautoselecter.js" type="text/javascript"></script>
<script type="text/javascript">
new IncAutoSelecter();
</script>
</table>
<?php
mysql_close($db);
?>
</body>
</html>