<?php

namespace Edu\CmsSimpleBadge\Console\Command;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ObjectManager;
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

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Sliders constructor.
     *
     * @param BadgesFactory $badges
     * @param Context $context
     */
    public function __construct(
        BadgesFactory $badges,
        Context $context
    ) {
        $this->badges = $badges;
        $this->context = $context;
        $this->objectManager = ObjectManager::getInstance();
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $productId=5;
        $product = $this->objectManager->get('Magento\Catalog\Model\Product')->load($productId);
        $p = $product->getData('badges_list');
        $q = $product->getBadgesList();
        $output->writeln($p);
        $output->writeln($q);

        $badges = $this->badges->create()->getCollection();
        foreach ($badges as $item) {
            $output->writeln('id = ' . $item->getData()['badge_id'] . ';');
            $output->writeln('name = ' . $item->getData()['name']);
        }

        return null;
    }
}
