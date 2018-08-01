<?php
namespace Magenest\EmailNotifications\Observer\Email;

use Magento\Framework\Event\ObserverInterface;

use Magento\Framework\Event\Observer;


use Psr\Log\LoggerInterface;

use Magento\Framework\Registry;

class Email
{
    CONST PATH ='emailnotifications_config/';
    protected $_logger;

    protected $_coreRegistry;

    protected $_scopeConfig;

    protected $_transportBuilder;

    protected $_storeManager;

    protected $_reviewFactory;

    public function sendEmail($email)
    {
        switch ($email) {
            case "order":
                $path = $this::PATH . 'config_group_new_order/config_new_order_enable';
                break;
            case "coupon":
                $path = $this::PATH . 'config_group_new_coupon/config_new_coupon_enable';
                break;
            case "coupon_receive":
                $path = $this::PATH . 'config_group_new_coupon/config_new_coupon_receiver';
                break;
            case "coupon_template":
                $path = $this::PATH . 'config_group_new_coupon/config_new_coupon_template';
                break;
            case "coupon_sender":
                $path = $this::PATH . 'config_group_email_sender/config_email_sender';
                break;
            case "order_sender":
                $path = $this::PATH . 'config_group_new_order/config_new_order_receiver';
                break;
            case "order_template":
                $path = $this::PATH . 'config_group_new_order/config_new_order_template';
                break;
            case "email_sender";
                $path = $this::PATH . 'config_group_email_sender/config_email_sender';
                break;
            default:
                $path = "";
        }
        return $path;

    }

                public function send(Observer $observer,array $var)
            {
                $var = [];
                $orderId = $observer->getEvent()->getOrder()->getId();
                /** @var \Magento\Sales\Model\Order $orderModel */
                $orderModel = $this->_orderFactory->create();
                $order = $orderModel->load($orderId);
                $createdAt = $order->getCreatedAt();
                $couponCode = $order->getCouponCode();

                switch ($var){
                    case "order_code":
                        $email = ['orderId' => $orderModel->load($orderId)->getIncrementId(),
                            'created_at' => $createdAt,
                            'coupon_code' => $couponCode];
                            break;
                    case "coupon_order":
                        $email =  ['orderId' => $orderModel->load($orderId)->getIncrementId(),
                            'created_at' => $createdAt];
                        break;
                    default:
                        $email = [];
                }
               return $email;
            }


    public function registrationEmail($reg){
            switch ($reg){
                case "reg_enable":
                    $path = $this::PATH.'config_group_new_registration/config_new_registration_enable';
                    break;
                case "reg_receive":
                    $path = $this::PATH.'config_group_new_registration/config_new_registration_receiver';
                    break;
                case "reg_template":
                    $path = $this::PATH.'config_group_new_registration/config_new_registration_template';
                    break;
                case "email_sender":
                    $path = $this::PATH.'config_group_email_sender/config_email_sender';
                    break;
                    default;
                    $path = "";

            }
                return $path;
        }

        public function registration(Observer $observer,array $var){

                $var = [];
            $customer_name = $observer->getEvent()->getCustomer()->getFirstname().''.$observer->getEvent()->getCustomer()->getLastname();

            switch ($var){
                case "regis_customer":
                    $email = ['customerName' => $customer_name,
                        'customerEmail' => $observer->getEvent()->getCustomer()->getEmail()];
                    break;
                default:
                    $email = [];

            }
            return $email;
        }

        public function newreview($rv){
                switch ($rv){
                    case "rv_enable":
                        $path = $this::PATH.'config_group_new_review/config_new_review_enable';
                        break;
                    case "rv_receive":
                        $path = $this::PATH.'config_group_new_review/config_new_review_receiver';
                        break;
                    case "rv_template":
                        $path = $this::PATH.'config_group_new_review/config_new_review_template';
                        break;
                    case "email_sender":
                        $path = $this::PATH.'config_group_email_sender/config_email_sender';
                        break;
                        default;
                        $path = "";
                }
            return $path;
        }

