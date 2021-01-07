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

namespace Naxero\BuyNow\Plugin\CustomerSessionContext;

/**
 * Class CustomerSessionContext.
 */
class CustomerSessionContext
{
    /**
 	 * @var \Magento\Customer\Model\Session
 	 */
    public $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    public $httpContext;

    /**
     * CustomerSessionContext constructor.
     */
    public function __construct(
    	\Magento\Customer\Model\Session $customerSession,
    	\Magento\Framework\App\Http\Context $httpContext
    ) {
    	$this->customerSession = $customerSession;
    	$this->httpContext = $httpContext;
    }

    /**
 	* @param \Magento\Framework\App\ActionInterface $subject
 	* @param callable $proceed
 	* @param \Magento\Framework\App\RequestInterface $request
 	* @return mixed
 	*/
     public function aroundDispatch(
    	\Magento\Framework\App\ActionInterface $subject,
    	\Closure $proceed,
    	\Magento\Framework\App\RequestInterface $request
    ) {
    	$this->httpContext->setValue(
        	'customer_id',
        	$this->customerSession->getCustomerId(),
        	false
    	);

    	$this->httpContext->setValue(
        	'customer_name',
        	$this->customerSession->getCustomer()->getName(),
        	false
    	);

    	$this->httpContext->setValue(
        	'customer_email',
        	$this->customerSession->getCustomer()->getEmail(),
        	false
    	);

    	return $proceed($request);
    }
}
