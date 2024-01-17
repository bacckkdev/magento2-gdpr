<?php

namespace Redepy\GDPR\Api;

use Redepy\GDPR\Api\Data\CookieGroupsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CookieGroupsRepositoryInterface
{
    /**
     * @param CookieGroupsInterface $group
     * @param int $storeId
     * @return CookieGroupsInterface
     */
    public function save(CookieGroupsInterface $group, int $storeId = 0);

    /**
     * @param int $groupID
     * @param int $storeId
     * @return CookieGroupsInterface
     * @throws NoSuchEntityException
     */
    public function getById($groupId, int $storeId = 0);

    /**
     * @param CookieGroupsInterface $group
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CookieGroupsInterface $group);

    /**
     * @param int $cookieId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($cookieId);
}
