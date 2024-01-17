<?php

namespace Redepy\GDPR\Model\OptionSource\Cookie;

use Redepy\GDPR\Api\CookieManagementInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Groups implements OptionSourceInterface
{
    const NONE_GROUP_ID = 0;

    /**
     * @var CookieManagementInterface
     */
    private $cookieManagement;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param CookieManagementInterface $cookieManagement
     * @param RequestInterface $request
     */
    public function __construct(
        CookieManagementInterface $cookieManagement,
        RequestInterface          $request
    ) {
        $this->cookieManagement = $cookieManagement;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        $storeId = (int)$this->request->getParam('store');
        $allGroups = $this->cookieManagement->getGroups($storeId);
        $groups = [
            [
                'value' => self::NONE_GROUP_ID,
                'label' => __('None')
            ]
        ];

        foreach ($allGroups as $group) {
            $groups[] = ['value' => $group->getId(), 'label' => $group->getName()];
        }

        return $groups;
    }
}
