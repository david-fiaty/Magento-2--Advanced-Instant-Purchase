<?php
namespace Naxero\BuyNow\Controller\Card;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * FormAdd Class.
 */
class FormAdd extends \Magento\Framework\App\Action\Action
{
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
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
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
            $html .= $this->newCardBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

      /**
     * Generates the new card block.
     */
    public function newCardBlock()
    {
        return $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate(Naming::getModuleName() . '::popup/card.phtml')
            ->setData('load', $this->configHelper->value('card_form/load'))
            ->toHtml();
    }
}
