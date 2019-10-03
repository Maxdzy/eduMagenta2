<?php

namespace Edu\CmsSimpleBadge\Console\Command;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Backend\App\Action\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SomeCommand
 */
class badgesList extends Command
{
    /**
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;
    protected $_objectManager;

    /**
     * Sliders constructor.
     *
     * @param BadgesFactory $badges
     * @param Context $context
     * @param \Magento\Framework\App\ObjectManager $_objectManager
     */
    public function __construct(
        BadgesFactory $badges,
        Context $context
    ) {
        $this->badges = $badges;
        $this->context = $context;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('badges:select:all');
        $this->setDescription('This is badges console command.');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $product_id=5;
        $_product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($product_id);
        $p = $_product->getResource()->getAttribute('badges_select')->getFrontend()->getValue($_product);
        print_r($p);
        $output->writeln($p);


        /*$badges = $this->badges->create()->getCollection();
        foreach ($badges as $item) {
            $output->writeln('id = '.$item->getData()['badge_id'].';');
            $output->writeln('name = '.$item->getData()['name']);
        }*/


        return null;
    }
}
