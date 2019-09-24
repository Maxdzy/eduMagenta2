<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Edu\CmsSimpleBadge\Model\BadgesFactory;

class Index extends Action
{
    protected $pageFactory;

    protected $badgesFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        BadgesFactory $badgesFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->badgesFactory = $badgesFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $badge = $this->badgesFactory->create();
        $collection = $badge->getCollection();
        foreach ($collection as $item) {
            echo "<pre>";
            print_r($item->getData());
            echo "</pre>";
        }
        exit();
        return $this->pageFactory->create();
    }
}
