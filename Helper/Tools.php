<?php
/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

namespace Naxero\BuyNow\Helper;

/**
 * Class Tools helper.
 */
class Tools extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var FormKey
     */
    public $formKey;

    /**
     * Class Tools helper constructor.
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->formKey = $formKey;
    }

    /**
     * Generate a form key.
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
