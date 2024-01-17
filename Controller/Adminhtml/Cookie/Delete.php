<?php

namespace Redepy\GDPR\Controller\Adminhtml\Cookie;

use Redepy\GDPR\Api\CookieRepositoryInterface;
use Redepy\GDPR\Controller\Adminhtml\AbstractCookie;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends AbstractCookie
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param CookieRepositoryInterface $cookieRepository
     */
    public function __construct(
        Action\Context            $context,
        LoggerInterface           $logger,
        CookieRepositoryInterface $cookieRepository
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * @return void
     */
    public function execute() {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->cookieRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the cookie.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete cookie right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }

        $this->_redirect('*/*/');
    }
}
