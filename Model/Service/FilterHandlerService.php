<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

/**
 * Class FilterHandlerService.
 */
class FilterHandlerService
{
    /**
     * FilterHandlerService constructor.
     */
    public function __construct(

    ) {

    }

    /**
     * Filter content placeholders.
     */
    public function filterContent($content, $config)
    {
        // Product name
        $content = str_replace('{product_name}', $config['product']['name'], $content);

        // Product price
        $content = str_replace('{product_price}', $config['product']['price'], $content);

        return $content;
    }
}
