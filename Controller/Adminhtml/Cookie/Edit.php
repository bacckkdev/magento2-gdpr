<?php

namespace Redepy\GDPR\Controller\Adminhtml\Cookie;

use Redepy\GDPR\Controller\Adminhtml\AbstractCookie;
use Redepy\GDPR\Model\Repository\CookieRepository;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

class Edit extends AbstractCookie
{
    /**
     * @var CookieRepository
     */
    private $cookieRepository;

    /**
     * @param Context $context
     * @param CookieRepository $cookieRepository
     */
    public function __construct(
        Context          $context,
        CookieRepository $cookieRepository
    ) {
        parent::__construct($context);
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * @return Page
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute() {
        $id = (int)$this->getRequest()->getParam('id');
        $storeId = (int)$this->getRequest()->getParam('store');
        $title = __('New Cookie');

        if ($id) {
            $model = $this->cookieRepository->getById($id, $storeId);
            $title = __('Edit Cookie %1', $model->getName());
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if (!$id) {
            $resultPage->getLayout()->unsetElement('store_switcher');
        }

        $resultPage->setActiveMenu('Redepy_GDPR::cookies');
        $resultPage->addBreadcrumb(__('Cookies'), __('Cookies'));
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
