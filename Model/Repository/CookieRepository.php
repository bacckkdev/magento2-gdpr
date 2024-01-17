<?php

namespace Redepy\GDPR\Model\Repository;

use Redepy\GDPR\Api\CookieRepositoryInterface;
use Redepy\GDPR\Api\Data\CookieInterface;
use Redepy\GDPR\Model\CookieFactory;
use Redepy\GDPR\Model\ResourceModel\Cookie as CookieResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CookieRepository implements CookieRepositoryInterface
{
    /**
     * @var CookieFactory
     */
    private $cookieFactory;

    /**
     * @var CookieResource
     */
    private $cookieResource;

    /**
     * @var array
     */
    private $cookies;

    /**
     * @var array
     */
    private $snapshots = [];

    /**
     * @param CookieFactory $cookieFactory
     * @param CookieResource $cookieResource
     */
    public function __construct(
        CookieFactory  $cookieFactory,
        CookieResource $cookieResource
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->cookieResource = $cookieResource;
    }

    /**
     * @param CookieInterface $cookie
     * @param int $storeId
     * @return CookieInterface
     * @throws CouldNotSaveException
     */
    public function save(CookieInterface $cookie, int $storeId = 0) {
        try {
            if ($cookie->getId()) {
                $cookie = $this->getById($cookie->getId(), $storeId)
                    ->addData($cookie->getData());
            }

            $cookieSnapshot = $this->snapshots[$cookie->getId()][$storeId] ?? $this->cookieFactory->create();
            $this->cookieResource->setStoreId($storeId);
            $this->cookieResource->save($cookie);
            unset($this->cookies[$cookie->getId()], $this->snapshots[$cookie->getId()][$storeId]);
            $currentCookie = $this->getById($cookie->getId(), $storeId);

        } catch (\Exception $e) {
            if ($cookie->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save cookie with ID %1. Error: %2',
                        [$cookie->getId(), $e->getMessage()]
                    )
                );
            }

            throw new CouldNotSaveException(__('Unable to save new cookie. Error: %1', $e->getMessage()));
        }

        return $cookie;
    }

    /**
     * @param $cookieId
     * @param int $storeId
     * @return mixed|CookieInterface
     * @throws NoSuchEntityException
     */
    public function getById($cookieId, int $storeId = 0) {
        if (!isset($this->cookies[$cookieId][$storeId])) {
            /** @var \Redepy\GDPR\Model\Cookie $cookie */
            $cookie = $this->cookieFactory->create();
            $this->cookieResource->setStoreId($storeId);
            $this->cookieResource->load($cookie, $cookieId);

            if (!$cookie->getId()) {
                throw new NoSuchEntityException(__('Cookie with specified ID "%1" not found.', $cookieId));
            }
            $this->cookies[$cookieId][$storeId] = $cookie;
            $this->snapshots[$cookieId][$storeId] = clone $cookie;
        }

        return $this->cookies[$cookieId][$storeId];
    }

    /**
     * @param $cookieName
     * @return \Redepy\GDPR\Model\Cookie
     * @throws NoSuchEntityException
     */
    public function getByName($cookieName) {
        /** @var \Redepy\GDPR\Model\Cookie $cookie */
        $cookie = $this->cookieFactory->create();
        $this->cookieResource->load($cookie, $cookieName, CookieInterface::NAME);

        if (!$cookie->getId()) {
            throw new NoSuchEntityException(__('Cookie with specified Name "%1" not found.', $cookieName));
        }

        return $cookie;
    }

    /**
     * @param CookieInterface $cookie
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete(CookieInterface $cookie) {
        try {
            $this->cookieResource->delete($cookie);
            unset($this->cookies[$cookie->getId()], $this->snapshots[$cookie->getId()]);
        } catch (\Exception $e) {
            if ($cookie->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove cookie with ID %1. Error: %2',
                        [$cookie->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove cookie. Error: %1', $e->getMessage()));
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
        $cookie = $this->getById($cookieId);

        $this->delete($cookie);
    }
}
