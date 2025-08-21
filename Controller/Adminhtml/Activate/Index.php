<?php
/**
 * Index file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage Controller
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Controller\Adminhtml\Activate;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use UPSBG\Dashboard\Helper\Data;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Class Index
 *
 * The controller index used for the connect route page.
 *
 */
class Index extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     * @param ConfigInterface $config
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        RequestInterface $request,
        ConfigInterface $config,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->config = $config;
        $this->helperData = $helperData;
    }

    /**
     * Main function for integration
     */
    public function execute()
    {
        try {

            $integrationName = $this->request->getParam('integration_name');
            $scopeId = $this->request->getParam('store_id');
            $integrationType = $this->request->getParam('integration_type');
            $data = [];

            $storeId = $this->request->getParam('store');
            $websiteId = $this->request->getParam('website');

            // Determine scope and scope code properly
            if ($storeId) {
                $scope = ScopeInterface::SCOPE_STORES;
                $scopeCode = $storeId;
            } elseif ($websiteId) {
                $scope = ScopeInterface::SCOPE_WEBSITES;
                $scopeCode = $websiteId;
            } else {
                $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
                $scopeCode = 0;
            }

            if ($integrationType == 'save') {

                $connectionStatus = Data::CONNECT_STATUS;
                $storeScope = ScopeInterface::SCOPE_STORES;

                $this->helperData->saveConfigValue($connectionStatus, '1', $scope, $scopeCode);

                $data = [
                    'status' => true,
                    'type' => 'Create Integration',
                    'data' => [$connectionStatus => 1],
                    'error' => false
                ];
            }

            if ($integrationType == 'remove') {
                
                $this->helperData->removeIntegration($integrationName);
                $connectionStatus = Data::CONNECT_STATUS;
                $storeScope = ScopeInterface::SCOPE_STORES;

                $this->helperData->saveConfigValue($connectionStatus, '0', $scope, $scopeCode);

                $data = [
                    'status' => true,
                    'type' => 'Remove Integration',
                    'error' => false
                ];

            }

            if ($integrationType == 'fetchtoken') {
                $data = [
                    'status' => true,
                    'data' => $this->helperData->getToken(),
                    'error' => true
                ];
            }

        } catch (\Exception $e) {

            $data = [
                'status' => false,
                'data' => $e->getMessage(),
                'error' => true
            ];

        }

        $result = $this->resultJsonFactory->create();

        return $result->setData($data);
    }
}
