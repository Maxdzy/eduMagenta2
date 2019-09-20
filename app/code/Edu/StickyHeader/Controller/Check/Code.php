<?php
/**
 * @category    Edu
 * @package     Edu\StickyHeader
 * @author      Maxim Dzyuba
 */

declare(strict_types=1);

namespace Edu\StickyHeader\Controller\Check;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Code
 *
 * @package Edu\StickyHeader\Controller\Check
 */
class Code extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * Garment ID length without the last number which is used for the check
     */
    private const GARMENT_ID_LENGTH = 12;

    /**
     * Code constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @return ResponseInterface|Forward|Json|ResultInterface
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        return $response->setData([
            'isAuthentic' => "12312312312312"
        ]);
    }

    /**
     * Check if the provided garment ID belongs to an authentic Moose Knuckles product
     *
     * @param string $garmentId
     * @return bool
     */
    private function isAuthentic($garmentId): bool
    {
        $garmentIdArray = str_split($garmentId);
        $checkNumber = array_pop($garmentIdArray);
        if (count($garmentIdArray) !== self::GARMENT_ID_LENGTH) {
            return false;
        }

        $evenSum = $oddSum = 0;

        foreach ($garmentIdArray as $key => $value) {
            if ($key % 2 === 0) {
                $evenSum += $value;
            } else {
                $oddSum += $value;
            }
        }

        $lastDigit = abs($evenSum - $oddSum) % 10;

        return $lastDigit === (int)$checkNumber;
    }
}
