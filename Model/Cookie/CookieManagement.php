<?php

declare(strict_types=1);

namespace Redepy\GDPR\Model\Cookie;

use Redepy\GDPR\Api\CookieManagementInterface;
use Redepy\GDPR\Api\Data\CookieGroupsInterface;
use Redepy\GDPR\Api\Data\CookieInterface;
use Redepy\GDPR\Model\ResourceModel\Cookie;
use Redepy\GDPR\Model\ResourceModel\CookieGroup;

class CookieManagement implements CookieManagementInterface
{
    /**
     * @var Cookie\CollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * @var CookieGroup\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @param Cookie\CollectionFactory $cookieCollectionFactory
     * @param CookieGroup\CollectionFactory $groupCollectionFactory
     */
    public function __construct(
        Cookie\CollectionFactory      $cookieCollectionFactory,
        CookieGroup\CollectionFactory $groupCollectionFactory
    ) {
        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @param int $storeId
     * @param int $groupId
     * @return array|CookieInterface[]
     */
    public function getCookies(int $storeId = 0, int $groupId = 0): array {
        $collection = $this->createCookieCollection($storeId);

        if ($groupId) {
            $collection->addFieldToFilter(CookieInterface::ID, ['eq' => $groupId]);
        }

        return $collection->getItems();
    }

    /**
     * @param int $storeId
     * @param array $groupIds
     * @return array|CookieInterface[]
     */
    public function getNotAssignedCookiesToGroups(int $storeId = 0, array $groupIds = []): array {
        $collection = $this->createCookieCollection($storeId);

        if ($groupIds) {
            $collection->addFieldToFilter(CookieInterface::ID, ['nin' => $groupIds]);
        }

        return $collection->getItems();
    }

    /**
     * @param int $storeId
     * @param array $groupIds
     * @return array|CookieGroupsInterface[]
     */
    public function getGroups(int $storeId = 0, array $groupIds = []): array {
        $collection = $this->groupCollectionFactory->create();
        $collection->addFieldToFilter(CookieGroupsInterface::IS_ENABLED, ['eq' => 1]);
        $collection->setOrder(CookieGroupsInterface::ID, $collection::SORT_ORDER_ASC);

        if ($groupIds) {
            $collection->addFieldToFilter(CookieGroupsInterface::ID, ['in' => $groupIds]);
        }

        return $collection->getItems();
    }

    /**
     * @param int $storeId
     * @return Cookie\Collection
     */
    protected function createCookieCollection(int $storeId = 0) {
        $collection = $this->cookieCollectionFactory->create();
        $collection->setStoreId($storeId);

        return $collection;
    }
}
