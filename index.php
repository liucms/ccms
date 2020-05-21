<?php 
date_default_timezone_set('PRC'); 
session_start(); 
error_reporting(0); 
if(empty($_SESSION['token'])) { 
    $token = md5(mt_rand(1000000,9999999)); 
    $_SESSION['token'] = $token; 
} else {
    $token = $_SESSION['token'];
}
if(!empty($_POST['token']) && $_SESSION['token'] == $_POST['token'] && !empty($_POST['email'])&&!empty($_POST['password'])) { 
    preg_match('/^[1-9a-zA-Z\d_]{5,}$/i',$_POST['email'],$usermail);
    preg_match('/^[1-9a-zA-Z\d_]{5,}$/i',$_POST['password'],$userpsword);
    if($usermail['0'] == 'admin' && $userpsword['0'] == 'djkk123'){set('user', '1', 'admin', $expire=6000000);}
    if($usermail['0'] == 'user1' && $userpsword['0'] == 'djkk123'){set('user', '2', 'user1', $expire=6000000);}
    if($usermail['0'] == 'user2' && $userpsword['0'] == 'djkk123'){set('user', '3', 'user2', $expire=6000000);}
}
function db($srt=0) {
    if($srt){
        $host = '127.0.0.1';//数据库地址
        $port = '3306';     //端口
        $dbname = 'root';   //数据库表
        $db = array(
            'username' => 'root',   //用户名
            'password' => 'root',   //密码
            'dsn' => 'mysql:host='.$host.';dbname='.$dbname.';port='.$port.';charset=utf8',
        );
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        try{
            $pdo = new PDO($db['dsn'], $db['username'], $db['password'], $options);
        }catch(PDOException $e){
            die('数据库连接失败:' . $e->getMessage());
        }
        return $pdo;
    } else {
        return $pdo = NULL;
    }
}
if(!empty($_SESSION['user'])&&(get('user')=='1'||get('user')=='2'||get('user')=='3')) {
    if(!empty($_POST['token']) && $_SESSION['token'] == $_POST['token']&&!empty($_POST['downall'])){
        $data = arrayToString(get_select_all(2));
        $filename = '导出数据.txt';
        header("Content-type: text/plain");
        header("Accept-Ranges: bytes");
        header("Content-Disposition: p_w_upload; filename=".$filename);
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        exit($data);
    }
    if(!empty($_POST['token']) && $_SESSION['token'] == $_POST['token']&&!empty($_POST['id'])){
        if(!empty($_POST['searchs'])) {
            $_SESSION['id'] = ceil($_POST['id']);
            $idData = get_select(ceil($_POST['id']));
            unset($_SESSION['token']);
            $token = md5(mt_rand(1000000,9999999)); 
            $_SESSION['token'] = $token; 
            if(empty($idData)){
                echo "<script src=\"/layer/jquery.min.js\"></script><script src=\"/layer/layer.js\"></script><script>layer.open({content: '数据库无记录！', icon: 2, btn: ['确认'],yes: function(index) {window.location.href='/';},btn2: function(index) {},cancel: function() {}});</script>";
            }
        }elseif(!empty($_POST['award'])) {
            $_SESSION['id'] = ceil($_POST['id']);
            $idData = get_select(ceil($_POST['id']));
            if($idData['0']['value'] != '2'){
            $AwardData = get_update(ceil($_POST['id']),'2');}
            unset($_SESSION['token']);
            $token = md5(mt_rand(1000000,9999999)); 
            $_SESSION['token'] = $token; 
            if($AwardData){
                echo "<script src=\"/layer/jquery.min.js\"></script><script src=\"/layer/layer.js\"></script><script>layer.open({content: '彩金赠送成功！', icon: 1, btn: ['确认'],yes: function(index) {window.location.href='/';},btn2: function(index) {},cancel: function() {}});</script>";
            }else{
                echo "<script src=\"/layer/jquery.min.js\"></script><script src=\"/layer/layer.js\"></script><script>layer.open({content: '已赠送过彩金！', icon: 2, btn: ['确认'],yes: function(index) {window.location.href='/';},btn2: function(index) {},cancel: function() {}});</script>";
            }
        }
    }
    if((!empty($_POST['award'])||!empty($_POST['searchs']))&&empty($_POST['id'])) {
        echo "<script src=\"/layer/jquery.min.js\"></script><script src=\"/layer/layer.js\"></script><script>layer.open({content: '手机号不能为空！', icon: 2, btn: ['确认'],yes: function(index) {window.location.href='/';},btn2: function(index) {},cancel: function() {}});</script>";
    } 
    if(!empty($_FILES["file"]["tmp_name"])) {
        move_uploaded_file($_FILES["file"]["tmp_name"], 'data.txt');unset($_FILES);
        echo '<!DOCTYPE HTML><html><meta charste="utf-8"><body><p>数据导入中...</p><script type="text/javascript">window.location.href = "/index.php?q=1";</script></body></html>';
        exit();
    }
    if(!empty($_GET['q']) && !empty(get_insert_all(ceil($_GET['q'])))) {
        echo '<!DOCTYPE HTML><html><meta charste="utf-8"><body><p>第'.ceil($_GET['q']).'份数据导入中...</p><script type="text/javascript">window.location.href = "/index.php?q='.(ceil($_GET['q'])+1).'";</script></body></html>';
        exit();
    } else if(!empty($_GET['q'])){
        @unlink('data.txt');
        echo '<script type="text/javascript">window.location.href = "/";</script>';
        exit();
    }
}
function get_select($sum) {
    db();$pdo = db(1);
    $stmt = $pdo->prepare("select * from haoma where name = ?");
    $stmt->bindValue(1,ceil($sum));
    $stmt->execute();
    return $stmt->fetchAll();
} 
function get_select_all($sum) {
    db();$pdo = db(1);
    $stmt = $pdo->prepare("select `name` from haoma where value = ?");
    $stmt->bindValue(1,ceil($sum));
    $stmt->execute();
    return $stmt->fetchAll();
} 
function get_insert_all($sum,$inData = 'data.txt') {
    db();$pdo = db(1);
    $intime = strtotime(date("YmdHis"));
    $sqlall = "INSERT INTO haoma(`name`,`value`,`time`,`origin`,`operator`) VALUES ";
    $idSum = array_slice(@file($inData),((($sum-1)*5000)>1?($sum-1)*5000:0),5000);
    if(!empty($idSum)){
        for($i=0;$i<count($idSum);$i++) {
            $sqlall .= "(".trim(ceil($idSum[$i])).",'1','".trim($intime)."','".trim($_SESSION['user']['user'])."','0'),";
        }
        $sqlall = substr($sqlall,0,strlen($sqlall)-1);
        if ($pdo->exec($sqlall)) {
            return $pdo->lastinsertid();
        } else {
            return '0';
        }
    } else {
        return '0';
    }
} 
function get_update($name = '0',$value = '1') {
    db();$pdo = db(1);
    $intime = strtotime(date("YmdHis"));
    if(!empty($name) && !empty($value)){
        $stmt = $pdo->prepare("UPDATE `haoma` SET `value`=?,`operator`=? WHERE (`name`=?)");
        $stmt->bindValue(1, ceil($value));
        $stmt->bindValue(2, trim($_SESSION['user']['user']));
        $stmt->bindValue(3, ceil($name));
        $stmt->execute();
        return $stmt->rowCount();
    } else {
        return '0';
    }
}
function set($name, $data, $user, $expire=600){
    $session_data = array();
    $session_data['data'] = $data;
    $session_data['user'] = $user;
    $session_data['expire'] = time()+$expire;
    $_SESSION[$name] = $session_data;
}
function get($name){
    if(isset($_SESSION[$name])){
        if($_SESSION[$name]['expire']>time()){
            return $_SESSION[$name]['data'];
        }else{
            clear($name);
        }
    }
    return false;
}
function clear($name){
    unset($_SESSION[$name]);
}
function arrayToString($arr) {
    if (is_array($arr)){
        return implode(PHP_EOL, array_map('arrayToString', $arr));
    }
    return $arr;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>手机号数据送彩金查询</title>
<link rel="stylesheet" href="/assets/css/amazeui.min.css">
<style>.header {text-align: center;}.header h1 {font-size: 200%;color: #333;margin-top: 30px;}.header p {font-size: 14px;}</style>
</head>
<body>
<?php if(!empty($_SESSION['user'])&&(get('user')=='1'||get('user')=='2'||get('user')=='3')) { ?>
<div class="header">
  <div class="am-g">
    <h1>手机号送彩金查询</h1>
    <p>导入的手机数据格式为TXT文件：一行一个手机号<br/></p>
    <p><div id="showtimes"><script type="text/javascript">show_cur_times();</script></div></p>
  </div>
  <hr />
</div>
<div class="am-g am-u-lg-6 am-u-md-8 am-u-sm-centered">
<?php if(get('user')=='1') {?>
<div class="am-form-group">
<form class="am-form-inline" action="" method="post" enctype="multipart/form-data">
    <label for="doc-ipt-3-1" class="am-form-label">手机数据:</label>
    <div class="am-form-group am-form-file">
    <button type="button" class="am-btn am-btn-danger">
    <i class="am-icon-cloud-upload"></i>请选择要上传的TXT文件</button>
    <input type="file" name="file" multiple>
    </div>
    <input class="am-btn am-btn-default am-btn-primary" type="submit" name="submit" value="导入数据">
    <input class="am-btn am-btn-default am-btn-primary" type="submit" name="downall" value="导出数据">
    <input type="hidden" name="token" value="<?php echo $token; ?>" /> 
</form>
</div>
<?php } ?>
<div class="am-form-group">
<form id="myFrm" class="am-form-inline" role="form" action="" method="post" enctype="multipart/form-data">
    <label for="doc-ipt-3-1" class="am-form-label">手机彩金:</label>
    <input class="am-form-field" type="text" name="id" <?php echo empty($_SESSION['id'])?'':'value="'.ceil($_SESSION['id']).'"'; ?> />
    <input class="am-btn am-btn-default am-btn-primary" name="searchs" type="submit" value="记录查询"> 
    <?php if(get('user')=='1' || get('user')=='2') {?><input class="am-btn am-btn-default am-btn-primary" name="award" type="submit" value="送彩金"><?php } ?>
    <input type="hidden" name="token" value="<?php echo $token; ?>" /> 
</form>
</div>

<?php if(!empty($idData)){ ?>
<table class="am-table am-table-bordered am-table-striped am-table-compact">
<thead><tr><th>ID</th><th>会员账号</th><th>彩金赠送</th><th>客服人员</th><th>导入时间</th></tr></thead>
<?php for($i=0;$i<count($idData);$i++){ ?>
<tbody><tr><td><?php echo ceil($idData[$i]['id']); ?></td><td><?php echo ceil($idData[$i]['name']); ?></td><td><?php echo ceil($idData[$i]['value']) == '1' ? '<font color="blue">否</font>':'<font color="red">是</font>'; ?></td><td><?php echo $idData[$i]['operator']; ?></td><td><?php echo date("Y-m-d H:i:s",ceil($idData[$i]['time'])); ?></td></tr></tbody>
<?php } ?>
</table>
<?php } ?>
</div> 
<?php } else { ?>
<div class="header">
  <div class="am-g">
    <h1>手机号数据登录界面</h1>
    <div id="showtimes"><script type="text/javascript">show_cur_times();</script></div>
  </div>
  <hr />
</div>
<div class="am-g">
  <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
    <form method="post" class="am-form">
      <label for="email">用户:</label>
      <input type="text" name="email" value="">
      <br>
      <label for="password">密码:</label>
      <input type="password" name="password" value="">
      <br />
      <div class="am-cf">
        <input type="submit" name="" value="登 录" class="am-btn am-btn-primary am-btn-sm am-fl">
        <input type="hidden" name="token" value="<?php echo $token; ?>" />
      </div>
    </form>
    <hr>
  </div>
</div>
<?php }?>
<!--[if (gte IE 9)|!(IE)]><!-->
<script src="/layer/jquery.min.js"></script>
<!--<![endif]-->
<script src="/assets/js/amazeui.min.js"></script>
<script type="text/javascript">
function show_cur_times(){
    var myDate = new Date();
    var year = myDate.getFullYear();
    var month = myDate.getMonth() + 1;
    var date = myDate.getDate();
    var h = myDate.getHours();
    var m = myDate.getMinutes();
    var s = myDate.getSeconds();
    var now = "当前服务器时间: " + year + '-' + conver(month) + "-" + conver(date) + " " + conver(h) + ':' + conver(m) + ":" + conver(s);
    return document.getElementById("showtimes").innerHTML= now;
}
function conver(s) {
    return s < 10 ? '0' + s : s;
}
setInterval("show_cur_times()",100);
</script>
</body>
</html>
