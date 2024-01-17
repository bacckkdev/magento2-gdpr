<?php

namespace Redepy\GDPR\Model\ResourceModel\Cookie;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Redepy\GDPR\Model\ResourceModel\Cookie as CookieResourceModel;
use Redepy\GDPR\Model\Cookie as CookieModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'redepy_gdprcookie_collection';
    protected $_eventObject = 'redepy_gdprcookie_collection';
    protected $_storeManager;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface        $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface       $eventManager,
        StoreManagerInterface  $storeManager,
        AdapterInterface       $connection = null,
        AbstractDb             $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
    }

    /**
     * @return void
     */
    protected function _construct() {
        $this->_init(CookieModel::class, CookieResourceModel::class);
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        return $this->_toOptionArray('id');
    }

    /**
     * @param string|null $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = null, $labelField = 'name', $additional = []) {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * @return $this
     */
    public function joinGroup() {
        if (!$this->getFlag('group_table_joined')) {
            $this->getSelect()->joinLeft(
                ['groups' => $this->getTable('redepy_gdprcookie_group')],
                'main_table.group_id = groups.id',
                ['group' => 'IFNULL(groups.name, "None")']
            );
            $this->setFlag('group_table_joined', true);
        }

        return $this;
    }
}
