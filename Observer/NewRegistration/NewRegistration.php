<?php

namespace Magenest\EmailNotifications\Observer\NewRegistration;


use Magenest\EmailNotifications\Observer\Email\Email;

class NewRegistration extends  Email
{

    public function execute(Observer $observer)
    {
        $enable = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_registration/config_new_registration_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable == 'yes') {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_registration/config_new_registration_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails = explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_registration/config_new_registration_template',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $customer_name = $observer->getEvent()->getCustomer()->getFirstname() . ' ' . $observer->getEvent()->getCustomer()->getLastname();
                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customerName' => $customer_name,
                            'customerEmail' => $observer->getEvent()->getCustomer()->getEmail()
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
