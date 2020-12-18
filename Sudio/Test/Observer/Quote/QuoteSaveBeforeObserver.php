<?php
declare(strict_types=1);

namespace Sudio\Test\Observer\Quote;

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Sudio\Test\Model\Data\AttributeInterface;

/**
 * Class QuoteSaveBeforeObserver
 *
 * @category Sudio
 * @package  Sudio_Test
 * @author   Bartosz Wojcik
 */
class QuoteSaveBeforeObserver implements ObserverInterface
{

    /**
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SaveBeforeObserver constructor.
     *
     * @param RedirectInterface $redirect
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        RedirectInterface $redirect,
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->redirect = $redirect;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        try {

            $url = $this->extractParamFromUrl($this->redirect->getRefererUrl());

            if ($url) {
                $quote->setData(AttributeInterface::MODULE_ATTRIBIUTE_NAME, $url);
                $this->quoteRepository->save($quote);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        return;
    }

    /**
     * @param string $urlInfo
     * @return string
     */
    protected function extractParamFromUrl(string $urlInfo) : string
    {
        if (!$urlInfo || !parse_url($urlInfo, PHP_URL_QUERY)) {
            return '';
        }
        parse_str(parse_url($urlInfo, PHP_URL_QUERY), $urlElements);

        return $urlElements[AttributeInterface::MODULE_ATTRIBIUTE_NAME] ?? '';

    }
}

