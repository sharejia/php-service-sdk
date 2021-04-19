<?php
namespace service\Data;


use service\Random\RandomService;

class DataService
{

    /**
     * 将数组转化换成树状结构
     * @param array $data 数据内容
     * @param bool $virtual_id 是否需要虚拟编号
     * @param int $parent_id 父级数据id值
     * @param string $primary_key 父级数据键名(用于确定父子关系)
     * @param string $children_key 子级数据键名(用于确定父子关系)
     * @param string $children_name 子节点键名称
     * @return array
     */
    public static function array2Tree(array $data = [], bool $virtual_id = false, $parent_id = 0, $primary_key = 'id', $children_key = 'pid', string $children_name = 'children')
    {
        $result = [];

        foreach ($data as $k => $v) {

            if ($virtual_id) $v['virtual_id'] = md5(RandomService::numeric(10));

            if ($v[$children_key] == $parent_id) {
                $v[$children_name] = self::array2Tree($data, $virtual_id, $v[$primary_key], $primary_key, $children_key, $children_name);
                $result[]          = $v;
            }
        }

        return $result;
    }

    /**
     * 移除树形结构中的某个节点及所有子节点
     * @param $data                数据(树形结构)
     * @param $val                 某key对应的值
     * @param string $key 以此key作为移除条件
     * @param string $children 子节点key
     * @return mixed
     */
    public static function removeTreeNode(&$data, $val, $key = 'id', $children = 'children')
    {
        foreach ($data as $k => $v) {
            if (isset($v[$key]) && $v[$key] == $val) {
                unset($data[$k]);
                continue;
            } else if (isset($v[$children]) && !empty($v[$children])) {
                self::removeTreeNode($v[$children], $val, $key, $children);
            }
        }

        return $data;
    }

    /**
     * 将树形结构转化为二维数组 每次只能传入一个根节点 (待优化)
     * @param $v
     * @param string $son_node
     * @return array
     */
    public static function tree2twoArr(&$v, $son_node = 'children')
    {
# 定义空数组
        static $result = [];

        if (isset($v[$son_node]) && !empty($v[$son_node])) {
            $result[] = self::tree2twoArr($v[$son_node], $son_node);
        } else {
            $result[] = $v;
            unset($v);
        }

        return $result;
    }

    /**
     * @description:根据数据
     * @param array $dataArr
     * @param string $keyStr
     * @return array :
     */
    public static function dataGroup(array $dataArr, string $keyStr): array
    {
        $newArr = [];

        foreach ($dataArr as $k => $val) {    //数据根据日期分组
            $newArr[$val[$keyStr]][] = $val;
        }

        return $newArr;
    }

    /**
     * 去除HTML标签
     * @param $str
     * @return string
     */
    public static function deleteHtml($str)
    {
        $str = trim($str); //清除字符串两边的空格
        $str = preg_replace("/\t/", "", $str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
        $str = preg_replace("/\r\n/", "", $str);
        $str = preg_replace("/\r/", "", $str);
        $str = preg_replace("/\n/", "", $str);
        $str = preg_replace("/ /", "", $str);
        $str = preg_replace("/  /", "", $str);  //匹配html中的空格
        return trim($str); //返回字符串
    }

    /**
     * 根据值删除一位数组中的元素
     * @param array $data 数据数组
     * @param $value 值
     * @param bool $resetKey
     */
    public static function deleteEleByValue(array $data, $value, bool $resetKey = false)
    {
        foreach ($data as $k => $v) {
            if ($v == $value) {
                unset($data[$k]);
            }
        }

        if ($resetKey) {
            $data = array_values($data);
        }

        return $data;
    }

    /**
     * 移除树形结构中的空节点
     * @param array $data 数据
     * @param string $child_node_name 节点名称
     * @return array
     */
    public static function removeEmptyNodeFromTree(array &$data, string $child_node_name = 'children')
    {
        foreach ($data as $k => &$v) {
            if (empty($v[$child_node_name])) {
                unset($v[$child_node_name]);
            } else {
                self::removeEmptyNodeFromTree($v[$child_node_name], $child_node_name);
            }
        }

        return $data;
    }












}