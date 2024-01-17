<?php

namespace Redepy\GDPR\Model;

use Redepy\GDPR\Api\Data\CookieGroupsInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class CookieGroup extends AbstractModel implements CookieGroupsInterface, IdentityInterface
{
    const CACHE_TAG = 'redepy_cookie_groups';

    /**
     * @return void
     */
    public function _construct() {
        $this->_init(ResourceModel\CookieGroup::class);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->getData(self::NAME);
    }

    /**
     * @param $name
     * @return CookieGroupsInterface
     */
    public function setName($name) {
        $this->setData(self::NAME, $name);
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param $description
     * @return CookieGroupsInterface
     */
    public function setDescription($description) {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return bool
     */
    public function getIsEnabled() {
        return (bool)$this->getData(self::IS_ENABLED);
    }

    /**
     * @param $isEnabled
     * @return CookieGroupsInterface
     */
    public function setIsEnabled($isEnabled) {
        $this->setData(self::IS_ENABLED, $isEnabled);
    }

    /**
     * @return bool
     */
    public function getIsEssential() {
        return (bool)$this->getData(self::IS_ESSENTIAL);
    }

    /**
     * @param $isEssential
     * @return CookieGroupsInterface
     */
    public function setIsEssential($isEssential) {
        $this->setData(self::IS_ESSENTIAL, $isEssential);
    }

    /**
     * @return array
     */
    public function getIdentities() {
        return [self::CACHE_TAG];
    }

    /**
     * @return array
     */
    public function getCacheTags() {
        $tags = parent::getCacheTags();
        if (!$tags) {
            $tags = [];
        }
        return $tags + $this->getIdentities();
    }
}
