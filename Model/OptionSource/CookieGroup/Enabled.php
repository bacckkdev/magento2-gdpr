<?php

namespace Redepy\GDPR\Model\OptionSource\CookieGroup;

use Magento\Framework\Option\ArrayInterface;

class Enabled implements ArrayInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @return array[]
     */
    public function toOptionArray() {
        return [
            ['value' => self::DISABLED, 'label' => __('No')],
            ['value' => self::ENABLED, 'label' => __('Yes')]
        ];
    }
}
