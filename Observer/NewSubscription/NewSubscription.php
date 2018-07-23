<?php

namespace Magenest\EmailNotifications\Observer\NewSubscription;


use Magenest\EmailNotifications\Observer\Email\Email;

class NewSubscription extends Email
{

    public function execute(Observer $observer)
    {
        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $observer->getEvent()->getSubscriber();
        $status = $subscriber->getStatus();
        $isStatusChanged =$subscriber->isStatusChanged();
        $customerId = $subscriber->getCustomerId();
        $customer = $this->_customerFactory->create()->load($customerId);
        $customerName = $customer->getName();
        $customerEmail = $customer->getEmail();
        $enable = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_subscription/config_new_subscription_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable == 'yes' && $status == 1 && $isStatusChanged == true) {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_subscription/config_new_subscription_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails =explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_subscription/config_new_subscription_template',
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
        $enableUnsubscribe = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_unsubscription/config_new_unsubscription_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enableUnsubscribe == 'yes' && $status == 3 && $isStatusChanged == true) {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_unsubscription/config_new_unsubscription_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails =explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_unsubscription/config_new_unsubscription_template',
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
