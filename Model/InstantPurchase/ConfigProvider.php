<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;


/**
 * Class ConfigProvider
 */
class ConfigProvider implements \Magento\Customer\CustomerData\SectionSourceInterface
{
	/**
     * {@inheritdoc}
     */
    public function getSectionData() : array
    {
    	$config = [
    		'name' => 'John Doe',
    		'Email' => 'john@webkul.com',
    		'DOB' => '08/05/1990',
    	];
 
    	return $config;
    }
}
