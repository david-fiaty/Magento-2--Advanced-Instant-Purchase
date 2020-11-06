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

namespace Naxero\BuyNow\Controller\Address;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * FormAdd Class.
 */
class FormAdd extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var CurrentCustomer
     */
    public $currentCustomer;

    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * BillingAddress constructor.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->currentCustomer = $currentCustomer;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Handles the controller method.
     */
    public function execute()
    {
        // Prepare the output
        $html = '';

        // Process the request
        if ($this->getRequest()->isAjax()) {
            $html .= $this->newAddressBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Generates the new address block.
     */
    public function newAddressBlock()
    {
        // Prepare the block arguments
        $params = [
            'customerSession' => $this->customerSession,
            'currentCustomer' => $this->currentCustomer
        ];

        // Build the block
        $blockHtml = $this->pageFactory->create()->getLayout()
        ->createBlock(
            Naming::getModulePath() . '\Block\Address\Edit',
            'customer_address_edit',
            $params
        )
        ->setTemplate(Naming::getModuleName() . '::address/edit.phtml')
        ->toHtml();

        return $blockHtml;
    }
}
