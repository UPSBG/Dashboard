<?php
namespace UPSBG\Dashboard\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use UPSBG\Dashboard\Model\Config;

class ConfigTest extends TestCase
{

    /**
     * @var Config
     */
    private $config;

    protected function setUp(): void
    {
        $this->config = Bootstrap::getObjectManager()->create(Config::class);
    }

    public function testSaveConnectionId()
    {
        $result = $this->config->saveConnectionId(['connection_id' => 'test123']);
        $this->assertTrue($result);
    }
}
