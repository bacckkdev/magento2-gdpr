<?php

declare(strict_types=1);

namespace Redepy\GDPR\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Redepy\GDPR\Model\CookieFactory;
use Redepy\GDPR\Model\CookieGroupFactory;
use Redepy\GDPR\Model\Repository\CookieGroupsRepository;
use Redepy\GDPR\Model\Repository\CookieRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class UpgradeData implements UpgradeDataInterface
{
    private $cookiesByGroups = [
        'Essential' => [
            'Cookies' => [
                'PHPSESSID' => [
                    'Description' => "To store the logged in user's username and a 128bit encrypted key."
                        . "This information is required to allow a user to stay logged in to a web site"
                        . "without needing to submit their username and password for each page visited."
                        . " Without this cookie, a user is unabled to proceed to areas of the web site"
                        . " that require authenticated access.",
                ],
                'private_content_version' => [
                    'Description' => "Appends a random, unique number and time to pages with customer content"
                        . " to prevent them from being cached on the server.",
                ],
                'persistent_shopping_cart' => [
                    'Description' => "Stores the key (ID) of persistent cart to make it possible to"
                        . " restore the cart for an anonymous shopper.",
                ],
                'form_key' => [
                    'Description' => "A security measure that appends a random string to all form submissions"
                        . " to protect the data from Cross-Site Request Forgery (CSRF).",
                ],
                'store' => [
                    'Description' => "Tracks the specific store view / locale selected by the shopper.",
                ],
                'login_redirect' => [
                    'Description' => "Preserves the destination page the customer was navigating"
                        . " to before being directed to log in.",
                ],
                'mage-messages' => [
                    'Description' => "Tracks error messages and other notifications that are shown to the user,"
                        . " such as the cookie consent message, and various error messages, The message is"
                        . " deleted from the cookie after it is shown to the shopper.",
                ],
                'mage-cache-storage' => [
                    'Description' => "Local storage of visitor-specific content that enables e-commerce functions.",
                ],
                'mage-cache-storage-section-invalidation' => [
                    'Description' => "Forces local storage of specific content sections"
                        . " that should be invalidated.",
                ],
                'mage-cache-sessid' => [
                    'Description' => "The value of this cookie triggers the cleanup of local cache storage.",
                ],
                'product_data_storage' => [
                    'Description' => "Stores configuration for product data related to Recently Viewed"
                        . " / Compared Products.",
                ],
                'user_allowed_save_cookie' => [
                    'Description' => "Indicates if the shopper allows cookies to be saved.",
                ],
                'mage-translation-storage' => [
                    'Description' => "Stores translated content when requested by the shopper.",
                ],
                'mage-translation-file-version' => [
                    'Description' => "Stores the file version of translated content.",
                ],
                'section_data_ids' => [
                    'Description' => "Stores customer-specific information related to shopper-initiated actions"
                        . " such as display wish list, checkout information, etc.",
                ]
            ],
            'Description' => "Necessary cookies enable core functionality of the website. Without these"
                . " cookies the website can not function properly. They help to make a website usable"
                . " by enabling basic functionality.",
            'Essential' => true,
            'Enabled' => true
        ],
        'Marketing' => [
            'Cookies' => [
                'recently_viewed_product' => [
                    'Description' => "Stores product IDs of recently viewed products for easy navigation.",
                ],
                'recently_viewed_product_previous' => [
                    'Description' => "Stores product IDs of recently previously viewed"
                        . " products for easy navigation.",
                ],
                'recently_compared_product' => [
                    'Description' => "Stores product IDs of recently compared products.",
                ],
                'recently_compared_product_previous' => [
                    'Description' => "Stores product IDs of previously compared"
                        . " products for easy navigation.",
                ]
            ],
            'Description' => "Marketing cookies are used to track and collect visitors actions"
                . " on the website. Cookies store user data and behaviour information, which"
                . " allows advertising services to target more audience groups. Also more customized"
                . " user experience can be provided according to collected information.",
            'Essential' => false,
            'Enabled' => true
        ],
        'Google Analytics' => [
            'Cookies' => [
                '_ga' => [
                    'Description' => "Used to distinguish users.",
                ],
                '_gid' => [
                    'Description' => "Used to distinguish users.",
                ],
                '_gat' => [
                    'Description' => "Used to throttle request rate.",
                ]
            ],
            'Description' => "A set of cookies to collect information and report about website usage statistics"
                . " without personally identifying individual visitors to Google.",
            'Essential' => false,
            'Enabled' => true
        ]
    ];

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    /**
     * @var CookieGroupsRepository
     */
    private $cookieGroupsRepository;

    /**
     * @var CookieFactory
     */
    private $cookieFactory;

    /**
     * @var CookieRepository
     */
    private $cookieRepository;

    /**
     * @param CookieGroupFactory $cookieGroupFactory
     * @param CookieGroupsRepository $cookieGroupsRepository
     * @param CookieFactory $cookieFactory
     * @param CookieRepository $cookieRepository
     */
    public function __construct(
        CookieGroupFactory     $cookieGroupFactory,
        CookieGroupsRepository $cookieGroupsRepository,
        CookieFactory          $cookieFactory,
        CookieRepository       $cookieRepository,
    ) {
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->cookieGroupsRepository = $cookieGroupsRepository;
        $this->cookieFactory = $cookieFactory;
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * @param $isUpdate
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function addCookieInformation($isUpdate = false) {
        foreach ($this->cookiesByGroups as $groupName => $groupData) {
            if (!$isUpdate) {
                $cookieGroup = $this->cookieGroupFactory->create();
                $cookieGroup->setName($groupName);
                $cookieGroup->setDescription($groupData['Description']);
                $cookieGroup->setIsEnabled($groupData['Enabled']);
                $cookieGroup->setIsEssential($groupData['Essential']);

                $this->cookieGroupsRepository->save($cookieGroup);
                $groupId = $cookieGroup->getId();
            }

            foreach ($groupData['Cookies'] as $name => $cookieData) {
                if (!$isUpdate) {
                    $cookie = $this->cookieFactory->create();
                    $cookie->setName($name);
                    $cookie->setDescription($cookieData['Description']);
                    $cookie->setGroupId($groupId);
                } else {
                    try {
                        $cookie = $this->cookieRepository->getByName($name);
                    } catch (NoSuchEntityException $e) {
                        continue;
                    }
                }

                $this->cookieRepository->save($cookie);
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->addCookieInformation();
        }
        $setup->endSetup();
    }
}
