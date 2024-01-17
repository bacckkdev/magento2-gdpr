<?php

declare(strict_types=1);

namespace Redepy\GDPR\Model\Cookie;

class CookieBackend extends CookieManagement
{
    /**
     * @param int $storeId
     * @return \Redepy\GDPR\Model\ResourceModel\Cookie\Collection
     */
    protected function createCookieCollection(int $storeId = 0) {
        $collection = $this->cookieCollectionFactory->create();

        return $collection;
    }
}
