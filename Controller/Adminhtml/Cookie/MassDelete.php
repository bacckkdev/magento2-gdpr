<?php
declare(strict_types=1);

namespace Redepy\GDPR\Controller\Adminhtml\Cookie;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Redepy\GDPR\Api\CookieRepositoryInterface;
use Redepy\GDPR\Api\Data\CookieInterface;
use Redepy\GDPR\Model\ResourceModel\Cookie\CollectionFactory;

class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CookieRepositoryInterface $cookieRepository
     */
    public function __construct(
        Context                   $context,
        Filter                    $filter,
        CollectionFactory         $collectionFactory,
        CookieRepositoryInterface $cookieRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->cookieRepository = $cookieRepository;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute() {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        try {
            $this->massAction($this->filter->getCollection($this->collectionFactory->create()));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @return void
     * @throws CouldNotDeleteException
     */
    private function massAction(AbstractDb $collection): void {
        $count = $collection->count();

        /** @var CookieInterface $cookie */
        foreach ($collection->getItems() as $cookie) {
            $this->cookieRepository->delete($cookie);
        }

        $this->messageManager->addSuccessMessage(new Phrase('A total of %1 record(s) have been deleted.', [$count]));
    }
}
