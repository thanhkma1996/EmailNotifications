<?php
namespace Magenest\EmailNotifications\Observer\NewOrder;

use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;
class NewOrder extends Email
{

    


    public function execute(Observer $observer)
    {
        $orderId=$observer->getEvent()->getOrder()->getId();
        /** @var \Magento\Sales\Model\Order $orderModel */
        $orderModel = $this->_orderFactory->create();
        $order = $orderModel->load($orderId);
        $createdAt = $order->getCreatedAt();
        $couponCode = $order->getCouponCode();
        $enable = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_order/config_new_order_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $enableCoupon = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_coupon/config_new_coupon_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enableCoupon == 'yes' && $couponCode) {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_coupon/config_new_coupon_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails =explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_coupon/config_new_coupon_template',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'orderId' => $orderModel->load($orderId)->getIncrementId(),
                            'created_at' => $createdAt,
                            'coupon_code' => $couponCode
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

        if ($enable == 'yes') {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_order/config_new_order_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails = explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_order/config_new_order_template',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'orderId' => $orderModel->load($orderId)->getIncrementId(),
                            'created_at' => $createdAt,
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
