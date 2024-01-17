<?php

namespace Redepy\GDPR\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class CookieGroup extends AbstractDb
{
    /**
     * @var int
     */
    private $storeId = 0;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function _construct() {
        $this->_init('redepy_gdprcookie_group', 'id');
    }

    /**
     * @param int $storeId
     * @return void
     */
    public function setStoreId(int $storeId) {
        $this->storeId = $storeId;
    }
}
