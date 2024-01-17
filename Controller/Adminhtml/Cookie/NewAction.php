<?php

namespace Redepy\GDPR\Controller\Adminhtml\Cookie;

use Redepy\GDPR\Controller\Adminhtml\AbstractCookie;

class NewAction extends AbstractCookie
{
    /**
     * @return void
     */
    public function execute() {
        $this->_forward('edit');
    }
}
