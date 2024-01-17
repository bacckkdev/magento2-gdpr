<?php

namespace Redepy\GDPR\Controller\Adminhtml\CookieGroup;

use Redepy\GDPR\Api\CookieGroupsRepositoryInterface;
use Redepy\GDPR\Controller\Adminhtml\AbstractCookieGroup;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends AbstractCookieGroup
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CookieGroupsRepositoryInterface
     */
    private $cookieGroupRepository;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param CookieGroupsRepositoryInterface $cookieGroupRepository
     */
    public function __construct(
        Action\Context                  $context,
        LoggerInterface                 $logger,
        CookieGroupsRepositoryInterface $cookieGroupRepository
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->cookieGroupRepository = $cookieGroupRepository;
    }

    /**
     * @return void
     */
    public function execute() {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->cookieGroupRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the cookie group.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete cookie group right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }

        $this->_redirect('*/*/');
    }
}
