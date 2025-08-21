<?php
/**
 * Checkbox file
 *
 * @category   UPSBG
 * @package    UPSBG_Dashboard
 * @subpackage Model
 * @link       https://www.ups.com
 * @since      1.0.0
 * @author UPSBG eCommerce Shipping Dashboard
 */
namespace UPSBG\Dashboard\Model\Config\Source;

/**
 * Class Checkbox
 *
 * Used in creating options for getting product type value
 *
 */
class Checkbox
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'tc_checkbox_0',
                'label' => __(
                    'By checking you agree with <a href="%1" target="_blank">UPS Privacy Notice</a>.',
                    'https://www.ups.com/de/en/support/shipping-support/legal-terms-conditions/privacy-notice.page'
                )// @codingStandardsIgnoreLine
            ],
            [
                'value' => 'tc_checkbox_1',
                'label' => __(
                    'By checking you agree with <a href="%1" target="_blank">UPS Terms and Conditions</a>.',
                    'https://www.ups.com/de/en/support/shipping-support/legal-terms-conditions.page'
                )// @codingStandardsIgnoreLine
            ],
            [
                'value' => 'tc_checkbox_2',
                'label' => __(
                    'By checking you agree with <a href="%1" target="_blank">Itembase Merchant Terms</a>.',
                    'https://www.itembase.com/terms/merchant'
                )// @codingStandardsIgnoreLine
            ]
        ];
    }
}
