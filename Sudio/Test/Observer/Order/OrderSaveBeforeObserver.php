<?php
declare(strict_types=1);

namespace Sudio\Test\Observer\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject\Copy;
use Sudio\Test\Model\Data\AttributeInterface;

/**
 * Class OrderSaveBeforeObserver
 * @category Sudio
 * @package Sudio_Test
 * @author Bartosz Wójcik
 */
class OrderSaveBeforeObserver implements ObserverInterface
{

    /**
     * @var Copy
     */
    protected $objectCopyService;

    /**
     * @param Copy $objectCopyService
     */
    public function __construct(
        Copy $objectCopyService
    ) {
        $this->objectCopyService = $objectCopyService;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer): OrderSaveBeforeObserver
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');
        if ($quote->hasData(AttributeInterface::MODULE_ATTRIBIUTE_NAME)) {
            $order->setData(
                AttributeInterface::MODULE_ATTRIBIUTE_NAME,
                $quote->getData(AttributeInterface::MODULE_ATTRIBIUTE_NAME)
            );
        }

        return $this;
    }
}
