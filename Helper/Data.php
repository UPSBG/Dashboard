<?php
/**
 * Data file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage Helper
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\OauthService;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\ConsumerFactory;
use Magento\Backend\Helper\Data as HelperBackend;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;

/**
 * Class Data
 *
 * Setup Core connection and dashboard connection methods
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * API URL.
     */
    //@codingStandardsIgnoreStart
    public const API_URL = 'https://api.itembase.com/connectivity/instances/'
        . 'c15f3252-849e-4319-afff-1abb4a4f78ec/connect/complete';
    //@codingStandardsIgnoreEnd
    
    /**
     * Integration Name.
     */
    public const ADDON_INSTANCE_ID = 'c15f3252-849e-4319-afff-1abb4a4f78ec';

    /**
     * Integration Name.
     */
    public const INTEGRATION_NAME = 'UPS-Shipping-Dashboard-Integration';
    
    /**
     * Module Name
     */
    public const MODULE_NAME = 'UPSBG_Dashboard';
    
    /**
     * Configuration path.
     */
    public const ACTIVE_CONNECTION_PATH = 'upsbg_dashboard/general/connection_id';
    
    /**
     * Configuration path.
     */
    public const CONSUMER_KEY = 'upsbg_dashboard/general/consumer_key';
    
    /**
     * Configuration path.
     */
    public const CONSUMER_SECRET = 'upsbg_dashboard/general/consumer_secret';
    
    /**
     * Configuration path.
     */
    public const ADMIN_TOKEN = 'upsbg_dashboard/general/admin_token';
    
    /**
     * Configuration path.
     */
    public const CONNECT_STATUS = 'upsbg_dashboard/general/connect_status';

    /**
     * OAuth Token path.
     */
    public const MAGENTO_OAUTH_AS_BEARER = 'oauth/consumer/enable_integration_as_bearer';

    /**
     * The Module list
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * The Integration factory.
     * @var \Magento\Integration\Model\IntegrationFactory
     */
    protected $integrationfactory;

    /**
     * User OAuth service.
     * @var \Magento\Integration\Model\OauthService
     */
    protected $oauthservice;

    /**
     * Magento Authorization service.
     * @var \Magento\Integration\Model\AuthorizationService
     */
    protected $authorizationservice;

    /**
     * Main Token.
     * @var \Magento\Integration\Model\Oauth\Token
     */
    protected $token;

    /**
     * Scope configuration interface.
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var ConfigCollectionFactory
     */
    protected $configCollectionFactory;

    /**
     * Config Writer interface.
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * Cache Type list interface.
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeListInterface;

    /**
     * Cache Pool
     * @var Pool
     */
    protected $cacheFrontendPool;

    /**
     * Model Store manager.
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * App State.
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * Product meta.
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Customer access token.
     * @var consumerFactory
     */
    protected $consumerFactory;

    /**
     * The backend helper.
     * @var HelperBackend
     */
    protected $HelperBackend;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context The context.
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList The module list.
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata The product metadata.
     * @param \Magento\Integration\Model\IntegrationFactory $integrationfactory The integration factory.
     * @param \Magento\Integration\Model\OauthService $oauthservice The OAuth service.
     * @param \Magento\Integration\Model\AuthorizationService $authorizationservice The authorization service.
     * @param \Magento\Integration\Model\Oauth\Token $token The token.
     * @param \Magento\Integration\Model\Oauth\ConsumerFactory $consumerFactory The consumer factory.
     * @param \Magento\Framework\App\Helper\Backend $HelperBackend The backend helper.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface The configuration.
     * @param ConfigCollectionFactory $configCollectionFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface The writer interface.
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeListInterface The type list interface.
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool Cache pool.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager The store manager.
     * @param \Magento\Framework\App\State $state The state.
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata,
        IntegrationFactory $integrationfactory,
        OauthService $oauthservice,
        AuthorizationService $authorizationservice,
        Token $token,
        ConsumerFactory $consumerFactory,
        HelperBackend $HelperBackend,
        ScopeConfigInterface $scopeConfigInterface,
        ConfigCollectionFactory $configCollectionFactory,
        WriterInterface $writerInterface,
        TypeListInterface $typeListInterface,
        Pool $cacheFrontendPool,
        StoreManagerInterface $storeManager,
        State $state
    ) {
        $this->_moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
        $this->integrationfactory = $integrationfactory;
        $this->oauthservice = $oauthservice;
        $this->authorizationservice = $authorizationservice;
        $this->token = $token;
        $this->consumerFactory = $consumerFactory;
        $this->HelperBackend = $HelperBackend;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->writerInterface = $writerInterface;
        $this->typeListInterface = $typeListInterface;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->storeManager = $storeManager;
        $this->state = $state;
        parent::__construct($context);
    }

    /**
     * Get the version of the module.
     *
     * @return string The version of the module.
     */
    public function getModuleVersion()
    {
        return $this->_moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    /**
     * Get Magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Create Integration
     *
     * @return string
     */
    public function createIntegration()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        $storeId =  $this->_request->getParam('store');
        $websiteId = $this->_request->getParam('website');

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

        if ($scopeId == 0) {
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeCode = 0;
        } elseif ($scopeId > 0) {
            $scope = ScopeInterface::SCOPE_WEBSITES;
            $scopeCode = $scopeId;
        } else {
            $scope = ScopeInterface::SCOPE_STORES;
            $scopeCode = $scopeId;
        }

        $name = self::INTEGRATION_NAME;
        $name = $name.''.$scopeId;
        $email = '';
        $endpoint = self::API_URL;

        $ConfigconsumerKey = self::CONSUMER_KEY;
        $ConfigconsumerSecret = self::CONSUMER_SECRET;
        $ConfigAdminToken = self::ADMIN_TOKEN;
        $statusConfig = self::CONNECT_STATUS;

        $consumer_key = '';
        $consumer_secret = '';
        $integrationExists = $this->integrationfactory->create()->load($name, 'name')->getData();

        $access_token = "";

        if (empty($integrationExists)) {
            $integrationData = [
                'name' => $name,
                'email' => $email,
                'status' => '1',
                'endpoint' => $endpoint,
                'setup_type' => '0'
            ];
            try {
                // Create Integration
                $integrationFactory = $this->integrationfactory->create();
                $integration = $integrationFactory->setData($integrationData);
                $integration->save();
                $integrationId = $integration->getId();
                $consumerName = 'Integration' . $integrationId;

                // Create consumer
                $oauthService = $this->oauthservice;
                $consumer = $oauthService->createConsumer(['name' => $consumerName]);
                $consumerId = $consumer->getId();
                $integration->setConsumerId($consumer->getId());
                $integration->save();

                // Grant permission
                $authrizeService = $this->authorizationservice;
                $authrizeService->grantAllPermissions($integrationId);

                // Activate and Authorize
                $token = $this->token;
                $uri = $token->createVerifierToken($consumerId);
                $token->setType('access');
                $token->save();
                $access_token = $token['token'];

                $this->saveConfigValue($ConfigAdminToken, $access_token, $scope, $scopeCode);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        $this->enableTokeAsBearerToken();

        // get access token
        $value_conf_ck = $this->scopeConfigInterface->getValue($ConfigconsumerKey, $scope, $scopeCode);
        
        $available_config_active =  $this->isScopedConfigValueSet($statusConfig, $scope, $scopeCode);

        $is_connected = $this->scopeConfigInterface->getValue($statusConfig, $scope, $scopeCode);

        if ($available_config_active) {
            if ($is_connected || $is_connected == '1') {
                $is_connected_status = '1';
            } else {
                $is_connected_status = '0';
            }
        } else {
            $is_connected_status = '0';
        }

        $dataCollection = $this->integrationfactory->create()->getCollection()->addFieldToFilter('name', $name);
        $dataFirstItem = $dataCollection->getFirstItem();
        $consumerId = $dataFirstItem->getData('consumer_id');
        $tokenData = $this->token->getCollection()->addFieldToFilter('consumer_id', $consumerId);
        $tokenDataFirstItem = $tokenData->getFirstItem();
        $access_token = $tokenDataFirstItem->getData('token');

        $consumer = $this->consumerFactory->create()->load($consumerId);
        $consumer_key = $consumer['key'];
        $consumer_secret = $consumer['secret'];

        $mainaccess_token = $this->scopeConfigInterface->getValue($ConfigAdminToken, $scope, $scopeCode);
    
        if ($this->scopeConfigInterface->getValue($ConfigAdminToken, $scope, $scopeCode) == "") {
            $mainaccess_token = $access_token;
        }

        $data = [
            'consumer_key'=>$consumer_key,
            'consumer_secret'=>$consumer_secret,
            'access_token'=> $access_token,
            'activate' => $this->getActivate(),
            'integration_name' => $name,
            'store_id' => $storeId,
            'conn_status' => $is_connected_status
        ];
        $this->saveConfigValue($ConfigconsumerKey, $consumer_key, $scope, $scopeCode);
        //@codingStandardsIgnoreStart
        $this->saveConfigValue($ConfigconsumerSecret, $consumer_secret, $scope, $scopeCode);
        //@codingStandardsIgnoreEnd
        $this->flushAllCache();
        return $data;
    }

    /**
     * Save a configuration value.
     *
     * @param string $path The path of the configuration.
     * @param mixed $value The value to be saved.
     * @param string $storeScope The store scope.
     * @param int|string|null $storeId The store ID.
     * @return void
     */
    public function saveConfigValue($path, $value, $storeScope, $storeId)
    {
        $this->writerInterface->save($path, $value, $storeScope, $storeId);
    }

    /**
     * Get a configuration value.
     *
     * @param string $path The path of the configuration.
     * @param string|null $scopeType The scope type.
     * @param string|null $scopeCode The scope code.
     * @return mixed The value of the configuration.
     */
    public function getConfigValue($path, $scopeType = null, $scopeCode = null)
    {
        return $this->scopeConfigInterface->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Get the admin URL.
     *
     * @return string The admin URL.
     */
    public function getAdminUrl()
    {
        return $this->HelperBackend->getHomePageUrl();
    }

    /**
     * Get the activate value.
     *
     * @return mixed The activate value.
     */
    public function getActivate()
    {

        $storeId = $this->_request->getParam('store');
        
        $websiteId = $this->_request->getParam('website');

        if ($storeId) {
            // Store-level config
            return $this->scopeConfigInterface->getValue(
                self::ACTIVE_CONNECTION_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $storeId
            );
        }

        if ($websiteId) {
            // Website-level config
            return $this->scopeConfigInterface->getValue(
                self::ACTIVE_CONNECTION_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
                $websiteId
            );
        }

        // Default (global) config
        return $this->scopeConfigInterface->getValue(
            self::ACTIVE_CONNECTION_PATH,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * Remove an integration.
     *
     * @param string $integration_name The name of the integration.
     * @return void
     */
    public function removeIntegration($integration_name)
    {

        $scopeddata = $this->getScope();

        $scope = $scopeddata['scope'];
        $scopeCode = $scopeddata['scopeCode'];
        $scopeId = $scopeddata['scopeId'];
        $websiteId = $scopeddata['websiteId'];
        $storeUrl = $scopeddata['storeUrl'];

        $ConfigconsumerKey = self::CONSUMER_KEY;
        $ConfigconsumerSecret = self::CONSUMER_SECRET;
        $ConfigAdminToken = self::ADMIN_TOKEN;

        //@codingStandardsIgnoreStart
        $this->integrationfactory->create()->getCollection()
        ->addFieldToFilter('name', $integration_name)
        ->setPageSize(1)->getFirstItem()->delete();
        //@codingStandardsIgnoreEnd

        $this->saveConfigValue($ConfigconsumerKey, '', $scope, $scopeId);
        $this->saveConfigValue($ConfigconsumerSecret, '', $scope, $scopeId);
        $this->saveConfigValue($ConfigAdminToken, '', $scope, $scopeId);
        
        $this->flushAllCache();
    }

    /**
     * Get the token.
     *
     * @return string The token.
     */
    public function getToken()
    {

        $scopeddata = $this->getScope();

        $scope = $scopeddata['scope'];
        $scopeCode = $scopeddata['scopeCode'];
        $scopeId = $scopeddata['scopeId'];
        $websiteId = $scopeddata['websiteId'];
        $storeUrl = $scopeddata['storeUrl'];

        $ConfigAdminToken = self::ADMIN_TOKEN;

        return $this->scopeConfigInterface->getValue($ConfigAdminToken, $scope, $scopeId);
    }

    /**
     * Enable access token as bearer token standalone.
     *
     * @return string The token.
     */
    public function enableTokeAsBearerToken()
    {
        $scopeddata = $this->getScope();

        $scope = $scopeddata['scope'];
        $scopeCode = $scopeddata['scopeCode'];
        $scopeId = $scopeddata['scopeId'];
        $websiteId = $scopeddata['websiteId'];
        $storeUrl = $scopeddata['storeUrl'];

        $accessTokenAsBearerPath = self::MAGENTO_OAUTH_AS_BEARER;

        $isTokenEnable = $this->scopeConfigInterface->getValue($accessTokenAsBearerPath, $scope, $scopeId);

        if ($isTokenEnable != "1") {
            $this->writerInterface->save($accessTokenAsBearerPath, '1');
            $this->flushAllCache();
        }
    }
    
    /**
     * Retrieve the store manager instance.
     *
     * @return StoreManagerInterface The store manager instance.
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * Get Scope Information
     *
     * @return array
     */
    public function getScope()
    {
        $storeId = $this->_request->getParam('store');
        $websiteId = $this->_request->getParam('website');

        $scope = '';
        $scopeId = 0;
        $scopeCode = '';
        $websiteIdFinal = null;
        $storeUrl = '';

        if ($storeId) {
            // Store View scope
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $scopeId = $storeId;
            $scopeCode = $this->storeManager->getStore($storeId)->getCode();
            $websiteIdFinal = $this->storeManager->getStore($storeId)->getWebsiteId();
            $storeUrl = $this->storeManager->getStore($storeId)->getBaseUrl();

        } elseif ($websiteId) {
            // Website scope
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
            $scopeId = $websiteId;
            $scopeCode = $this->storeManager->getWebsite($websiteId)->getCode();
            $websiteIdFinal = $websiteId;
            $defaultStore = $this->storeManager->getWebsite($websiteId)->getDefaultStore();
            $storeUrl = $defaultStore ? $defaultStore->getBaseUrl() : '';

        } else {
            // Default (global) scope
            $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeId = 0;
            $scopeCode = 'default';
            $defaultStore = $this->storeManager->getDefaultStoreView();
            $websiteIdFinal = $defaultStore ? $defaultStore->getWebsiteId() : null;
            $storeUrl = $defaultStore ? $defaultStore->getBaseUrl() : '';
        }

        return [
            'scope'     => $scope,
            'scopeCode' => $scopeCode,
            'scopeId'   => $scopeId,
            'websiteId' => $websiteIdFinal,
            'storeUrl'  => $storeUrl,
        ];
    }

    /**
     * Get Scope Information
     *
     * @return string
     */
    public function flushAllCache()
    {
        $types = $this->typeListInterface->getTypes();

        // Clean cache types (like config, layout, etc.)
        foreach (array_keys($types) as $type) {
            $this->typeListInterface->cleanType($type);
        }

        // Clean full-page and other frontend pools
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }

        return true;
    }

    /**
     * Check if a config value is explicitly set for a specific scope and ID.
     *
     * @param string $path    Configuration path
     * @param string $scope   e.g., 'default', 'websites', 'stores'
     * @param int    $scopeId Scope ID
     * @return bool
     */
    public function isScopedConfigValueSet(string $path, string $scope, int $scopeId): bool
    {
        $collection = $this->configCollectionFactory->create();
        $collection->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('scope_id', $scopeId);

        return $collection->getSize() > 0;
    }
}
