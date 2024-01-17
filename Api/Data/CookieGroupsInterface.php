<?php

namespace Redepy\GDPR\Api\Data;

interface CookieGroupsInterface
{
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const IS_ENABLED = 'is_enabled';
    const IS_ESSENTIAL = 'is_essential';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return CookieGroupsInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return CookieGroupsInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return CookieGroupsInterface
     */
    public function setDescription($description);

    /**
     * @return bool
     */
    public function getIsEnabled();

    /**
     * @param $isEnabled
     * @return CookieGroupsInterface
     */
    public function setIsEnabled($isEnabled);

    /**
     * @return bool
     */
    public function getIsEssential();

    /**
     * @param $isEssential
     * @return CookieGroupsInterface
     */
    public function setIsEssential($isEssential);
}
