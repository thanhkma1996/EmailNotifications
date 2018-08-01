<?php
namespace Magenest\EmailNotifications\Observer\Wishlist;


use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;

class WishlistAddProduct extends Email
{
    CONST Addwishlist = "addwish";
    CONST Wishlist_enable = "wishlist_enable";
    CONST Wishlist_receiver = "wishlist_receiver";
    CONST Wishlist_template = "wishlist_template";
    CONST Email_sender="email_sender";
    public function execute(Observer $observer)
    {
        $enable = $this->_scopeConfig->getValue(
            self::Wishlist_enable,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable == 'yes') {
            $receiverList = $this->_scopeConfig->getValue(
                self::Wishlist_receiver,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        self::Wishlist_template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                           self::Addwishlist
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            self::Email_sender,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $receiverEmail
                    )->getTransport();

                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }
            }
        }
    }
}
