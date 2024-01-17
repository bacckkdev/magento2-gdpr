<?php

namespace Redepy\GDPR\Ui\DataProvider\Listing;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Redepy\GDPR\Model\ResourceModel\Cookie\CollectionFactory;

class CookieGroupDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Redepy\GDPR\Model\ResourceModel\CookieGroup\CollectionFactory $collectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Redepy\GDPR\Model\ResourceModel\CookieGroup\CollectionFactory $collectionFactory,
        string                                                         $name,
        string                                                         $primaryFieldName,
        string                                                         $requestFieldName,
        array                                                          $meta = [],
        array                                                          $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|\Redepy\GDPR\Model\ResourceModel\Cookie\Collection|\Redepy\GDPR\Model\ResourceModel\CookieGroup\Collection
     */
    public function getCollection() {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }
}
