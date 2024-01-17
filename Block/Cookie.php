<?php

namespace Redepy\GDPR\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Redepy\GDPR\Api\CookieManagementInterface;

class Cookie extends Template
{
    public const IS_ENABLED_PATH = 'gdprcookies/general/enabled';
    public const GENERAL_PATH = 'gdprcookies/general/';
    public const DESIGN_PATH = 'gdprcookies/design/';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var CookieManagementInterface
     */
    private $cookieManagement;

    /**
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param CookieManagementInterface $cookieManagement
     * @param array $data
     */
    public function __construct(
        Template\Context          $context,
        ScopeConfigInterface      $scopeConfig,
        CookieManagementInterface $cookieManagement,
        array                     $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->cookieManagement = $cookieManagement;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function enabled() {
        return $this->_scopeConfig->getValue(self::IS_ENABLED_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return false|string
     */
    public function getJsConfig($message) {
        $jsonConfig = [
            'palette' => [
                'popup' => [
                    'background' => $this->getDesignValue('popup_background_color'),
                    'text' => $this->getDesignValue('popup_text_color'),
                ],
                'button' => [
                    'background' => $this->getDesignValue('button_color'),
                    'text' => $this->getDesignValue('button_text_color')
                ],
            ],
            'type' => 'opt-in',
            'content' => [
                'message' => $message,
                'allow' => $this->getConfigValue('allow'),
                'deny' => $this->getConfigValue('deny'),
                'link' => "",
                'href' => "",
            ],
            'cookie_group' => $this->getGroupData()
        ];

        return json_encode($jsonConfig);
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getConfigValue($field) {
        return $this->_scopeConfig->getValue(self::GENERAL_PATH . $field, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getDesignValue($field) {
        return $this->_scopeConfig->getValue(self::DESIGN_PATH . $field, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getGroupData() {
        $groupData = [];
        $groups = $this->cookieManagement->getGroups();
        $cookies = $this->cookieManagement->getCookies();

        foreach ($groups as $group) {
            $groupData[$group->getId()] = [
                'groupId' => $group->getId(),
                'isEssential' => $group->getIsEssential(),
                'name' => $group->getName(),
                'description' => $group->getDescription(),
                'cookies' => []
            ];
        }

        if ($groupData) {
            foreach ($cookies as $cookie) {
                if (isset($groupData[$cookie->getGroupId()])) {
                    $groupData[$cookie->getGroupId()]['cookies'][] = [
                        'name' => $cookie->getName(),
                        'description' => $cookie->getDescription(),
                    ];
                }
            }
        }

        return $groupData;
    }
}
