<?php

namespace utils\Wechat;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use utils\Curl;

class TemplateMessage
{

    /**
     * @var null 微信推送模板消息地址
     */
    private $url = null;

    /**
     * @var null 微信账号openid
     */
    private $openid = null;

    /**
     * @var null 模板消息的模板id
     */
    private $template_id = null;


    /**
     * TemplateMessage constructor.
     * @param $openid           微信账号openid
     * @param $template_id      模板消息的模板id
     */
    public function __construct($openid, $template_id)
    {
        $this->openid      = $openid;
        $this->template_id = $template_id;

        $AccessToken = WechatService::getPublicBlogToken();
        $this->url   = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$AccessToken}";
    }

    /**
     * 发送模板消息
     * @param array $data 模板消息内容
     * @param array $redirect_info 模板消息跳转内容
     * @return bool
     */
    public function send(array $data, array $redirect_info = [])
    {
        /**
         * 组合数据
         */
        $params = $this->buildParams($data, $redirect_info);

        if (empty($params)) {
            return false;
        }

        /**
         * 发送请求(通知微信推送模板消息)
         */
        $cURL = new \utils\cUrl\Curl($this->url);
        $cURL->setMethod('POST');
        $cURL->setParam($params);
        $result = $cURL->Query();

        if (empty($result)) {
            return false;
        } else {
            $result = json_decode($result, true);
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return false;
        }

        return true;
    }

    /**
     * 组装微信服务器推送模板消息时需要的参数
     * @param array $data 模板内容
     * @param array $redirect_info 模板跳转相关信息(跳转到url或某个微信小程序,微信小程序必须和此微信公众号绑定)
     * @return string
     */
    private function buildParams(array $data, array $redirect_info = [])
    {
        $params = [
            'touser'      => $this->openid,
            'template_id' => $this->template_id,
        ];

        if (isset($redirect_info['url']) && $redirect_info['url'] != '' && !empty($redirect_info['url'])) {
            $params['url'] = trim($redirect_info['url']);
        }

        if (isset($redirect_info['miniprogram']) && is_array($redirect_info['miniprogram']) && !empty($redirect_info['miniprogram'])) {

            if (isset($redirect_info['miniprogram']['appid']) && $redirect_info['miniprogram']['appid'] != '') {
                $params['miniprogram']['appid'] = trim($redirect_info['miniprogram']['appid']);
            }

            if (isset($redirect_info['miniprogram']['pagepath']) && $redirect_info['miniprogram']['pagepath'] != '') {
                $params['miniprogram']['pagepath'] = trim($redirect_info['miniprogram']['pagepath']);
            }
        }

        $params['data'] = $data;

        return json_encode($params);
    }


}