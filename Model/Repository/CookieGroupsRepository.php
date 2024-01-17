<?php

namespace Redepy\GDPR\Model\Repository;

use Redepy\GDPR\Api\CookieGroupsRepositoryInterface;
use Redepy\GDPR\Api\Data\CookieGroupsInterface;
use Redepy\GDPR\Model\CookieGroupFactory;
use Redepy\GDPR\Model\ResourceModel\CookieGroup as CookieGroupResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CookieGroupsRepository implements CookieGroupsRepositoryInterface
{
    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var CookieGroupResource
     */
    private $cookieGroupResource;

    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var array
     */
    private $snapshots = [];

    /**
     * @param CookieGroupFactory $cookieGroupFactory
     * @param CookieGroupResource $cookieGroupResource
     */
    public function __construct(
        CookieGroupFactory  $cookieGroupFactory,
        CookieGroupResource $cookieGroupResource,
    ) {
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->cookieGroupResource = $cookieGroupResource;
    }

    /**
     * @param CookieGroupsInterface $group
     * @param int $storeId
     * @return CookieGroupsInterface
     * @throws CouldNotSaveException
     */
    public function save(CookieGroupsInterface $group, int $storeId = 0) {
        try {
            if ($group->getId()) {
                $group = $this->getById($group->getId(), $storeId)
                    ->addData($group->getData());
            }

            $groupSnapshot = $this->snapshots[$group->getId()][$storeId] ?? $this->cookieGroupFactory->create();
            $this->cookieGroupResource->setStoreId($storeId);
            $this->cookieGroupResource->save($group);
            unset($this->groups[$group->getId()], $this->snapshots[$group->getId()][$storeId]);
            $currentGroup = $this->getById($group->getId(), $storeId);

        } catch (\Exception $e) {
            if ($group->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save cookie group with ID %1. Error: %2',
                        [$group->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new cookie group. Error: %1', $e->getMessage()));
        }

        return $group;
    }

    /**
     * @param $groupId
     * @param int $storeId
     * @return mixed|CookieGroupsInterface
     * @throws NoSuchEntityException
     */
    public function getById($groupId, int $storeId = 0) {
        if (!isset($this->groups[$groupId][$storeId])) {
            /** @var \Redepy\GDPR\Model\CookieGroup $group */
            $group = $this->cookieGroupFactory->create();
            $this->cookieGroupResource->setStoreId($storeId);
            $this->cookieGroupResource->load($group, $groupId);

            if (!$group->getId()) {
                throw new NoSuchEntityException(__('Cookie group with specified ID "%1" not found.', $groupId));
            }

            $this->groups[$groupId][$storeId] = $group;
            $this->snapshots[$groupId][$storeId] = clone $group;
        }

        return $this->groups[$groupId][$storeId];
    }

    /**
     * @param CookieGroupsInterface $group
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete(CookieGroupsInterface $group) {
        try {
            $this->cookieGroupResource->delete($group);

            unset($this->groups[$group->getId()], $this->snapshots[$group->getId()]);
        } catch (\Exception $e) {
            if ($group->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove cookie group with ID %1. Error: %2',
                        [$group->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove cookie group. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param $cookieId
     * @return void
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($cookieId) {
        $group = $this->getById($cookieId);

        $this->delete($group);
    }
}
