<?php

namespace Edu\CmsCreateStore\Console\Command;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Design\Theme\ListInterface as themeList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ColorChange
 */
class ThemeChange extends Command
{
    /**
     * @inheritDoc
     */
    const THEME_FULL_PATH = 'frontend/Magento/blank';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    private $context;
    private $themeList;
    private $collectionFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param ThemeList $themeList
     * @param CollectionFactory $themeCollectionFactory
     */

    //ScopeConfigInterface
    public function __construct(
        StoreManagerInterface $storeManager,
        ThemeList $themeList,
        CollectionFactory $themeCollectionFactory,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->context = $context;
        $this->themeList = $themeList;
        $this->collectionFactory = $themeCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('edu:theme-change');
        $this->setDescription('This is my first console command. Param: not');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('================================');
        //$store = $this->storeManager->getStore('ded');

        $themeCollection = $this->collectionFactory->create();
        $theme = $themeCollection->getThemeByFullPath("frontend/Skin/german");
        $themeId = $theme->getId();

        $output->writeln($themeId);

        /*if (isset($groupid)) {
            $output->writeln($groupid->getId());
        } else {
            $output->writeln('code ded123 is not found');
        }*/
    }
}
