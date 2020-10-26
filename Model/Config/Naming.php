<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config;

/**
 * Class Naming.
 */
class Naming
{
    /**
     * Get the module name.
     */
    public static function getModuleName() {
        return 'Naxero_AdvancedInstantPurchase';
    }

    /**
     * Get the module alias.
     */
    public static function getModuleAlias() {
        return 'naxero_aip';
    }

    /**
     * Get the module path.
     */
    public static function getModulePath() {
        return 'Naxero\AdvancedInstantPurchase';
    }

    /**
     * Get the module title.
     */
    public static function getModuleTitle() {
        return __('Naxero Advanced Instant Purchase');
    }
} 