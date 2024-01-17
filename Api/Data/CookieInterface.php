<?php

namespace Redepy\GDPR\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CookieInterface extends ExtensibleDataInterface
{
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const GROUP_ID = 'group_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return CookieInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return CookieInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $description
     * @return CookieInterface
     */
    public function setDescription($description);

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $groupId
     * @return CookieInterface
     */
    public function setGroupId($groupId);
}
