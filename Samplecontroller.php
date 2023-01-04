<?php
namespace VendorName\ModuleName\Controller\Wheel;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Samplecontroller extends \Magento\Framework\App\Action\Action
{
     public function __construct(
         \Magento\Framework\App\Action\Context $context,
         TransportBuilder $transportBuilder,
         StoreManagerInterface $storeManager,
         StateInterface $state,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         \VendorName\ModuleName\Model\PosteduserFactory $posteduserFactory,
         \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
         \Magento\SalesRule\Api\CouponRepositoryInterface $couponRepository,
         \VendorName\ModuleName\Model\SlicepostFactory $slicePostFactory,
         \VendorName\ModuleName\Model\FrequencyFactory $frequency,
         \Magento\SalesRule\Api\Data\CouponInterface $coupon,
         \Magento\Customer\Model\SessionFactory $session,
         \Magento\SalesRule\Model\CouponFactory $couponColl
     ){
        parent::__construct($context);
        $this->couponColl        = $couponColl;
        $this->scopeConfig       = $scopeConfig;
        $this->transportBuilder  = $transportBuilder;
        $this->storeManager      = $storeManager;
        $this->inlineTranslation = $state;
        $this->posteduserFactory = $posteduserFactory;
        $this->resultJsonFactory = $resultJsonFactory; 
        $this->coupon            = $coupon;
        $this->couponRepository  = $couponRepository; 
        $this->frequencyFactory  = $frequency;
        $this->_customerSession  = $session;
        $this->_slicePostFactory = $slicePostFactory;
    }
     public function execute(){

         $post =$this->getRequest()->getPostValue();
         $result       = $this->resultJsonFactory->create();
         $postUserData = $this->posteduserFactory->create();
         $model        = $this->_slicePostFactory->create();
         if(isset($post['first_name'])){ $postUserData->setFirstName($post['first_name']); }
         if(isset($post['last_name'])){ $postUserData->setLastName($post['last_name']); }
         if(isset($post['email'])){ $postUserData->setEmail($post['email']); }
         if(isset($post['slice_id'])){ 
            $sliceData = $model->load($post['slice_id']);
            $sliceLabel = $sliceData->getSliceLabel();
            $postUserData->setSliceLabel($sliceLabel);
        }
        try {
                $postUserData->save();
                $this->getSaveFrequency();
                if (isset($post['slice_id']) && !empty($post['slice_id'])){            
                        $sliceData = $model->load($post['slice_id']);
                        $ruleId = $sliceData->getRuleId();
                        $sliceLabel = $sliceData->getSliceLabel();
                        $couponCollection = $this->couponColl->create()->getCollection()->addFieldToFilter('rule_id', $ruleId);
                        if(count($couponCollection) > 0){
                        foreach($couponCollection as $item){
                            if($item->getUsageLimit() >= $item->getTimesUsed() ){
                                $coupon = $item->getCode();
                                break;
                            }else{
                                $coupon = "couponnotavailable";
                                }
                            }
                        }else{
                            $coupon = "couponnotavailable";
                        }
                    }


                if($coupon != "couponnotavailable"){
                $this->sendEmail($post['email'], $coupon, $post['first_name'], $post['last_name'], $sliceLabel);
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        if ($this->getRequest()->isAjax()) 
        {
            $return=Array
            (
                'coupon' => $coupon,
                'Post_Slice_label' => $sliceLabel
            );
            return $result->setData($return);
        }    
    }

    private function getSaveFrequency(){
        $ip         = $_SERVER['REMOTE_ADDR'];
        $currentTime= date('d-m-Y H:i:s');
        $frequency  = $this->frequencyFactory->create();
        $customerSession = $this->_customerSession->create();

            if ($customerSession->isLoggedIn()) {
                $userId   = $customerSession->getCustomer()->getId();
                $frequncyColl = $frequency->getCollection()->addFieldToFilter('user_id', $userId);
                if(count($frequncyColl)==0){
                    $frequency->setUserId($userId);
                    $frequency->setCreatedAt($currentTime);
                    $frequency->setUpdatedAt($currentTime);
                }else{
                    foreach($frequncyColl as $item){ $userEntityId = $item->getEntityId(); }
                    $UpdateFreq = $frequency->load($userEntityId);;
                    $UpdateFreq->setUpdatedAt($currentTime);
                    $UpdateFreq->save();

                }
            } else {
                 $frequncyColl = $frequency->getCollection()->addFieldToFilter('user_ip', $ip);
                 if(count($frequncyColl)==0){
                    $frequency->setUserIp($ip);
                    $frequency->setCreatedAt($currentTime);
                    $frequency->setUpdatedAt($currentTime);
                }else{
                    foreach($frequncyColl as $item){ $userEntityId = $item->getEntityId(); }
                    $UpdateFreq = $frequency->load($userEntityId);;
                    $UpdateFreq->setUpdatedAt($currentTime);
                    $UpdateFreq->save();

                }
            }
        $frequency->save();

    } 

    private function createCoupon(int $ruleId) {
        /** @var CouponInterface $coupon */
        $couponUnquieCode=$this->generateRandomString();
        $coupon = $this->coupon;
        $coupon->setCode($couponUnquieCode)->setCreatedAt(date('Y-m-d H:i:s'))->setRuleId($ruleId);
        $coupon = $this->couponRepository->save($coupon);
        $shoppingCartRuleData = $this->couponRepository->getById($coupon->getCouponId());
        $shoppingCartRuleData->setType(1);
        $shoppingCartRuleData->setUsageLimit(1);
        $shoppingCartRuleData->setUsagePerCustomer(1);
        $shoppingCartRuleData->save();
        return $couponUnquieCode;
    }

    public function generateRandomString($length = 12) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
}
