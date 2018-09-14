<?php
session_start();
/**
 * 首页控制器
 */
class IndexAction extends ActionMiddleware
{
    public function index(){
        $redis = R();
        //$_SESSION['redis_demo'] = ['username' =>'zhangsan','age' =>30];
        if ($data = $redis->get("users_cache")) {
            echo "get data from redis<br>";
            $data = json_decode($data,true);
        }else{
            echo "get data from mysql!<br>";
            $data = [
                ['id'=>1,'username'=>'jason','gender'=>'male'],
                ['id'=>2,'username'=>'董','gender'=>'female'],
                ['id'=>3,'username'=>'wangcai','gender'=>'unknown'],
              ];
            $redis->set("users_cache",json_encode($data));
            $redis->expire("users_cache",5);
        }
        dump($data);
    }
    /* public function index(){
        //$userId = $this->input['user_id'];
        $name = 'Yzm';
        $age = 121;
        $data = array(
            'name'=>$name,
            'age'=>$age,
        );
        $model = new YzmDbPdo();
        $sql = "select * from class";
        $_data = $model->getRows($sql);
        echo ("<pre>");
        print_r($_data);
        $this->display('index/index.html',$data);
    } */
    public function age(){
        echo "age";
    }
    public function mem(){
        //setVar('age','21');
        //echo getVar("age");
        // delVar("name");
        phpinfo();
    }
    public function redis(){
        $redis = R();
        //$redis->set("name","good");
        //$redis->lpush('sql','select * from user');
        //echo $redis->get('name');
        $sql = $redis->rpop('sql');
        dump($sql);
    }
    // 产品列表
    public function product()
    {
        $products = [
            '1' => ['id' => 1, 'name' =>'keyboard','price'=>105.2,'stock' =>100],
            '2' => ['id' => 2, 'name' =>'monitor','price'=>2890.5,'stock' =>100],
            '3' => ['id' => 3, 'name' =>'mouse','price'=>50,'stock' =>100],
            '4' => ['id' => 4, 'name' =>'GPU','price'=>3000,'stock' =>100],
        ];
        foreach($products as $product){
            echo "<p>";
            echo "id:".$product['id']."<br>";
            echo "name:".$product['name']."<br>";
            echo "price:".$product['price']."<br>";
            echo "stock:".$product['stock']."<br>";
            echo "<a href='/index/addCart/?id=".$product['id']."'>加入购物车</a>";
            echo "</p>";
        }
    }
    // 加入购物车
    public function addCart()
    {
        $products = [
            '1' => ['id' => 1, 'name' =>'keyboard','price'=>105.2,'stock' =>100],
            '2' => ['id' => 2, 'name' =>'monitor','price'=>2890.5,'stock' =>100],
            '3' => ['id' => 3, 'name' =>'mouse','price'=>50,'stock' =>100],
            '4' => ['id' => 4, 'name' =>'GPU','price'=>3000,'stock' =>100],
        ];
        $id = $_GET['id'];
        $product = $products[$id];
        $redis = R();
        if($p = $redis->hGet('cart','productid_'.$product['id'])){
            $p = json_decode($p,true);
            $p['num']++;
            $redis->hSet('cart','productid_'.$product['id'],json_encode($p));
        }else{
            $product['num'] = 1;
            $redis->hSet('cart','productid_'.$product['id'],json_encode($product));
        }
        dump($redis->hgetAll('cart'));
    }
    // 粉丝列表
    public function fans()
    {
        $users = [
            1=>['id' => '1','username' => 'Jason','age' => 20,'password'=>md5('123')],
            2=>['id' => '2','username' => 'xiaoqiang','age' => 30,'password'=>md5('456')],
            3=>['id' => '3','username' => 'xiaoli','age' => 32,'password'=>md5('789')],
            4=>['id' => '4','username' => 'wangcai','age' => 22,'password'=>md5('100')],
        ];
        return $users;
    }
    public function login()
    {
        $this->display('index/login.html');
    }
    // 登录验证
    public function login_chk()
    {
        $users = $this->fans();
        if(!empty($_POST)){
            $username = $_POST['username'];
            $password = md5($_POST['password']);
            if($user = $this->verifyUser($username,$password)){
                $_SESSION['user'] = $user;
                echo "signed successfully!<br>";
            }
        }
        foreach ($users as $user) {
            echo "<p>用户名：".$user['username']."<br>";
            echo "年龄：".$user['age']."<br>";
            echo '<a href="flollow/?id='.$user['id'].'">关注</a><hr></p>';
        }
    }
    // 加入粉丝
    public function flollow()
    {
        $id = $_GET['id'];
        $users = $this->fans();
        $user = $users[$id];
        // 将当前登录用户写入到对方的粉丝列表当中
        $redis = R();
        /* dump($_SESSION['user']);
        exit(); */
        if(!$this->isFan($id)){
            $redis->lpush('fans_'.$id,json_encode($_SESSION['user']));
            echo "关注成功！";
        }else{
            echo "您已经关注！";
        }
    }
    // 验证用户登录信息
    public function verifyUser($username,$password){
        $users = $this->fans();
        foreach($users as $user){
            if($user['username'] == $username && $user['password'] == $password){
                return $user;
            }
        }
        return false;
    }
    // 粉丝
    public function isFan($id)
    {
        $redis = R();
        $fans = $redis->lrange('fans_'.$id,0,-1);
        foreach ($fans as $fan) {
            if ($_SESSION['user'] == json_decode($fan,true)) {
                return true;
            }
        }
        return false;
    }
    // 共同好友
    public function friends()
    {
        $redis = R();
        $redis->sadd('Jason_friends','xiaoqiang','wangcai','xiaoxiaoqiang');
        $redis->sadd('qiangqiang_friends','xiaoqiang','wangcai');
        echo "Jason 和 qiangqiang 的共同好友：<hr>";
        var_dump($redis->sinter('jason_friends','qiangqiang_friends'));
    }

