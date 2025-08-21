<?php
/**
 * Config file
 *
 * @category   UPS
 * @package    UPS_Dashboard
 * @subpackage Model
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPS eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Model;

use UPSBG\Dashboard\Api\ConfigInterface;
use UPSBG\Dashboard\Helper\Data;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class Config
 *
 * This class represents the configuration settings
 */
class Config implements ConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        TypeListInterface $cacheTypeList
    ) {
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Save configuration values.
     *
     * @param array $connectionId
     * @return bool
     */
    public function saveConnectionId(array $connectionId): bool
    {
        try {

            // Save the connection ID
            $this->configWriter->save(
                Data::ACTIVE_CONNECTION_PATH,
                $connectionId,
                $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $scopeId = 0
            );

            // Clean config cache
            // $this->cacheTypeList->cleanType('config');

            // Prepare success response
            $response = [
                'status' => true,
                'data' => [Data::ACTIVE_CONNECTION_PATH => $connectionId],
                'error' => false
            ];

        } catch (\Exception $e) {
            
            // Prepare error response
            $response = ['success' => false, 'message' => $e->getMessage()];

        }
        
        // Encode response to JSON
        return json_encode($response);
    }
}
