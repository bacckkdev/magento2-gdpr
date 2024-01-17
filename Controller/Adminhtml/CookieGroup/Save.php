<?php

namespace Redepy\GDPR\Controller\Adminhtml\CookieGroup;

use Redepy\GDPR\Controller\Adminhtml\AbstractCookieGroup;
use Redepy\GDPR\Model\CookieGroupFactory;
use Redepy\GDPR\Model\Repository\CookieGroupsRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends AbstractCookieGroup
{
    /**
     * @var CookieGroupsRepository
     */
    private $cookieGroupRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param CookieGroupsRepository $cookieGroupRepository
     * @param DataPersistorInterface $dataPersistor
     * @param CookieGroupFactory $cookieGroupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                $context,
        CookieGroupsRepository $cookieGroupRepository,
        DataPersistorInterface $dataPersistor,
        CookieGroupFactory     $cookieGroupFactory,
        LoggerInterface        $logger
    ) {
        parent::__construct($context);
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->dataPersistor = $dataPersistor;
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute() {
        $formData = $this->getRequest()->getPostValue('cookiegroup');
        $storeId = (int)$this->getRequest()->getParam('store');

        try {
            $data = $formData;
            $data['store_id'] = $storeId;
            $model = isset($formData['id'])
                ? $this->cookieGroupRepository->getById($formData['id'])
                : $this->cookieGroupFactory->create();

            if (!$data['cookies']) {
                $data['cookies'] = [];
            }

            if ($storeId) {
                $this->modifyUseDefaultsData($data);
            }

            $model->setData($data);
            $this->cookieGroupRepository->save($model, $storeId);
            $this->messageManager->addSuccessMessage(__('You saved the item.'));

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);

                return;
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->redirectIfError($formData);

            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error has occured'));
            $this->logger->critical($e);
            $this->redirectIfError($formData);

            return;
        }

        $this->_redirect('*/*');
    }

    /**
     * @param array $data
     * @return void
     */
    private function modifyUseDefaultsData(array &$data) {
        $useDefaultData = $this->getRequest()->getPostValue('use_default');

        foreach ($useDefaultData as $field => $isUseDefault) {
            if ((bool)$isUseDefault === true) {
                $data[$field] = null;
            }
        }
    }

    /**
     * @param array $formData
     */
    private function redirectIfError($formData) {
        $this->dataPersistor->set('formData', $formData);

        if ($id = (int)$this->getRequest()->getParam('id')) {
            $this->_redirect('*/*/edit', ['id' => $id]);
        } else {
            $this->_redirect('*/*/new');
        }
    }
}
