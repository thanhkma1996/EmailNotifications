<?php
namespace Magenest\EmailNotifications\Observer\Email;

use Magento\Framework\Event\ObserverInterface;

use Magento\Framework\Event\Observer;

use Psr\Log\LoggerInterface;

use Magento\Framework\Registry;

class Email
{
    protected $_logger;

    protected $_coreRegistry;

    protected $_scopeConfig;

    protected $_transportBuilder;

    protected $_storeManager;

    protected $_reviewFactory;

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