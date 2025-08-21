<?php
/**
 * StoreUrl file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage Block
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Block\Adminhtml\System\Config\Form\Field;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Button
 *
 * Add core connection button stucture
 *
 * The class file define the connection button links
 *
 */
class StoreUrl extends Field
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Render text field element with store URL
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // Get the base URL of the default store
        $storeId =  $this->getRequest()->getParam('store');
        $websiteId = $this->getRequest()->getParam('website');

        // Determine scope and scope ID dynamically

        if ($storeId) {
            
            // Store View scope
            $scope = ScopeInterface::SCOPE_STORES;
            $scopeId = $storeId;
            $storeUrl = $this->storeManager->getStore($storeId)->getBaseUrl();

        } elseif ($websiteId) {

            // Website scope: Get default store of website
            $scope = ScopeInterface::SCOPE_WEBSITES;
            $scopeId = $websiteId;
            $website = $this->storeManager->getWebsite($websiteId);
            $defaultStore = $website->getDefaultStore();
            $storeUrl = $defaultStore ? $defaultStore->getBaseUrl() : '';

        } else {

            // Global scope: Use default store (usually store ID 0 is not valid)
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeId = 0;
            $defaultStore = $this->storeManager->getDefaultStoreView();
            $storeUrl = $defaultStore ? $defaultStore->getBaseUrl() : '';
        }

        // Set the value of the element to the store URL
        $element->setValue($storeUrl);

        return parent::_getElementHtml($element);
    }
}
