<?php

declare(strict_types=1);

namespace Redepy\GDPR\Ui\DataProvider\Form;

use Magento\Framework\Api\Filter;
use Redepy\GDPR\Api\CookieRepositoryInterface;
use Redepy\GDPR\Model\ResourceModel\Cookie\CollectionFactory;
use Redepy\GDPR\Model\StoreData\ScopedFieldsProvider;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class CookieDataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @var ScopedFieldsProvider
     */
    private $scopedFieldsProvider;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param CookieRepositoryInterface $cookieRepository
     * @param ScopedFieldsProvider $scopedFieldsProvider
     * @param DataPersistorInterface $dataPersistor
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        CookieRepositoryInterface $cookieRepository,
        ScopedFieldsProvider $scopedFieldsProvider,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        UrlInterface $url,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        $this->url = $url;
        $this->cookieRepository = $cookieRepository;
        $this->scopedFieldsProvider = $scopedFieldsProvider;
    }

    /**
     * @return array|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData() {
        if ($this->getCollection()) {
            $storeId = (int)$this->request->getParam('store');
            $data = parent::getData();

            if ($data['totalRecords'] > 0) {
                $cookieId = (int)$data['items'][0]['id'];
                $cookie = $this->cookieRepository->getById($cookieId, $storeId);
                $data[$cookieId]['cookie'] = $cookie->getData();
            }

            if ($savedData = $this->dataPersistor->get('formData')) {
                $id = isset($savedData['id']) ? $savedData['id'] : null;
                if (isset($data[$id])) {
                    $data[$id] = array_merge($data[$id], $savedData);
                } else {
                    $data[$id] = $savedData;
                }
                $this->dataPersistor->clear('formData');
            }
            return $data;
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMeta() {
        $storeId = (int)$this->request->getParam('store');
        $cookieId = (int)$this->request->getParam($this->getRequestFieldName());
        $this->data['config']['submit_url'] = $this->url->getUrl('*/*/save', ['_current' => true]);
        $meta = parent::getMeta();

        if (!$cookieId) {
            return $meta;
        }

        $cookie = $this->cookieRepository->getById($cookieId, $storeId);
        $this->collection = $this->collectionFactory->create();
        $storeEntityTable = $this->collection->getMainTable();

        foreach ($this->scopedFieldsProvider->getScopedFields($storeEntityTable) as $scopedField) {
            $meta['settings']['children'][$scopedField]['arguments']['data']['config'] = [
                'scopeLabel' => __('[STORE VIEW]')
            ];

            if ($storeId) {
                $meta['settings']['children'][$scopedField]['arguments']['data']['config']['service'] = [
                    'template' => 'ui/form/element/helper/service'
                ];
                $meta['settings']['children'][$scopedField]['arguments']['data']['config']['disabled'] =
                    $cookie->dataHasChangedFor($scopedField) === false;
            }
        }

        if ($cookieId && $storeId) {
            $meta['settings']['children']['name']['arguments']['data']['config']['disabled'] = true;
            $meta['settings']['children']['provider']['arguments']['data']['config']['disabled'] = true;
            $meta['settings']['children']['type']['arguments']['data']['config']['disabled'] = true;
        }

        return $meta;
    }

    /**
     * @param Filter $filter
     * @return void
     */
    public function addFilter(Filter $filter) {
        if ($this->getCollection()) {
            $this->getCollection()->addFieldToFilter(
                $filter->getField(),
                [$filter->getConditionType() => $filter->getValue()]
            );
        }
    }
}
