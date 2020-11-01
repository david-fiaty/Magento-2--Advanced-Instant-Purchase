<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

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
