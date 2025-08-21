<?php
/**
 * HiddenField file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage Block
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use UPSBG\Dashboard\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class HiddenField
 * This class manage hidden data
 */
class HiddenField extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/hiddenfield.phtml';

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor.
     *
     * @param Data $helperData The helper data.
     * @param StoreManagerInterface $storeManager
     * @param Context $context The context.
     * @param array $data Additional data.
     */
    public function __construct(
        Data $helperData,
        StoreManagerInterface $storeManager,
        Context $context,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Unset scope
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {

        $integrationData = $this->helperData->createIntegration();

        $consumer_key = $integrationData['consumer_key'];
        $consumer_secret = $integrationData['consumer_secret'];
        $access_token = $integrationData['access_token'];
        $activate = $integrationData['activate'];
        $integration_name = $integrationData['integration_name'];
        $store_id = $integrationData['store_id'];
        $conn_status = $integrationData['conn_status'];
        $originalData = $element->getOriginalData();

        $magento_url = $this->helperData->getStoreManager()->getStore($store_id)->getBaseUrl();

        $storeId =  $this->getRequest()->getParam('store');
        $websiteId = $this->getRequest()->getParam('website');

        // Determine scope and scope ID dynamically

        $scope = ScopeInterface::SCOPE_WEBSITES;
        $scopeId = $websiteId;
        
        if ($storeId) {
            
            // Store View scope
            $scope = ScopeInterface::SCOPE_STORES;
            $scopeId = $storeId;
            $magento_url = $this->storeManager->getStore($storeId)->getBaseUrl();
            $websiteId = 0;

        } elseif ($websiteId) {

            // Website scope: Get default store of website
            $scope = ScopeInterface::SCOPE_WEBSITES;
            $scopeId = $websiteId;
            $website = $this->storeManager->getWebsite($websiteId);
            $defaultStore = $website->getDefaultStore();
            $allStores = $this->storeManager->getWebsite($websiteId)->getStores();
            $scopeId = '';
            foreach ($allStores as $key => $value) {
                $scopeId .= $key.',';
            }
            $scopeId = rtrim($scopeId, ',');
            $magento_url = $defaultStore ? $defaultStore->getBaseUrl() : '';

        } else {
            // Global scope: Use default store (usually store ID 0 is not valid)
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeId = 0;
            $websiteId = 0;
            $defaultStore = $this->storeManager->getDefaultStoreView();
            $magento_url = $defaultStore ? $defaultStore->getBaseUrl() : '';

        }

        $this->addData([
            'magento_url' => $magento_url,
            'admin_url' => $this->helperData->getAdminUrl(),
            'magento_version'   => $this->helperData->getMagentoVersion(),
            'module_name'   => '',
            'module_version'   => $this->helperData->getModuleVersion(),
            'consumer_key'   => $consumer_key,
            'consumer_secret'   => $consumer_secret,
            'token'=> $access_token,
            'is_activate'=> $activate,
            'upsapiurl'   => Data::API_URL,
            'integration_name'   => $integration_name,
            'store_id'   => $scopeId,
            'website_id'   => $websiteId,
            'conn_status' => $conn_status

        ]);

        return $this->_toHtml();
    }
}
