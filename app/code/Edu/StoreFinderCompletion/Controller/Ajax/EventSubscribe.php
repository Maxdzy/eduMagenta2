<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\EmailAddress;
use Psr\Log\LoggerInterface;
use Edu\StoreFinderCompletion\Model\RsvpRepository;
use Magento\Framework\Data\Form\FormKey\Validator;
use Zend_Validate;
use Zend_Validate_Exception;

class EventSubscribe extends Action
{
    // Request params
    const PARAM_EMAIL = 'email';
    const PARAM_EVENT_ID = 'event_id';
    const PARAM_STORE_ID = 'store_id';

    // Result params
    const PARAM_STATUS = 'status';
    const PARAM_ERROR_MESSAGE = 'error_message';

    /**
     * @var RsvpRepository
     */
    protected $rsvpRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * EventSubscribe constructor.
     * @param Context $context
     * @param RsvpRepository $rsvpRepository
     * @param LoggerInterface $logger
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        RsvpRepository $rsvpRepository,
        LoggerInterface $logger,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->rsvpRepository = $rsvpRepository;
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $data = [static::PARAM_STATUS => 'error'];
        try {
            $params = $this->getParams();

            $rsvp = $this->rsvpRepository->create();
            $rsvp->setEmail($params[static::PARAM_EMAIL]);
            $rsvp->setEventId((int)$params[static::PARAM_EVENT_ID]);
            $rsvp->setStoreId((int)$params[static::PARAM_STORE_ID]);
            $this->rsvpRepository->save($rsvp);

            $data[static::PARAM_STATUS] = 'success';
        } catch (LocalizedException $exception) {
            $data[static::PARAM_ERROR_MESSAGE] = $exception->getMessage();
        } catch (\Exception $exception) {
            $data[static::PARAM_ERROR_MESSAGE] = __('Something went wrong. Please try again later.');
            $this->logger->error($exception);
        }

        /** @var Json $json */
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $json->setData($data);
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    protected function getParams()
    {
        $requestParams = $this->getRequest()->getParams();
        $this->validateParams($requestParams);

        return $requestParams;
    }

    /**
     * @param array $params
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    protected function validateParams(array $params)
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Form key is invalid'));
        }

        if (!isset($params[static::PARAM_EMAIL])) {
            throw new LocalizedException(__('Email address not set'));
        }

        if (!Zend_Validate::is($params[static::PARAM_EMAIL], EmailAddress::class)) {
            throw new LocalizedException(__('Invalid email address: %1', $params[static::PARAM_EMAIL]));
        }

        if (!isset($params[static::PARAM_EVENT_ID]) || !is_numeric($params[static::PARAM_EVENT_ID])) {
            throw new LocalizedException(__('Event ID not set'));
        }

        if (!isset($params[static::PARAM_STORE_ID]) || !is_numeric($params[static::PARAM_STORE_ID])) {
            throw new LocalizedException(__('Store ID not set'));
        }
    }
}
