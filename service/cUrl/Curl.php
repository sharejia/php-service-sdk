<?php
namespace service\cUrl;

use think\db\Query;
use think\Exception;

final class Curl
{
    # this cUrl
    private $cUrl = null;

    # set request URL
    private $request_URL = null;

    # set request method
    private $request_method = 'GET';

    # set param
    private $parameter = [];

    /**
     * Curl constructor.
     * @param $request_URL
     */
    public function __construct($request_URL)
    {
        $this->request_URL = $request_URL;

        //初始化
        $this->cUrl = curl_init();
        //设置抓取的url
        curl_setopt($this->cUrl, CURLOPT_URL, $this->request_URL);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($this->cUrl, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * @param string $method GET|POST
     */
    public function setMethod($method = 'GET')
    {
        $this->request_method = $method;

        if($this->request_method == 'GET') {
            //设置头文件的信息作为数据流输出
            curl_setopt($this->cUrl, CURLOPT_HEADER, 0);
        } else if($this->request_method == 'POST'){
             curl_setopt($this->cUrl, CURLOPT_HEADER, 0);
        } else {
            throw new Exception('敬请期待');
        }
    }

    /**
     * @param array $data
     */
    public function setParam($data = [])
    {
        $this->parameter = $data;
        if($this->request_method == 'GET') {

        } else if($this->request_method == 'POST'){
            curl_setopt($this->cUrl, CURLOPT_POSTFIELDS,$this->parameter);
        }
    }

    /**
     * @return bool|string
     */
    public function Query()
    {
        $check_environment = $this->check_environment();

        if($check_environment === false) return false;

        # 执行命令
        $data = curl_exec($this->cUrl);

        # 关闭URL请求
        curl_close($this->cUrl);

        # 显示获得的数据
        return $data;
    }

    /**
     * @return bool
     */
    private function check_environment()
    {
        if(!isset($this->request_URL) || $this->request_URL == null) {
            return false;
        }
    }








}