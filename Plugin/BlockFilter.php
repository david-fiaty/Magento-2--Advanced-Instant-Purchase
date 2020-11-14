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

namespace Naxero\BuyNow\Plugin;

/**
 * Class BlockFilter.
 */
class BlockFilter
{
    /**
     * Array
     */
    public static $blockParams = [
        'product_id'
    ];

    /**
     * Block
     */
    public $blockHelper;

    /**
     * Product
     */
    public $productHelper;

    /**
     * Logger
     */
    public $loggerHelper;

    /**
     * Class BlockFilter constructor.
     */
    public function __construct(
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Helper\Logger $loggerHelper
    ) {
        $this->blockHelper = $blockHelper;
        $this->productHelper = $productHelper;
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * After to HTML plugin event handler.
     */
    public function afterToHtml($subject, $html)
    {
        // Find block tags in the content
        $matches = $this->blockHelper->getBlockTags($subject, $html);

        // Process tags found
        if ($matches) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                if ($this->tagHasParameters($matches, $i)) {
                    // Prepare the loop variables
                    $errors = [];
                    $tag = $matches[0][$i];

                    // Build the block
                    $block = $this->blockHelper->buildButtonBlock($subject);

                    // Process the block tab parameters
                    $errors = $this->processParams($i, $matches, $block);

                    // Replace the tag with the generated HTML
                    if (empty($errors[0])) {
                        // Get the block HTMl
                        $blockHtml = str_replace($tag, $result['blockHtml']->toHtml(), $html);

                        // Get the product HTML
                        $productHtml = $this->blockHelper->renderProductBox(
                            $result['field']['value'],
                            $subject
                        );

                        // Finalise the output
                        $html = $productHtml . $blockHtml;
                    } else {
                        // Render the errors
                        $errorsHtml = $this->renderErrors($errors);
                        $html = str_replace($tag, $tag . $errorsHtml, $html);
                    }
                }
            }
        }

        return $html;
    }

    /**
     * Render all block parameters errors.
     */
    public function renderErrors($errors)
    {
        $errorsHtml = '';
        foreach ($errors as $error) {
            foreach ($error as $msg) {
                $errorsHtml .= $this->loggerHelper->renderUiMessage($msg);
            }
        }

        return $errorsHtml;
    }

    /**
     * Process all block parameters.
     */
    public function processParams($i, $matches, $block)
    {
        // Prepare the errors array
        $errors = [];

        // Check all parameters
        foreach (self::$blockParams as $key) {
            // Process the parameter
            $result = $this->processParam($key, $i, $matches, $block);

            // Handle the parameter errors
            if ($result['errors'] > 0) {
                $errors[] = $result['errors'];
            }
        }

        return $errors;
    }

    /**
     * Process a block parameter.
     */
    public function processParam($field, $i, $matches, $blockHtml)
    {
        // Prepare the errors count
        $errors = [];

        // Field search pattern
        $search = '/' . $field . '="(.*?)"/';

        // Look for the field
        preg_match($search, $matches[1][$i], $param);
        
        // If the field was found
        if (isset($param[1]) && !empty($param[1])) {
            // Checkf if the parameter is valid
            $result = $this->isParameterValid($field, $param);
            if ($result['is_valid']) {
                // Set the parameter argument
                $blockHtml->setData($field, $param[1]);
            } else {
                // Handle the parameter error
                $errors[] = $result['error'];
            }
        }

        return [
            'field' => [
                'name' => $field,
                'value' => $param[1]
            ],
            'blockHtml' => $blockHtml,
            'errors' => $errors
        ];
    }

    /**
     * Check if a block tag has parameters.
     */
    public function tagHasParameters(array $matches, int $i)
    {
        return isset($matches[1]) && isset($matches[1][$i]) && !empty($matches[1][$i]);
    }

    /**
     * Check if a tag parameter is valid.
     */
    public function isParameterValid(string $field, array $param)
    {
        // Prepare the conditions
        $condition1 = isset($param[1]);
        $condition2 = $this->isParameterRegistered($field);
        $condition3 = true;
        $error = '';

        // Validation for product_id
        if ($field == 'product_id' && $condition1 && $condition2) {
            // Valid id
            if (!$this->productHelper->isProduct($param[1])) {
                $condition3 = false;
                $error = __('Invalid value "%1" for parameter %2', $param[1], $field);
            }
        }

        return [
            'is_valid' => $condition1 && $condition2 && $condition3,
            'error' => $error
        ];
    }

    /**
     * Check if a tag parameter is registered.
     */
    public function isParameterRegistered(string $field)
    {
        return in_array($field, self::$blockParams);
    }
}
