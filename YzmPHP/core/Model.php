<?php
/**
 * Model类
*/
class Model extends YzmDbPdo
{
    public $table = ''; //数据表
    public $key = ""; //主键

    // 查询单条记录
    public function find($id)
    {
        $sql = "select * from {$this->table} where {$this->key} = {$id}";
        return $this->getRow($sql);
    }

    // 查询多条记录
    public function findAll()
    {
        $sql = "select * from {$this->table}";
        return $this->getRows($sql);
    }

    // 添加记录
    public function add($arr)
    {
        $add = $this->insert($this->table,$arr);
        if ($add) {
            return $this->getLastInsId(); //添加的ID
        }
    }

    // 编辑记录
    public function edit($arr,$where)
    {
        return $this->update($this->table,$arr,$where);
    }

    // 删除一条记录
    public function del($where)
    {
        return $this->delete($this->table,$where);
    }
}