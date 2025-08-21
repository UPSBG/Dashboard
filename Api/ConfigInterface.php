<?php
/**
 * ConfigInterface file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage API
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Api;

/**
 * Interface ConfigInterface
 * @api
 */
interface ConfigInterface
{
    /**
     * Save configuration values.
     *
     * @param array $connectionId
     * @return bool
     */
    public function saveConnectionId(array $connectionId): bool;
}
