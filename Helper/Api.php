<?php
/**
 * Created by Skynix Team.
 * Date: 31.10.19
 * Time: 18:14
 */
namespace Skynix\CloudflareSXAdapter\Helper;

use Magento\Framework\HTTP\Client\Curl;

class Api
{
    /**
     * @var Data
     */
    private $data;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * Api constructor.
     * @param Data $data
     * @param Curl $curl
     */
    public function __construct(
        Data $data,
        Curl $curl
    ) {
        $this->data = $data;
        $this->curl = $curl;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getCurlHeaders()
    {
        if (!$this->data->getEmail() || !$this->data->getApiKey()) {

            throw new \Exception('Invalid Email or Api-Key');
        }
        return [
            'Content-Type' => 'application/json',
            'X-Auth-Email' => $this->data->getEmail(),
            'X-Auth-Key' => $this->data->getApiKey()
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAccounts()
    {
        try {
            $this->curl->setHeaders(
                $this->getCurlHeaders()
            );
            $this->curl->get($this->data->getApiUrl('accounts'));
            $resultAccounts = json_decode($this->curl->getBody());

            if (!$resultAccounts->success) {
                throw new \Exception(implode(' ', $resultAccounts->messages));
            }

            return $this->getAccountsData($resultAccounts);

        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $resultAccounts
     * @return array
     * @throws \Exception
     */
    private function getAccountsData($resultAccounts)
    {
        $accounts = $resultAccounts->result;
        if (empty($accounts)) {

            throw new \Exception('There is no any account');
        }

        $dataAccounts = [];
        foreach ($accounts as $account) {

            $dataAccounts[] = [
                'account.id' => $account->id,
                'account.name' => $account->name
            ];
        }
        return $dataAccounts;
    }

    /**
     * @param array $accountsData
     * @return array
     * @throws \Exception
     */
    public function getZones()
    {
        try {
            $this->curl->setHeaders(
                $this->getCurlHeaders()
            );
            $this->curl->get($this->data->getApiUrl('zones'));
            $result = json_decode($this->curl->getBody());

            if (!$result->success) {
                throw new \Exception(implode(' ', $result->messages));
            }
            $zonesIds = $this->getZonesIds($result);
            return $zonesIds;

        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $zonesData
     * @return array
     * @throws \Exception
     */
    private function getZonesIds($zonesData)
    {
        $resultZones = $zonesData->result;
        if (empty($resultZones)) {

            throw new \Exception('There is no any zones');
        }
        $zonesIds = [];
        foreach ($resultZones as $zone) {
            $zonesIds[] = $zone->id;
        }

        return $zonesIds;
    }

    /**
     * @param array $zones
     * @return bool
     * @throws \Exception
     */
    public function purgeCache(array $zones)
    {
        foreach ($zones as $zone) {
            $this->curl->setHeaders(

                $this->getCurlHeaders()
            );
            $this->curl->post(
                $this->data->getApiUrl("zones/$zone/purge_cache"),
                json_encode(['purge_everything' => true])
            );

            $result = json_decode($this->curl->getBody());

            if (!$result->success) {
                throw new \Exception(implode(' ', $result->messages));
            }
        }
        return true;
    }
}
