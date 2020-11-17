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

namespace Naxero\BuyNow\Model\Service;

use Magento\Vault\Api\Data\PaymentTokenInterface;
use Naxero\BuyNow\Model\Config\Naming;

/**
 * Class CardHandlerService.
 */
class CardHandlerService
{
    /**
     * @var array
     */
    public static $cardMapper = [
        'VI' => 'Visa',
        'MC' => 'Mastercard',
        'AE' => 'American Express',
        'DN' => 'Diners Club International',
        'DI' => 'Discover',
        'JCB' => 'JCB'
    ];

    /**
     * @var Repository
     */
    public $assetRepository;

    /**
     * @var Reader
     */
    public $directoryReader;

    /**
     * @var Csv
     */
    public $csvParser;

    /**
     * CardHandlerService constructor.
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Framework\Module\Dir\Reader $directoryReader,
        \Magento\Framework\File\Csv $csvParser
    ) {
        $this->assetRepository = $assetRepository;
        $this->directoryReader = $directoryReader;
        $this->csvParser = $csvParser;
    }

    /**
     * Get a card code from name.
     *
     * @return string
     */
    public function getCardCode($scheme)
    {
        return array_search(
            $scheme,
            self::$cardMapper
        );
    }

    /**
     * Get a card scheme from code.
     *
     * @return string
     */
    public function getCardScheme($code)
    {
        if (isset(self::$cardMapper[$code])) {
            return self::$cardMapper[$code];
        }
    }
    
    /**
     * Get a card icon.
     *
     * @return string
     */
    public function getCardIcon($code)
    {
        return $this->assetRepository ->getUrl(
            Naming::getModuleName() . '::images/cards/' . strtolower($code) . '.svg'
        );
    }

    /**
     * Check if a card is active.
     *
     * @return bool
     */
    public function isCardActive($card)
    {
        return $card->getIsActive() && $card->getIsVisible();
    }
}
