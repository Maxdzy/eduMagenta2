<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Scandiweb\StoreFinder\Helper\UrlUpdate as UrlUpdateHelper;

class RecurringData implements InstallDataInterface
{
    /**
     * @var UrlUpdateHelper
     */
    private $urlUpdateHelper;

    /**
     * Recurring constructor.
     * @param UrlUpdateHelper $urlUpdateHelper
     */
    public function __construct(UrlUpdateHelper $urlUpdateHelper)
    {
        $this->urlUpdateHelper = $urlUpdateHelper;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->reindexSeoUrls();

        $setup->endSetup();
    }

    private function reindexSeoUrls()
    {
        $this->urlUpdateHelper->recreateUrlRewritesFromConfig();
    }
}
