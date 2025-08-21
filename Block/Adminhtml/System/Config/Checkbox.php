<?php
/**
 * Checkbox file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage Block
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Block\Adminhtml\System\Config;

use UPSBG\Dashboard\Model\Config\Source\Checkbox as CheckboxOp;

/**
 * Class Checkbox
 * To add dynamic checkout boxes for terms
 */
class Checkbox extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Configuration path.
     */
    public const CONFIG_PATH = 'upsbg_dashboard/general/checkbox';

    /**
     * @var $_template
     */
    protected $_template = 'UPSBG_Dashboard::system/config/checkbox.phtml';

    /**
     * @var checkbox
     */
    protected $checkboxop;

    /**
     * @var _values
     */
    protected $_values = null;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context The context.
     * @param \UPSBG\Dashboard\Model\Config\Source\Checkbox $checkboxop The checkbox operation model.
     * @param array $data Additional data.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CheckboxOp $checkboxop,
        array $data = []
    ) {
        $this->checkboxop = $checkboxop;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());

        return $this->_toHtml();
    }
    
    /**
     * Get the values for the checkboxes.
     *
     * @return array The values.
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->checkboxop->toOptionArray() as $value) {
            $values[$value['value']] = $value['label'];
        }

        return $values;
    }

    /**
     * Get whether the specified value is checked.
     *
     * @param string $name The name of the checkbox.
     * @return bool True if the checkbox is checked, false otherwise.
     */
    public function getIsChecked($name)
    {
        return in_array($name, $this->getCheckedValues());
    }

    /**
     * Get the checked values from configuration.
     *
     * @return array The checked values.
     */
    public function getCheckedValues()
    {
        if ($this->_values === null) {
            $data = $this->getConfigData();
            if (isset($data[self::CONFIG_PATH])) {
                $data = $data[self::CONFIG_PATH];
            } else {
                $data = '';
            }
            $this->_values = explode(',', $data);
        }
        return $this->_values;
    }
}
