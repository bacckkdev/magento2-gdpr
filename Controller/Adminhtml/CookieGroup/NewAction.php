<?php

namespace Redepy\GDPR\Controller\Adminhtml\CookieGroup;

use Redepy\GDPR\Controller\Adminhtml\AbstractCookieGroup;

class NewAction extends AbstractCookieGroup
{
    /**
     * @return void
     */
    public function execute() {
        $this->_forward('edit');
    }
}
