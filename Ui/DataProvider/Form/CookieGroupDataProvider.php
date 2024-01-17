<?php

declare(strict_types=1);

namespace Redepy\GDPR\Ui\DataProvider\Form;

use Magento\Framework\Api\Filter;
use Redepy\GDPR\Api\CookieGroupsRepositoryInterface;
use Redepy\GDPR\Model\Cookie\CookieBackend;
use Redepy\GDPR\Model\StoreData\ScopedFieldsProvider;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Redepy\GDPR\Model\ResourceModel\CookieGroup\CollectionFactory;
use Redepy\GDPR\Model\ResourceModel\CookieGroup\Collection;

class CookieGroupDataProvider extends AbstractDataProvider
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
     * @var CookieGroupsRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var ScopedFieldsProvider
     */
    private $scopedFieldsProvider;

    /**
     * @var CookieBackend
     */
    private $cookieGroupCollection;

    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Collection $cookieGroupCollection
     * @param CookieGroupsRepositoryInterface $groupRepository
     * @param ScopedFieldsProvider $scopedFieldsProvider
     * @param DataPersistorInterface $dataPersistor
     * @param CookieBackend $cookieBackend
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $cookieGroupCollection,
        CookieGroupsRepositoryInterface $groupRepository,
        ScopedFieldsProvider $scopedFieldsProvider,
        DataPersistorInterface $dataPersistor,
        CookieBackend $cookieBackend,
        RequestInterface $request,
        UrlInterface $url,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        $this->url = $url;
        $this->collection = $cookieGroupCollection;
        $this->groupRepository = $groupRepository;
        $this->scopedFieldsProvider = $scopedFieldsProvider;
        $this->cookieBackend = $cookieBackend;
    }

    /**
     * @return array|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData() {
        if ($this->getCollection()) {
            $storeId = (int)$this->request->getParam('store');
            $groupId = (int)$this->request->getParam($this->getRequestFieldName());
            $data = parent::getData();

            if ($data['totalRecords'] > 0) {
                $group = $this->groupRepository->getById($groupId, $storeId);
                $data[$groupId]['cookiegroup'] = $group->getData();
                $assignedCookieIds = [];

                foreach ($this->cookieBackend->getCookies($storeId, $groupId) as $cookie) {
                    $assignedCookieIds[] = (string)$cookie->getId();
                }

                $data[$groupId]['cookiegroup']['cookies'] = $assignedCookieIds;
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
        $groupId = (int)$this->request->getParam($this->getRequestFieldName());
        $this->data['config']['submit_url'] = $this->url->getUrl('*/*/save', ['_current' => true]);
        $disabledCookies = [];

        foreach ($this->cookieBackend->getNotAssignedCookiesToGroups($storeId, [$groupId]) as $cookie) {
            if ($cookie->getGroupId() !== null) {
                $disabledCookies[] = (string)$cookie->getId();
            }
        }

        $this->data['config']['disabled_cookies'] = $disabledCookies;
        $meta = parent::getMeta();

        if (!$groupId) {
            return $meta;
        }

        $group = $this->groupRepository->getById($groupId, $storeId);
        if (!$this->getCollection()) {
            $this->collection = $this->groupCollectionFactory->create();
        }
        $storeEntityTable = $this->collection->getMainTable();
        $meta['settings']['children']['cookies']['arguments']['data']['config'] =
            $this->getCookiesMeta($storeId, $groupId);

        foreach ($this->scopedFieldsProvider->getScopedFields($storeEntityTable) as $scopedField) {
            $meta['settings']['children'][$scopedField]['arguments']['data']['config'] = [
                'scopeLabel' => __('[STORE VIEW]')
            ];

            if ($storeId) {
                $meta['settings']['children'][$scopedField]['arguments']['data']['config']['service'] = [
                    'template' => 'ui/form/element/helper/service'
                ];
                $meta['settings']['children'][$scopedField]['arguments']['data']['config']['disabled'] =
                    $group->dataHasChangedFor($scopedField) === false;
            }
        }

        if ($storeId && $groupId) {
            $meta['settings']['children']['is_essential']['arguments']['data']['config']['disabled'] = true;
        }

        return $meta;
    }

    /**
     * @param int $storeId
     * @param int $groupId
     * @return array
     */
    private function getCookiesMeta(int $storeId, int $groupId): array {
        $cookieConfig = ['scopeLabel' => __('[STORE VIEW]')];

        if ($storeId) {
            $storeCookieIds = array_keys($this->cookieBackend->getCookies($storeId, $groupId));
            $allStoreCookieIds = array_keys($this->cookieBackend->getCookies(0, $groupId));
            $cookieConfig['service']['template'] = 'ui/form/element/helper/service';
            $cookieConfig['disabled'] = $storeCookieIds === $allStoreCookieIds;
        }

        return $cookieConfig;
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
