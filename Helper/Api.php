<?php
/**
 * Created by Skynix Team.
 * Date: 31.10.19
 * Time: 18:14
 */
namespace SkynixLlc\CloudflareSXAdapter\Helper;

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
        if (!$this->data->getToken()) {

            throw new \Exception('Invalid Token');
        }
        return [
            'Authorization' => 'Bearer ' . $this->data->getToken(),
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAccount()
    {
        try {
            $this->curl->setHeaders(
                $this->getCurlHeaders()
            );
            $this->curl->get($this->data->getApiUrl('user/tokens/verify'));
            $resultAccount = json_decode($this->curl->getBody());

            if (!$resultAccount->success) {
                throw new \Exception(implode(' ', $resultAccount->messages));
            }

            return $this->getAccountData($resultAccount);

        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $resultAccount
     * @return array
     * @throws \Exception
     */
    private function getAccountData($resultAccount)
    {
        $account = $resultAccount->result;
        if (empty($account)) {

            throw new \Exception('There is no any account');
        }

        $dataAccount[] = [
            'account.id' => $account->id,
            'account.status' => $account->status
        ];
        return $dataAccount;
    }

    /**
     * @param array $accountData
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
