<?php
namespace mzclass\library;

/**
 * 数组相关处理
 *
 * @Author Mirze
 */
class Arr
{
    // 转换数组列表某字段值为KEY值数组
    function swap_array_key($arr, $field='') {
        if(empty($arr) || !is_array($arr) || ($field == '')) return $arr;

        $result = array();
        foreach ($arr as $k => $v) {
            $key = isset($v[$field]) ? $v[$field] : $k;
            $result[$key] = $v;
        }
        return $result;
    }

    // 单列表数据转换二级字段KEY数组集
    function swap_array_keys($arr, $field1='', $field2='') {
        if(empty($arr) || !is_array($arr) || ($field1 == '') || ($field2 == '')) return $arr;

        $result = array();
        foreach ($arr as $k => $v) {
            if(! isset($v[$field1])) continue;
            if(isset($v[$field2])) {
                $result[$v[$field1]][$v[$field2]] = $v;
            } else {
                $result[$v[$field1]][] = $v;
            }
        }
        return $result;
    }

    // 转换数组列表某字段值为KEY值数组
    function swap_array_kv($arr, $keyField='', $valField='') {
        if(empty($arr) || !is_array($arr) || ($keyField == '') || ($valField == '')) return $arr;

        $result = array();
        foreach ($arr as $k => $v) {
            $key = isset($v[$keyField]) ? $v[$keyField] : $k;
            $value = isset($v[$valField]) ? $v[$valField] : $v;
            $result[$key] = $value;
        }
        return $result;
    }

    // 转换二维数组列表某字段值为KEY值数组
    function swap_key_marray($arr, $field='') {
        if(empty($arr) || !is_array($arr) || ($field == '')) return $arr;

        $result = array();
        foreach ($arr as $k => $v) {
            $key = isset($v[$field]) ? $v[$field] : $k;
            $result[$key][] = $v;
        }
        return $result;
    }

    //对象转数组,使用get_object_vars返回对象属性组成的数组
    function object_to_array($obj){
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if(is_array($arr)){
            return array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }

    //数组转对象
    function array_to_object($arr){
        if(is_array($arr)){
            return (object) array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }

    /**
     * 多维数组按某个字段值排序
     * @param  array $multi_array  多维数组
     * @param  string $sort_key    排序字段
     * @param  [type] $sort        排序方式：SORT_DESC/SORT_ASC
     * @return [type]              [description]
     */
    function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){
        if(empty($multi_array) || !is_array($multi_array)) {
            return $multi_array;
        }
        foreach ($multi_array as $row_array){
            if(is_array($row_array)){
                $key_array[] = $row_array[$sort_key];
            }else{
                return $multi_array;
            }
        }
        array_multisort($key_array,$sort,$multi_array);
        return $multi_array;
    }
    
    /**
     * 二维数组排序 按照指定的key 对数组进行自然排序
     *
     * @param [type] $arr 将要排序的数组
     * @param [type] $keys 指定排序的key
     * @param string $type 排序类型 asc | desc
     * @return void
     */
    function sort_key($arr, $keys, $type = 'asc')
    {
        $keysvalue = $new_array = array();

        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }

        $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);

        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    // 二维数据按某字段求和
    function sum_key($arr, $key)
    {
        if(empty($arr)) return 0;

        $total = 0;
        foreach($arr AS $k => $v) {
            if(!isset($v[$key])) break;

            $total += $v[$key];
        }
        return $total;
    }

}