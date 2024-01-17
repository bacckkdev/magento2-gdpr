<?php

namespace Redepy\GDPR\Controller\Adminhtml\Cookie;

use Redepy\GDPR\Controller\Adminhtml\AbstractCookie;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractCookie
{
    /**
     * @return Page
     */
    public function execute() {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Redepy_GDPR::cookies');
        $resultPage->getConfig()->getTitle()->prepend(__('Cookies'));
        $resultPage->addBreadcrumb(__('Cookies'), __('Cookies'));

        return $resultPage;
    }
}