    // 积分排行榜
    public function credit()
    {
        $redis = R();
        $redis->zadd('users',10,'jason');
        $redis->zadd('users',20,'xiaoqiang');
        $redis->zadd('users',5,'xiaoliu');
        $redis->zadd('users',15,'xiaozhang');
        $redis->zadd('users',3,'lisi');
        $redis->zadd('users',0,'wangwu');
        $redis->zadd('users',12,'zhaoliu');
        $redis->zadd('users',20,'dongqi');
        $redis->zadd('users',33,'xiaowangwu');

        echo "达人积分排行榜：<hr>";
        $users = $redis->zrevrange("users",0,-1,true);
        foreach($users as $user => $score){
            echo $user." : ".$score."<br>";
        }
    }
    
    public function location()
    {
        return $this->display("index/location.html");
    }
    // 存储用户当前位置所在的地理坐标，查找附近的人
    public function store_location()
    {
        if(!empty($_POST)){
            $username = $_POST['username'];
            $address = $_POST['address'];
            // google api 获取坐标信息
            //$api = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";
            // baidu api 获取坐标信息
            $api = "https://api.map.baidu.com/geocoder/v2/?address=".urlencode($address)."&output=json&ak=ctkaCK5lsHlzTItYuLR7cFEhBXx9HMYS";
            // 请求api
            $str = file_get_contents($api);
            $data = json_decode($str);
            // 纬度
            $lat = $data->result->location->lat;
            // 经度
            $lng = $data->result->location->lng;
            $redis = R();
            $result = $redis->geoadd("nearby_users",$lng, $lat, $username);
            if($result){
                echo "添加成功";
                $this->redirect('/index/nearbyUsers/',5);
            }else{
                echo "添加失败，请换个用户！";
                $this->redirect('/index/location/',5);
            }
        }else {
            echo "非法操作！";
        }
    }
    // 获取用户所在的地理位置坐标
    public function nearbyUsers()
    {
        $redis = R();
        $users = $redis->zrange("nearby_users", 0, -1);
        foreach($users as $user){
            $pos = $redis->geopos("nearby_users",$user);
            // 经度
            $lng = $pos[0][0];
            // 纬度
            $lat = $pos[0][1];
            // baidu api 获取坐标信息,pois为是否显示周边100米地理信息
            $api = "https://api.map.baidu.com/geocoder/v2/?ak=ctkaCK5lsHlzTItYuLR7cFEhBXx9HMYS&location=".$lat.",".$lng."&output=json&pois=1";
            $str = file_get_contents($api);
            $data = json_decode($str);
            echo "用户名：".$user."<br>";
            echo "所在地理位置：".$data->result->formatted_address."<br>";
            echo "周边商业信息：<br><pre>";
            var_dump($data->result->pois);
            echo "<hr>";
        }
    }
    // 查找附近的人
    public function nearBy()
    {
        return $this->display("index/nearby.html");
    }
    // 查找指定距离范围内的其他人
    public function nearByFind()
    {
        if(!empty($_POST)){
            $username = $_POST['username'];
            $redis = R();
            $data = $redis->geoRadiusByMember("nearby_users", $username, 5, 'km');
            echo "附近的用户：<br>";
            foreach($data as $user){
                echo "<p>";
                echo $user."<br>";
                $dis = $redis->geodist("nearby_users", $username, $user, 'm');
                echo "距离：".$dis." 米";
                echo "</p>";
            }
        }
    }
    // 删除附近的人
    public function remNearByUser()
    {
        return $this->display("index/remnearbyuser.html");
    }
    public function removeNearByUser()
    {
        if(!empty($_POST)){
            $username = $_POST['username'];
            $redis = R();
            if($redis->zrem("nearby_users",$username)){
                echo "清除位置信息成功！";
                $this->redirect('/index/remNearByUser/',5);
            }
        }
    }

}
