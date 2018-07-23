<?php
namespace Magenest\EmailNotifications\Observer\Wishlist;


use Magenest\EmailNotifications\Observer\Email\Email;

class WishlistAddProduct extends Email
{

    public function execute(Observer $observer)
    {
        $productName = $observer->getEvent()->getProduct()->getName();
        $customerId = $observer->getEvent()->getWishlist()->getCustomerId();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customer = $this->_customerFactory->create()->load($customerId);
        $customerName = $customer->getName();
        $customerEmail = $customer->getEmail();
        $enable = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_wishlist/config_new_wishlist_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable == 'yes') {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_wishlist/config_new_wishlist_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails =explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_wishlist/config_new_wishlist_template',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customerName' => $customerName,
                            'customerEmail' => $customerEmail,
                            'productName' => $productName,
                            'store' => $this->_storeManager->getStore()
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            'emailnotifications_config/config_group_email_sender/config_email_sender',
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
