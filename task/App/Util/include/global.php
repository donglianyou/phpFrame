<?php
// 打印变量函数，调试使用
function dump($data=''){
    var_dump($data);
    exit;
}

// 设置memcache缓存
function setVar($key,$value,$expire='3600'){
    $mem = new MmCache(MEM_HOST,MEM_PORT);
    $mem->set($key,$value,$expire);
}

// 获取memcache缓存参数
function getVar($key){
    $mem = new MmCache(MEM_HOST,MEM_PORT);
    return $mem->get($key);
}

// 删除memcache缓存
function delVar($key){
    $mem = new MmCache(MEM_HOST,MEM_PORT);
    return $mem->remove($key);
}

// 使用redis缓存
function R(){
    $redis = new YzmRedis(REDIS_HOST,REDIS_PORT);
    return $redis;
}