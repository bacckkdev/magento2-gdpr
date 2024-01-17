<?php

namespace Redepy\GDPR\Block\Adminhtml\Fields;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element) {
        $output = parent::_getElementHtml($element);
        $output .= "
		<script type='text/javascript'>
			require([
				'jquery'
			], function(jQuery){
				(function($) {
					$('#" . $element->getHtmlId() . "').attr('data-hex', true).mColorPicker();
				})(jQuery);
			});
		</script>";
        return $output;
    }
}