        public function review(Observer $observer, array $var){
                $var = [];
            $reviewId = $observer->getObject()->getId();

            /** @var \Magento\Review\Model\Review $reviewModel */
            $reviewModel = $this->_reviewFactory->create();

            $detail =  $reviewModel->load($reviewId)->getDetail();
            $title = $reviewModel->load($reviewId)->getTitle();
            $productId = $reviewModel->load($reviewId)->getEntityPkValue();
            $nickname = $reviewModel->load($reviewId)->getNickname();

            switch ($var){
                case "review":
                    $email = ['nickname' => $nickname,
                        'productId' => $productId,
                        'store' => $this->_storeManager->getStore(),
                        'title' => $title,
                        'detail' => $detail];

                    break;
                default:
                    $email = [];
                }
                return $email;

        }
        public  function newstatus($ns){
                switch ($ns){
                    case "status_enable":
                        $path = $this::PATH.'config_group_new_orderstatus/config_new_orderstatus_enable';
                        break;
                    case "order_from":
                        $path = $this::PATH.'config_group_new_orderstatus/config_new_orderstatus_from';
                        break;
                    case "order_to":
                        $path = $this::PATH.'config_group_new_orderstatus/config_new_orderstatus_to';
                        break;
                    case "status_recevier":
                        $path = $this::PATH.'config_group_new_orderstatus/config_new_orderstatus_recevier';
                        break;
                    case "status_template":
                        $path = $this::PATH.'config_group_new_orderstatus/config_new_orderstatus_template';
                        break;
                    case "email_sender":
                        $path = $this::PATH.'config_group_email_sender/config_email_sender';
                        break;
                    default:
                        $path = "";
                }
            return $path;
        }

        public function subscription($sub){

                switch ($sub){
                    case "sub_enable":
                        $path=$this::PATH.'config_group_new_subscription/config_new_subscription_enable';
                        break;
                    case "sub_recevier":
                        $path=$this::PATH.'config_group_new_subscription/config_new_subscription_receiver';
                        break;
                    case "sub_template":
                        $path = $this::PATH.'config_group_new_subscription/config_new_subscription_template';
                        break;
                    case "email_sender":
                        $path = $this::PATH.'config_group_email_sender/config_email_sender';
                        break;
                    default:
                        $path = "";

                }
            return $path;
        }

        public function sub(Observer $observer,array $var){
                $var = [];
            $subscriber = $observer->getEvent()->getSubscriber();
            $customerId = $subscriber->getCustomerId();
            $customer = $this->_customerFactory->create()->load($customerId);
            $customerName = $customer->getName();
            $customerEmail = $customer->getEmail();
                switch ($var)
                {
                    case "sub_customer":
                        $email = [
                            'customerName' => $customerName,
                            'customerEmail' => $customerEmail,
                        ];
                        break;
                    case "unsub_customer":
                        $email = [
                            'customerName' => $customerName,
                            'customerEmail' => $customerEmail,
                        ];
                        break;
                    default:
                        $email = [];

                }
                return $email;
        }

        public function unsubscription($unsub){
                switch ($unsub){
                    case "unsub_enable":
                        $path = $this::PATH.'config_group_new_unsubscription/config_new_unsubscription_enable';
                        break;
                    case "unsub_recevier":
                        $path = $this::PATH.'config_group_new_unsubscription/config_new_unsubscription_recevier';
                        break;
                    case "unsub_template":
                        $path = $this::PATH.'config_group_new_unsubscription/config_new_unsubscription_template';
                        break;
                    case "email_sender":
                        $path = $this::PATH.'config_group_email_sender/config_email_sender';
                        break;
                    default:
                        $path = "";
                }
            return $path;
        }

        public function wishlist($wl){
                switch ($wl){
                    case "wishlist_enable":
                        $path = $this::PATH.'config_group_new_wishlist/config_new_wishlist_enable';
                        break;
                    case "wishlist_receiver":
                        $path = $this::PATH.'config_group_new_wishlist/config_new_wishlist_receiver';
                        break;
                    case "wishlist_template":
                        $path = $this::PATH.'config_group_new_wishlist/config_new_wishlist_template';
                        break;
                    case "email_sender":
                        $path = $this::PATH.'config_group_email_sender/config_email_sender';
                        break;
                    default:
                        $path = "";
                }
            return $path;
        }

        public  function addwishlist(Observer $observer,array $var){
                $var = [];
            $productName = $observer->getEvent()->getProduct()->getName();
            $customerId = $observer->getEvent()->getWishlist()->getCustomerId();
            /** @var \Magento\Customer\Model\Customer $customerModel */
            $customer = $this->_customerFactory->create()->load($customerId);
            $customerName = $customer->getName();
            $customerEmail = $customer->getEmail();
            switch ($var){
                case "addwish":
                    $email = ['customerName' => $customerName,
                        'customerEmail' => $customerEmail,
                        'productName' => $productName,
                        'store' => $this->_storeManager->getStore()];
                    break;
                default:
                    $email = [];

            }
            return $email;
        }
    public function __construct(
        LoggerInterface $loggerInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry,
        \Magento\Review\Model\ReviewFactory $reviewFactory
    )
    {
        $this->_logger = $loggerInterface;
        $this->_scopeConfig = $scopeConfig;
        $this->_coreRegistry = $registry;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_reviewFactory = $reviewFactory;
    }


}

?>