<?php
/**
 * Created by Skynix Team.
 * Date: 31.10.19
 * Time: 21:30
 */
namespace SkynixLlc\CloudflareSXAdapter\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use SkynixLlc\CloudflareSXAdapter\Helper\Data;
use SkynixLlc\CloudflareSXAdapter\Helper\Api;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class Purge extends \Magento\Backend\Controller\Adminhtml\Cache implements HttpGetActionInterface
{
    /**
     * @var Data
     */
    private $data;

    /**
     * @var Api
     */
    private $api;

    /**
     * Purge constructor.
     * @param Action\Context $context
     * @param TypeListInterface $cacheTypeList
     * @param StateInterface $cacheState
     * @param Pool $cacheFrontendPool
     * @param PageFactory $resultPageFactory
     * @param Data $data
     * @param Api $api
     */
    public function __construct(
        Action\Context $context,
        TypeListInterface $cacheTypeList,
        StateInterface $cacheState,
        Pool $cacheFrontendPool,
        PageFactory $resultPageFactory,
        Data $data,
        Api $api
    ) {
        parent::__construct(
            $context,
            $cacheTypeList,
            $cacheState,
            $cacheFrontendPool,
            $resultPageFactory
        );
        $this->data = $data;
        $this->api = $api;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $message = null;
        try {
            if ($this->data->getEnable()) {

                $this->api->getAccount();
                $zones = $this->api->getZones();
                $flush = $this->api->purgeCache($zones);

                if ($flush) {
                    $this->messageManager->addSuccessMessage(__("The Cloudflare cache has been cleaned."));

                }

            } else {

                throw new \Exception('Module not enable!!!');
            }

        } catch (\Exception $e) {

            $this->messageManager->addExceptionMessage($e);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('adminhtml/cache/index');
    }
}
