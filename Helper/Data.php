<?php
/**
 * Created by Skynix Team.
 * Date: 29.10.19
 * Time: 15:38
 */
namespace Skynix\CloudflareSXAdapter\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BASE_URL_CLOUDFLARE = "https://api.cloudflare.com/client/v4/";
    const TOKEN   = 'sx_cloudflare_setting/general/token';
    const STATUS  = 'sx_cloudflare_setting/general/module_enable';

    /**
     * Return module status
     *
     * @return mixed
     */
    public function getEnable()
    {
        return $this->scopeConfig->getValue(
            self::STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->scopeConfig->getValue(
            self::TOKEN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     *  Get CLOUDFLARE API url
     * @param $api
     * @return string
     */
    public function getApiUrl($api)
    {
        return self::BASE_URL_CLOUDFLARE . $api;
    }
}
