<?php

namespace Redepy\GDPR\Controller\Adminhtml\CookieGroup;

use Redepy\GDPR\Controller\Adminhtml\AbstractCookieGroup;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractCookieGroup
{
    /**
     * @return Page
     */
    public function execute() {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Redepy_GDPR::cookie_group');
        $resultPage->getConfig()->getTitle()->prepend(__('Cookie Groups'));
        $resultPage->addBreadcrumb(__('Cookie Groups'), __('Cookie Groups'));

        return $resultPage;
    }
}
