<?php

namespace Magenest\EmailNotifications\Observer\NewStatus;


use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;

class OrderSaveAfter extends Email
{
    CONST Status_enable = "status_enable";
    CONST Order_from = "order_from";
    CONST Order_to = "order_to";
    CONST Status_receiver = "status_receiver";
    CONST Status_template = "status_template";
    CONST Email_sender = "email_sender";

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $statusBefore =  $order->getOrigData('status');
        $statusAfter = $order->getStatus();

        $enable = $this->_scopeConfig->getValue(
            self::Status_enable,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable == 'yes') {
            $from = $this->_scopeConfig->getValue(
                self::Order_from,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $to = $this->_scopeConfig->getValue(
                self::Order_to,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ((strpos($from, $statusBefore) !== false) && (strpos($to, $statusAfter) !== false)) {
                $receiverList = $this->_scopeConfig->getValue(
                    self::Status_receiver,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                foreach ($receiverList as $receiverEmail) {
                    $template_id = $this->_scopeConfig->getValue(
                        self::Status_template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'orderId' => $order->getIncrementId(),
                            'updated_at' => $order->getUpdatedAt(),
                            'statebefore' => $from,
                            'stateafter' => $to

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
                }
            }
        }

    }
}
