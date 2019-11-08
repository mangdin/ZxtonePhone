<?php

namespace mangdin\ZxtonePhone;


/**
 * 获取授权cookie
 * Class GetCookStore
 * @package mangdin\ZxtonePhone
 */
class GetCookStore
{

    /**
     * @var string
     */
    private $CookStore;

    /**
     * @var float
     */
    private $expiredAt;

    /**
     * @param string $CookStore 获取的CookStore
     * @param float $expiredAt 具体过期时间，精确到毫秒
     */
    public function __construct($CookStore, $expiredAt)
    {
        $this->CookStore = $CookStore;
        $this->expiredAt = $expiredAt;
    }

    /**
     * 获取的CookStore
     *
     * @return string
     */
    public function getCookStore()
    {
        return $this->CookStore;
    }

    /**
     * 具体过期时间，精确到毫秒
     *
     * @return int
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * 是否有效，过期就是无效
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->expiredAt > time();
    }

}