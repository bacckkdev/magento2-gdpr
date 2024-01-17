<?php

namespace Redepy\GDPR\Controller\Adminhtml\Cookie;

use Redepy\GDPR\Api\Data\CookieInterface;
use Redepy\GDPR\Controller\Adminhtml\AbstractCookie;
use Redepy\GDPR\Model\CookieFactory;

//use Redepy\GDPR\Model\OptionSource\Cookie\Groups;
use Redepy\GDPR\Model\Repository\CookieRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends AbstractCookie
{
    /**
     * @var CookieRepository
     */
    private $cookieRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CookieFactory
     */
    private $cookieFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param CookieRepository $cookieRepository
     * @param DataPersistorInterface $dataPersistor
     * @param CookieFactory $cookieFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                $context,
        CookieRepository       $cookieRepository,
        DataPersistorInterface $dataPersistor,
        CookieFactory          $cookieFactory,
        LoggerInterface        $logger
    ) {
        parent::__construct($context);
        $this->cookieRepository = $cookieRepository;
        $this->dataPersistor = $dataPersistor;
        $this->cookieFactory = $cookieFactory;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute() {
        $formData = $this->getRequest()->getPostValue('cookie');
        $storeId = (int)$this->getRequest()->getParam('store');

        try {
            $data = $formData;
            $model = isset($formData['id'])
                ? $this->cookieRepository->getById($formData['id'])
                : $this->cookieFactory->create();

            if ($data[CookieInterface::GROUP_ID] === (string)Groups::NONE_GROUP_ID) {
                $data[CookieInterface::GROUP_ID] = null;
            }

            if ($storeId) {
                $this->modifyUseDefaultsData($data);
            }

            $model->setData($data);
            $this->cookieRepository->save($model, $storeId);
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
            $this->messageManager->addErrorMessage(__('An error has occurred.'));
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
