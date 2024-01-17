<?php

namespace Redepy\GDPR\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class AbstractCookieGroup extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Redepy_GDPR::cookie_group';
}
