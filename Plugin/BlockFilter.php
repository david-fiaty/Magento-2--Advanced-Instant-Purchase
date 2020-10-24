<?php
namespace Naxero\AdvancedInstantPurchase\Plugin;

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
        \Naxero\AdvancedInstantPurchase\Helper\Block $blockHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Logger $loggerHelper
    ) {
        $this->blockHelper = $blockHelper;
        $this->productHelper = $productHelper;
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * After to HTML plugin event handler.
     */
    public function afterToHtml($subject, $html) {
        // Find block tags in the content
        $matches = $this->blockHelper->getBlockTags($subject, $html);

        // Process tags found
        if ($matches) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                // Loop through the tag parameters
                if ($this->tagHasParameters($matches, $i)) {
                    // Prepare the loop variables
                    $errors = [];
                    $tag = $matches[0][$i];

                    // Build the block
                    $block = $this->blockHelper->buildButtonBlock($subject);

                    // Process the block tab parameters
                    foreach (self::$blockParams as $key) {
                        // Process the parameter
                        $result = $this->processParam($key, $i, $matches, $block);

                        // Handle the parameter errors
                        if ($result['errors'] > 0) {
                            $errors[] = $result['errors'];
                        }
                    }

                    // Replace the tag with the generated HTML
                    if (empty($errors[0])) {
                        // Success
                        $html = str_replace($tag, $result['blockHtml']->toHtml(), $html);
                    } else {
                        // Errors
                        $errorsHtml = '';
                        foreach ($errors as $error) {
                            foreach ($error as $msg) {
                                $errorsHtml .= $this->loggerHelper->renderUiMessage($msg);
                            }
                        }
                        $html = str_replace($tag, $errorsHtml, $html);
                    }
                }
            }
        }

        return $html;
    }

    /**
     * Process a block parameter.
     */
    public function processParam($field, $i, $matches, $blockHtml) {
        // Prepare the errors count
        $errors = [];

        // Process the parameter field
        $search = '/' . $field . '="(.*?)"/';
        preg_match($search, $matches[1][$i], $param);        
        if (isset($param[1]) && !empty($param[1])) {
            if ($this->isParameterValid($field, $param)) {
                $blockHtml->setData($field, $param[1]);
            } 
            else {
                $errors[] = __(
                    'Invalid value "%1" for parameter %2',
                    $param[1],
                    $field
                );
            }
        }

        return [
            'blockHtml' => $blockHtml,
            'errors' => $errors
        ];
    }

    /**
     * Check if a block tag has parameters.
     */
    public function tagHasParameters(array $matches, int $i) {
        return isset($matches[1]) && isset($matches[1][$i]) && !empty($matches[1][$i]);
    }

    /**
     * Check if a tag parameter is valid.
     */
    public function isParameterValid(string $field, array $param) {
        $condition1 = isset($param[1]) && (int) $param[1] > 0;
        $condition2 = $this->isParameterRegistered($field);
        $condition3 = true;

        // Validation for product_id
        if ($field == 'product_id' && $condition1 && $condition2) {
            $condition3 = $this->productHelper->isProduct($param[1]);
        }

        return $condition1 && $condition2 && $condition3;
    }

    /**
     * Check if a tag parameter is registered.
     */
    public function isParameterRegistered(string $field) {
        return in_array($field, self::$blockParams);
    }
} 