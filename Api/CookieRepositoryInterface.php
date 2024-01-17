<?php

namespace Redepy\GDPR\Api;

use Redepy\GDPR\Api\Data\CookieInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CookieRepositoryInterface
{
    /**
     * @param CookieInterface $cookie
     * @param int $storeId
     * @return CookieInterface
     */
    public function save(CookieInterface $cookie, int $storeId = 0);

    /**
     * @param int $cookieId
     * @param int $storeId
     * @return CookieInterface
     * @throws NoSuchEntityException
     */
    public function getById($cookieId, int $storeId = 0);

    /**
     * @param string $cookieName
     * @return CookieInterface
     * @throws NoSuchEntityException
     */
    public function getByName($cookieName);

    /**
     * @param CookieInterface $cookie
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CookieInterface $cookie);

    /**
     * @param int $cookieId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($cookieId);
}
