<?php

namespace Redepy\GDPR\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class AbstractCookie extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Redepy_GDPR::cookies';
}
