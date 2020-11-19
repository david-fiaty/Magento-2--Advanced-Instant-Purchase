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

namespace Naxero\BuyNow\Controller\Order;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Agreement controller class
 */
class Agreements extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var Config
     */
    public $configHelper;
    
    /**
     * Agreement controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        
        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * Handles the controller method.
     */
    public function execute()
    {
        // Prepare the output
        $html = '';

        // Process the request
        $request = $this->getRequest();

        // Todo - Add form key validation
        //if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
        if ($request->isAjax()) {
            $html .= $this->newAgreementBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Get the terms and conditions.
     */
    public function newAgreementBlock()
    {
        $enableAgreements = $this->configHelper->value('general/enable_agreements');
        if ($enableAgreements) {
            return $this->pageFactory->create()->getLayout()
                ->createBlock('Magento\CheckoutAgreements\Block\Agreements')
                ->setTemplate(Naming::getModuleName() . '::agreements/agreements-detail.phtml')
                ->toHtml();
        }

        return '';
    }
}
