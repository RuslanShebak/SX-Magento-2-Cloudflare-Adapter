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
    const BASE_URL_CLOUDFLARE = 'https://api.cloudflare.com/client/v4/';
    const STATUS    = 'sx_cloudflare_setting/general/module_enable';
    const API_KEY   = 'sx_cloudflare_setting/general/api_key';
    const EMAIL     = 'sx_cloudflare_setting/general/email';

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
     * Return api key
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            self::API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return email
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->scopeConfig->getValue(
            self::EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get CLOUDFLARE API url
     * @param null $api
     * @return string
     */
    public function getApiUrl($api = null)
    {
        return self::BASE_URL_CLOUDFLARE . $api;
    }
}
