<?php

namespace service\Wechat;

use think\Cache;
use think\Config;
use utils\Curl;
use utils\Response;

class WechatService
{

    /**
     * WechatService constructor.
     */
    public function __construct()
    {
    }

    /**
     * 获取微信AccessToken
     * @return bool|mixed
     */
    public static function getAccessToken()
    {
        # 获取微信token
        $wx_access_token = Cache::store('wechat')->get('access_token');

        if (empty($wx_access_token) || $wx_access_token == '' || $wx_access_token == null) {
            # 获取配置参数
            $wechat = \think\Config::get('Wechat.miniApp');
            $appid  = $wechat['appID'];
            $secret = $wechat['key'];

            # 设置微信服务器URL
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";

            $result = \utils\Curl::request($url, 'GET');
            $result = json_decode($result, true);

            if (isset($result['errcode']) && $result['errcode'] != 0) return false;

            $wx_access_token = $result['access_token'];

            Cache::store('wechat')->set('access_token', $result['access_token']);
        }

        return $wx_access_token;
    }

    /**
     * 发送模板消息
     * @param $openid           微信用户openid
     * @param $template_id      模板id
     * @param $temp_data        模板数据
     * @param null $color 字体颜色
     * @param $redirect_little_app  跳转到小程序
     * @param $redirect_url         跳转到url
     */
    public static function sendTemplateMessage($openid, $template_id, array $temp_data, $color = null, array $redirect_little_app = [], $redirect_url = null)
    {
        /**
         * 检查参数
         */
        if (empty($openid) || empty($template_id) || empty($temp_data)) {
            return false;
        }

        /**
         * 获取模板消息实例
         */
        $templateMessObj = new TemplateMessage($openid, $template_id);

        $send = $templateMessObj->send($temp_data, [
            'miniprogram' => $redirect_little_app,
            'url'         => $redirect_url,
        ]);

        if (!$send) return false;

        return true;
    }

    /**
     * 获取微信用户信息
     * @param $code         微信用户code
     * @param string $type 类型 miniApp:微信小程序 public_blog:微信公众号
     * @return bool|mixed
     * @throws \Exception
     */
    public static function getUserInfo($code, $type = 'miniApp')
    {
        if ($type == 'miniApp') {
            # 获取微信小程序信息
            $wechatConfig = Config::get('Wechat.miniApp');
            $appid        = $wechatConfig['appID'];
            $secret       = $wechatConfig['key'];

            $wechat_url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";

            $result = Curl::request($wechat_url, 'GET');
            $result = json_decode(stripslashes($result), true);

            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return false;
            }

            return $result;
        } else if ($type == 'public_blog') {
            /**
             * 此方法code传进来的参数,微信用户在知机维保公众号中的openid
             */

            # 获取微信小程序信息
            $wechatConfig = Config::get('Wechat.public_blog');
            $appid        = $wechatConfig['appid'];
            $secret       = $wechatConfig['appSecret'];

            $AccessToken = WechatService::getPublicBlogToken();

            $wechat_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$AccessToken}&openid={$code}&lang=zh_CN";

            $result = Curl::request($wechat_url, 'POST');

            $result = json_decode($result, true);

            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return false;
            }

            return $result;
        }
    }

    /**
     * 获取微信公众号AccessToken
     * @return mixed
     */
    public static function getPublicBlogToken()
    {
        $token = Cache::store('wechat')->get('public_blog_access_token');

        if (empty($token)) {
            # 获取配置参数
            $wechat = \think\Config::get('Wechat.public_blog');

            # 设置微信服务器URL
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wechat['appid']}&secret={$wechat['appSecret']}";

            $result = \utils\Curl::request($url, 'GET');
            $result = json_decode($result, true);

            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return false;
            }

            Cache::store('wechat')->set('public_blog_access_token', $result['access_token']);

            $token = $result['access_token'];
        }

        return $token;
    }

    /**
     * 解密微信的encryptedData
     * @param $encryptedData    密文
     * @param $iv               加密向量
     * @param $appid            公众号appid
     * @param $session_key      session_key
     * @return bool
     */
    public static function decodeEncryptedData($encryptedData, $iv, $appid, $session_key)
    {
        $data = null;

        $pc      = new WechatBizDataCrypt($appid, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            return $data;
        } else {
            return false;
        }
    }


}