<?php
/**
 * Edu Admin Cagegory Map Record Save Controller.
 * @category  Edu
 * @package   Edu_CmsSimpleBadge
 * @author    Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml;

use Edu\CmsSimpleBadge\Api\BadgesRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;

abstract class Badges extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Edu_CmsSimpleBadgeUploader::badge';

    /**
     * Image repository
     *
     * @var BadgesRepositoryInterface
     */
    protected $badgesRepositoryInterface;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Result Page Factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Date filter
     *
     * @var Date
     */
    protected $dateFilter;

    /**
     * Sliders constructor.
     *
     * @param Registry $registry
     * @param BadgesRepositoryInterface $badgesRepositoryInterface
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        BadgesRepositoryInterface $badgesRepositoryInterface,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context
    ) {
        parent::__construct($context);
        $this->coreRegistry = $registry;
        $this->badgesRepositoryInterface = $badgesRepositoryInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->dateFilter = $dateFilter;
    }
}
