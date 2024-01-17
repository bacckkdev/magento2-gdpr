<?php

namespace Redepy\GDPR\Api;

use Redepy\GDPR\Api\Data\CookieGroupsInterface;
use Redepy\GDPR\Api\Data\CookieInterface;

interface CookieManagementInterface
{
    /**
     * @param int $storeId
     * @param int $groupId
     * @return CookieInterface[]
     */
    public function getCookies(int $storeId = 0, int $groupId = 0): array;

    /**
     * @param int $storeId
     * @param array $groupIds
     * @return CookieInterface[]
     */
    public function getNotAssignedCookiesToGroups(int $storeId = 0, array $groupIds = []): array;

    /**
     * @param int $storeId
     * @param array $groupIds
     * @return CookieGroupsInterface[]
     */
    public function getGroups(int $storeId = 0, array $groupIds = []): array;
}
