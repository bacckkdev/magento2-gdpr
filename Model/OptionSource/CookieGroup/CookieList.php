<?php

declare(strict_types=1);

namespace Redepy\GDPR\Model\OptionSource\CookieGroup;

use Redepy\GDPR\Model\Cookie\CookieBackend;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;

class CookieList implements OptionSourceInterface
{
    /**
     * @var CookieBackend
     */
    private $cookieBackend;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param CookieBackend $cookieBackend
     * @param RequestInterface $request
     */
    public function __construct(
        CookieBackend    $cookieBackend,
        RequestInterface $request
    ) {
        $this->cookieBackend = $cookieBackend;
        $this->request = $request;
    }

    /**
     * @return array|array[]
     */
    public function toOptionArray() {
        return array_map(function ($cookie) {
            return [
                'value' => $cookie->getId(),
                'label' => $cookie->getName(),
            ];
        }, $this->toArray());
    }

    public function toArray(): array {
        $storeId = (int)$this->request->getParam('store');

        return $this->cookieBackend->getCookies($storeId);
    }
}
