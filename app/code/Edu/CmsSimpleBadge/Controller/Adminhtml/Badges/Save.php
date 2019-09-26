<?php
/**
 * Edu Admin Cagegory Map Record Save Controller.
 * @category  Edu
 * @package   Edu_CmsSimpleBadge
 * @author    Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;

class Save extends Action
{
    /**
     * @var BadgesFactory
     */
    public $badgesFactory;

    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @param Context $context
     * @param BadgesFactory $badgesFactory
     * @param ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(
        Context $context,
        BadgesFactory $badgesFactory,
        ObjectManagerInterface $objectManagerInterface
    ) {
        parent::__construct($context);
        $this->badgesFactory = $badgesFactory;
        $this->objectManager = $objectManagerInterface;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = 123;
        print_r($data);
        exit();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('badges/badges/addrow');
            return;
        }
        try {
            $rowData = $this->badgesFactory->create();
            $rowData->setData($data);


            $profileImage = $this->getRequest()->getFiles('image_url');
            $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;
            if ($profileImage && $fileName) {
                try {
                    /** @var ObjectManagerInterface $uploader */
                    $uploader = $this->objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'profile']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    /** @var AdapterInterface $imageAdapterFactory */
                    $imageAdapterFactory = $this->objectManager->get('Magento\Framework\Image\AdapterFactory')
                        ->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    /** @var Read $mediaDirectory */
                    $mediaDirectory = $this->objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath('Badges/Images')
                    );
                    //$data['profile'] = 'Modulename/Profile/'. $result['file'];
                    $rowData->setImageUrl('Badges/Images' . $result['file']); //Database field name
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }

            if (isset($data['id'])) {
                $rowData->setBadgeId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('badges/badges/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Edu_badges::save');
    }
}
