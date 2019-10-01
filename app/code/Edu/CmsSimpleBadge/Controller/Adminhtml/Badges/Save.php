<?php
/**
 * Edu Admin Cagegory Map Record Save Controller.
 * @category  Edu
 * @package   Edu_CmsSimpleBadge
 * @author    Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Manager;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Edu\CmsSimpleBadge\Api\BadgesRepositoryInterface;
use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;
use Edu\CmsSimpleBadge\Api\Data\BadgesInterfaceFactory;
use Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;
use Edu\CmsSimpleBadge\Model\Uploader;
use Edu\CmsSimpleBadge\Model\UploaderPool;
use Magento\Framework\Registry;

class Save extends Badges
{
    /**
     * @var BadgesFactory
     */
    protected $badgesFactory;

    /**
     * @var DirectoryList
     */
    protected $fileUploaderFactory;
    /**
     * @var Manager
     */
    protected $messageManager;

    /**
     * @var BadgesRepositoryInterface
     */
    protected $badgesRepository;


    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var UploaderPool
     */
    protected $uploaderPool;

    /**
     * Save constructor.
     *
     * @param BadgesRepositoryInterface $badgesRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Manager $messageManager
     * @param BadgesInterfaceFactory $badgesFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param UploaderPool $uploaderPool
     * @param Context $context
     * @param BadgesFactory $badgesFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        BadgesFactory $badgesFactory,
        BadgesRepositoryInterface $badgesRepository,
        Manager $messageManager,
        BadgesInterfaceFactory $badgesInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        UploaderPool $uploaderPool
    )
    {
        parent::__construct($registry, $badgesRepository, $resultPageFactory, $dateFilter, $context);
        $this->badgesFactory = $badgesFactory;
        $this->messageManager = $messageManager;
        $this->badgesInterfaceFactory = $badgesInterfaceFactory;
        $this->badgesRepository = $badgesRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->uploaderPool = $uploaderPool;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('badges_id');
            if ($id) {
                $model = $this->badgesRepository->getBadgeId($id);
            } else {
                unset($data['badges_id']);
                $model = $this->badgesFactory->create();
            }

            try {
                $image = $this->getUploader('badges')->uploadFileAndGetName('image', $data);
                $dataModel=[];
                $dataModel['image_url'] = $image;
                $dataModel['name'] = $this->getRequest()->getParam('name');
                $dataModel['status'] = $this->getRequest()->getParam('status');

                $rowData = $this->badgesFactory->create();
                $rowData->setData($dataModel);
                $this->badgesRepository->save($rowData);

                //$this->dataObjectHelper->populateWithArray($model, $data, badgesInterface::class);
                //$this->badgesRepository->save($model);


                $this->messageManager->addSuccessMessage(__('You saved this badges.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['badges_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the badges:' . $e->getMessage())
                );
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['badges_id' => $this->getRequest()->getParam('badge_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Edu_badges::save');
    }

    /**
     * @param $type
     * @return Uploader
     * @throws \Exception
     */
    protected function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }
}
