<?php

namespace VendorName\ModuleName\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class SampleHelper extends AbstractHelper
{
    protected $_scopeConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \VendorName\ModuleName\Model\SlicepostFactory $slicePostFactory,
        \VendorName\ModuleName\Model\PosteduserFactory $postedUserFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesRule\Model\RuleFactory $ruleCollection,
        \Magento\Customer\Model\SessionFactory $session,
         \VendorName\ModuleName\Model\FrequencyFactory $frequency,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->slicePostFactory = $slicePostFactory;
        $this->ruleCollection = $ruleCollection;
        $this->postedUserFactory = $postedUserFactory;
        $this->ruleRepository = $ruleRepository; 
        $this->frequencyFactory  = $frequency;
       $this->_customerSession = $session;
    }
    public function getCustomerFreq(){
        $ip = $_SERVER['REMOTE_ADDR'];
        $model = $this->frequencyFactory->create();
        $customerSession = $this->_customerSession->create();
            if ($customerSession->isLoggedIn()) {
                $userId   = $customerSession->getCustomer()->getId();
                $frequncy = $model->getCollection()->addFieldToFilter('user_id', $userId);
            } else {
                 $frequncy = $model->getCollection()->addFieldToFilter('user_ip', $ip);
            }
        return $frequncy;
    }

    public function getConfig($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getBaseUrl(){
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getMediaUrl(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getsliceById($id){
        $model = $this->slicePostFactory->create();
        return $collection = $model->load($id);
    }
    public function getSliceCollection(){
        $collection = $this->slicePostFactory->create()->getCollection();
        return $collection;
    }

    public function getUserCollection(){
        $collection = $this->postedUserFactory->create()->getCollection();
        return $collection;
    }
    public function getRuleCollection(){
        $rules = $this->ruleCollection->create()->getCollection()->addFieldToFilter('is_active', 1);

        return $rules;   
    }
    public function getRuleById($Id){
        $rule = $this->ruleRepository->getById($Id);
        return $rule;
    }
}