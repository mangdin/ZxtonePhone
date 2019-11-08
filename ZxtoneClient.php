<?php

namespace mangdin\ZxtonePhone;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use think\facade\Cache;

/**
 * 客户端类，封装了GPS紫光学生机开放平台Api的操作
 *
 * 具体的接口规则可参考官方文档：http://gps.zxtone.com/server/PPService.asmx
 *
 * @package mangdin\ZxtoneClient
 */
class ZxtoneClient
{
    /**
     * 用户名
     * @var string
     */
    private $LoginName;

    /**
     * 密码
     * @var string
     */
    private $PassWord;

    /**
     * key密钥
     * @var string
     */
    private $Key;

    /**
     * Cookie缓存名称前缀
     */
    const ACCESS_TOKEN_CACHE_PREFIX = 'GPSCookie_';

    /**
     * 接口入口网址
     */
    const API_ENDPOINT = 'http://gps.zxtone.com/server/PPService.asmx';

    /**
     * ZxtoneClient constructor.
     * @param $LoginName 用户名
     * @param $PassWord 密码
     * @param $Key  密钥key
     * @throws \Exception
     */
    public function __construct($LoginName, $PassWord, $Key)
    {
        $LoginName = trim($LoginName);
        $PassWord = trim($PassWord);
        $Key = trim($Key);

        if (empty($LoginName)) {
            throw new \Exception('login name is empty');
        }

        if (empty($PassWord)) {
            throw new \Exception('login password is empty');
        }

        if (empty($Key)) {
            throw new \Exception('login key is empty');
        }

        $this->LoginName = $LoginName;
        $this->PassWord = $PassWord;
        $this->Key = $Key;
    }

    /**
     * 获取getCookieStore
     *
     * @return string
     */
    public function getCookieStore()
    {
        //从缓存去读取
        $cacheKey = $this->getCookieStoreCacheKey($this->LoginName);
        /** @var GetCookStore $getCookStore */
        $getCookStore = Cache::get($cacheKey);
        if ($getCookStore && $getCookStore->isAvailable()) {
            return $getCookStore->getCookStore();
        }
        $params = [
            'LoginName' => $this->LoginName,
            'PassWord' => $this->PassWord,
            'key' => $this->Key,
        ];

        $result = $this->post(self::API_ENDPOINT . '/Login', $params, false);

        $getCookStore = new GetCookStore($result['d'], time() + 3600);

        //缓存永久存储，lifetime设为0
        Cache::set($cacheKey,$getCookStore,3600);

        return $getCookStore->getCookStore();
    }

    /**
     *  获取设备实时信息详情
     * @param $SerialNumber 设备号
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\Exception
     */
    public function getDeviceDetial($SerialNumber){
        $params = [
            'SerialNumber' => $SerialNumber,
        ];
        return $this->post(self::API_ENDPOINT . '/GetDeviceDetial', $params);
    }

    /**
     * 修改设备名称
     * @param $SerialNumber 设备号
     * @param $DeviceName 设备新名称
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\Exception
     */
    public function updateDeviceName($SerialNumber,$DeviceName){
        $params = [
            'Serialnumber' => $SerialNumber,
            'DeviceName' => $DeviceName,
        ];
        return $this->post(self::API_ENDPOINT . '/UpdateDeviceName', $params);
    }

    public function getPhoneConfig($SerialNumber)
    {
        $params = [
            'Serialnumber' => $SerialNumber,
        ];
        return $this->post(self::API_ENDPOINT . '/SHX007GetPhoneconfig', $params);
    }

    /**
     *  修改设备亲情号码
     * @param $SerialNumber 设备号
     * @param $ButtonNum    按键 1.SOS 2.按键1 3.按键2 4.按键3
     * @param $PhoneNumber  手机号码
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\Exception
     */
    public function setPhoneNumber($SerialNumber,$ButtonNum,$PhoneNumber){
        $params = [
            'Serialnumber' => $SerialNumber,
            'ButtonNum' => $ButtonNum,
            'phonenumber' => $PhoneNumber
        ];
        return $this->post(self::API_ENDPOINT . '/SHX007SetAuthorizedPhoneNumber', $params);
    }


    /**
     * 获取Cookie保存在缓存中的键值
     *
     * @param string $LoginName
     * @return string
     */
    private function getCookieStoreCacheKey($LoginName)
    {
        return self::ACCESS_TOKEN_CACHE_PREFIX . $LoginName;
    }

    /**
     * 萤石云接口post请求
     *
     * @param $url
     * @param array $params
     * @param bool $auth
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\Exception
     */
    private function post($url, $params = [], $auth = true)
    {
        if ($auth) {
            $cookieJar = CookieJar::fromArray([
                'ASP.NET_SessionId' => $this->getCookieStore(),
            ], 'gps.zxtone.com');
            $client = new Client(['cookies' => $cookieJar, 'headers' => ['content-type' => 'application/json' ]]);
            $response = $client ->post( $url,['json'=>$params]);
            $result = json_decode($response->getBody(), true);
            if ($result['d']){
                return $result;
            }else{
                throw new \Exception('请求结果失败');
            }
        }else{
            $jar = new CookieJar();
            $client = new Client(['cookies' => $jar, 'headers' => ['content-type' => 'application/json' ]]);
            $response = $client ->post( $url,['json'=>$params]);
            $cookie = $jar->getCookieByName('ASP.NET_SessionId')->toArray();
            $result = json_decode($response->getBody(), true);
            if ($result['d']){
                return ['d'=>$cookie['Value']];
            }else{
                throw new \Exception('登陆失败');
            }
        }
    }

}