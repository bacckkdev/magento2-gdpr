<?php

namespace Redepy\GDPR\Ui\DataProvider\Listing;

use Redepy\GDPR\Api\Data\CookieInterface;
use Redepy\GDPR\Model\ResourceModel\Cookie\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class CookieDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        string            $name,
        string            $primaryFieldName,
        string            $requestFieldName,
        array             $meta = [],
        array             $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|\Redepy\GDPR\Model\ResourceModel\Cookie\Collection
     */
    public function getCollection() {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->joinGroup();
        }

        return $this->collection;
    }

    /**
     * @param $field
     * @param $direction
     * @return void
     */
    public function addOrder($field, $direction) {
        if ($field === "group") {
            $field = "COALESCE(groups.name, \"None\")";
        }
        parent::addOrder($field, $direction);
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter) {
        switch ($filter->getField()) {
            case 'id':
                $field = 'main_table.id';
                break;
            case 'name':
                $field = 'main_table.name';
                break;
            case 'group':
                $field = 'groups.id';
                break;
        }
        if ($filter->getValue() === "0" && $filter->getField() === "group") {
            $this->getCollection()->addFieldToFilter(CookieInterface::GROUP_ID, ['null' => true]);
        } else {
            $this->getCollection()->addFieldToFilter(
                $field,
                [$filter->getConditionType() => $filter->getValue()]
            );
        }
    }
}
