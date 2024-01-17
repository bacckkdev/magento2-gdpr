<?php

namespace Redepy\GDPR\Model;

use Redepy\GDPR\Api\Data\CookieInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Cookie extends AbstractModel implements IdentityInterface, CookieInterface
{
    const CACHE_TAG = 'redepy_gdprcookie';

    protected $_cacheTag = 'redepy_gdprcookie';

    protected $_eventPrefix = 'redepy_gdprcookie';

    /**
     * @return void
     */
    protected function _construct() {
        $this->_init(ResourceModel\Cookie::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues() {
        return [];
    }

    /**
     * @return int
     */
    public function getId() {
        return parent::getData(self::ID);
    }

    /**
     * @param $id
     * @return CookieInterface
     */
    public function setId($id) {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getName() {
        return parent::getData(self::NAME);
    }

    /**
     * @param $name
     * @return CookieInterface
     */
    public function setName($name) {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string
     */
    public function getDescription() {
        return parent::getData(self::DESCRIPTION);
    }

    /**
     * @param $description
     * @return CookieInterface
     */
    public function setDescription($description) {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return int
     */
    public function getGroupId() {
        return $this->_getData(CookieInterface::GROUP_ID);
    }

    /**
     * @param $groupId
     * @return CookieInterface
     */
    public function setGroupId($groupId) {
        $this->setData(CookieInterface::GROUP_ID, $groupId);

        return $this;
    }
}
