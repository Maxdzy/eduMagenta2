<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Adminhtml\Badges\Edit\Buttons;

use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Generic
 * @package Edu\CmsSimpleBadge\Block\Adminhtml\Badges\Edit\Buttons
 */
class Generic
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BadgesInterface
     */
    protected $badgesInterface;

    /**
     * @param Context $context
     * @param BadgesInterface $badgesInterface
     */
    public function __construct(
        Context $context,
        BadgesInterface $badgesInterface
    ) {
        $this->context = $context;
        $this->badgesInterface = $badgesInterface;
    }

    /**
     * Return Badge ID
     *
     * @return int
     */
    public function getBadgeId()
    {
        try {
            return $this->badgesInterface->getBadgeId(
                $this->context->getRequest()->getParam('badge_id')
            );
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
