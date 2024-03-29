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

namespace Naxero\BuyNow\Block\Debug;

/**
 * ErrorMessage class constructor.
 */
class ErrorMessage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * ErrorMessage class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
    }
}
