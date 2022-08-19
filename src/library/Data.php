<?php
namespace mzclass\library;

/**
 * 数据处理类
 *
 *
 */
final class Data
{
    /**
     * 返回多层栏目
     * @param $data 操作的数组
     * @param int $pid 一级PID的值
     * @param string $html 栏目名称前缀
     * @param string $fieldPri 唯一键名，如果是表则是表的主键
     * @param string $fieldPid 父ID键名
     * @param int $level 不需要传参数（执行时调用）
     * @return array
     */
    static public function channelLevel($data, $pid = 0, $html = "&nbsp;", $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        if (empty($data)) {
            return array();
        }
        // dump($data);
        $arr = array();
        foreach ($data as $v) {
            if ($v[$fieldPid] == $pid) {
                $arr[$v[$fieldPri]] = $v;
                $arr[$v[$fieldPri]]['_level'] = $level;
                $arr[$v[$fieldPri]]['_html'] = str_repeat($html, $level - 1);
                $arr[$v[$fieldPri]]["_data"] = self::channelLevel($data, $v[$fieldPri], $html, $fieldPri, $fieldPid, $level + 1);
            }
        }
        // dump($arr);
        return $arr;
    }

    /**
     * 获得所有子栏目
     * @param $data 栏目数据
     * @param int $pid 操作的栏目
     * @param string $html 栏目名前字符
     * @param string $fieldPri 表主键
     * @param string $fieldPid 父id
     * @param int $level 等级
     * @return array
     */
    static public function channelList($data, $pid = 0, $html = "&nbsp;", $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        $data = self::_channelList($data, $pid, $html, $fieldPri, $fieldPid, $level);
        if (empty($data))
            return $data;
        foreach ($data as $n => $m) {
            if ($m['_level'] == 1)
                continue;
            $data[$n]['_first'] = false;
            $data[$n]['_end'] = false;
            if (!isset($data[$n - 1]) || $data[$n - 1]['_level'] != $m['_level']) {
                $data[$n]['_first'] = true;
            }
            if (isset($data[$n + 1]) && $data[$n]['_level'] > $data[$n + 1]['_level']) {
                $data[$n]['_end'] = true;
            }
        }
        //更新key为栏目主键
        $category=array();
        foreach($data as $d){
            $category[$d[$fieldPri]]=$d;
        }
        return $category;
    }

    //只供channelList方法使用
    static private function _channelList($data, $pid = 0, $html = "&nbsp;", $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        if (empty($data))
            return array();
        $arr = array();
        foreach ($data as $v) {
            $id = $v[$fieldPri];
            if ($v[$fieldPid] == $pid) {
                $v['_level'] = $level;
                $v['_html'] = str_repeat($html, $level - 1);
                array_push($arr, $v);
                $tmp = self::_channelList($data, $id, $html, $fieldPri, $fieldPid, $level + 1);
                $arr = array_merge($arr, $tmp);
            }
        }
        return $arr;
    }

    /**
     * 获得树状数据
     * @param $data 数据
     * @param $title 字段名
     * @param string $fieldPri 主键id
     * @param string $fieldPid 父id
     * @param string $emp 分隔：默认全角空格或&emsp;
     * @return array
     */
    static public function tree($data, $title, $fieldPri = 'id', $fieldPid = 'pid',$emp='　')
    {
        if (!is_array($data) || empty($data))
            return array();
        $arr = Data::channelList($data, 0, '', $fieldPri, $fieldPid);
        foreach ($arr as $k => $v) {
            $str = "";
            if ($v['_level'] > 2) {
                for ($i = 1; $i < $v['_level'] - 1; $i++) {
                    // $str .= "&emsp;│";
                    $str .= "{$emp}";
                }
            }
            if ($v['_level'] != 1) {
                $t = $title ? $v[$title] : "";
                if (isset($arr[$k + 1]) && $arr[$k + 1]['_level'] >= $arr[$k]['_level']) {
                    $arr[$k]['_name'] = $str . "{$emp}├─ " . $v['_html'] . $t;
                } else {
                    $arr[$k]['_name'] = $str . "{$emp}└─ " . $v['_html'] . $t;
                }
            } else {
                $arr[$k]['_name'] = $v[$title];
            }
        }
        //设置主键为$fieldPri
        $data = array();
        foreach ($arr as $d) {
            $data[$d[$fieldPri]] = $d;
        }
        return $data;
    }

    /**
     * 获得所有父级栏目
     * @param $data 栏目数据
     * @param $sid 子栏目
     * @param string $fieldPri 唯一键名，如果是表则是表的主键
     * @param string $fieldPid 父ID键名
     * @return array
     */
    static public function parentChannel($data, $sid, $fieldPri = 'id', $fieldPid = 'pid')
    {
        if (empty($data)) {
            return $data;
        } else {
            $arr = array();
            foreach ($data as $v) {
                if ($v[$fieldPri] == $sid) {
                    $arr[] = $v;
                    $_n = self::parentChannel($data, $v[$fieldPid], $fieldPri, $fieldPid);
                    if (!empty($_n)) {
                        $arr = array_merge($arr, $_n);
                    }
                }
            }
            return $arr;
        }
    }

    /**
     * 判断$s_cid是否是$d_cid的子栏目
     * @param $data 栏目数据
     * @param $sid 子栏目id
     * @param $pid 父栏目id
     * @param string $fieldPri 主键
     * @param string $fieldPid 父id字段
     * @return bool
     */
    static function isChild($data, $sid, $pid, $fieldPri = 'id', $fieldPid = 'pid')
    {
        $_data = self::channelList($data, $pid, '', $fieldPri, $fieldPid);
        foreach ($_data as $c) {
            //目标栏目为源栏目的子栏目
            if ($c[$fieldPri] == $sid)
                return true;
        }
        return false;
    }

    /**
     * 检测是不否有子栏目
     * @param $data 栏目数据
     * @param $cid 要判断的栏目cid
     * @param string $fieldPid 父id表字段名
     * @return bool
     */
    static function hasChild($data, $cid, $fieldPid = 'pid')
    {
        foreach ($data as $d) {
            if ($d[$fieldPid] == $cid) return true;
        }
        return false;
    }

    /**
     * 递归实现迪卡尔乘积
     * @param $arr 操作的数组
     * @param array $tmp
     * @return array
     */
    static function descarte($arr, $tmp = array())
    {
        static $n_arr = array();
        foreach (array_shift($arr) as $v) {
            $tmp[] = $v;
            if ($arr) {
                self::descarte($arr, $tmp);
            } else {
                $n_arr[] = $tmp;
            }
            array_pop($tmp);
        }
        return $n_arr;
    }


    /**
     * 无限级分类: 1 => array('id' => 1, 'pid' => 0, 'name' => '江西省'),
     *
     * @param  [type] $items [description]
     * @return [type]        [description]
    */
    static function gen_tree($items) {
        foreach ($items as $item)
            $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
        return isset($items[0]['son']) ? $items[0]['son'] : array();
    }

    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    static function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

}