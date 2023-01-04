<?php
namespace VendorName\ModuleName\Model;

use VendorName\ModuleName\Api\Data\SampledateInterface;
use Magento\Framework\DataObject\IdentityInterface;

class SampleModel extends \Magento\Framework\Model\AbstractModel implements DeliverydateInterface, IdentityInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'sampleupdate_sampleupdate';

    /**
     * @var string
     */
    protected $_cacheTag = 'sampleupdate_sampleupdate';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sampleupdate_sampleupdate';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('VendorName\ModuleName\Model\ResourceModel\Sampledate');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get quote_id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * Get order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Get timerange
     *
     * @return string
     */
    public function getTimerange()
    {
        return $this->getData(self::TIMERANGE);
    }

    /**
     * Set id
     *
     * @param int $id
     * @return \VendorName\ModuleName\Api\Data\DeliverydateInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set quote_id
     *
     * @param int $quoteId
     * @return \VendorName\ModuleName\Api\Data\DeliverydateInterface
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * Set order_id
     *
     * @param int $orderId
     * @return \VendorName\ModuleName\Api\Data\DeliverydateInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return \VendorName\ModuleName\Api\Data\DeliverydateInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Set date
     *
     * @param string $date
     * @return \VendorName\ModuleName\Api\Data\DeliverydateInterface
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * Set timerange
     *
     * @param string $timerange
     * @return \VendorName\ModuleName\Api\Data\DeliverydateInterface
     */
    public function setTimerange($timerange)
    {
        return $this->setData(self::TIMERANGE, $timerange);
    }

    /**
     * Load by quote id
     *
     * @param   int quote id
     * @return  $this
     */
    public function loadByQuoteId($quoteId)
    {
        $this->_getResource()->loadByQuoteId($this, $quoteId);
        return $this;
    }

    /**
     * Load by order id
     *
     * @param   int order id
     * @return  $this
     */
    public function loadByOrderId($orderId)
    {
        $this->_getResource()->loadByOrderId($this, $orderId);
        return $this;
    }

    /**
     * Load by order id and quote id
     *
     * @param   int order id
     * @param   int quote id
     * @return  $this
     */
    public function loadByOrderIdAndQuoteId($orderId, $quoteId)
    {
        $this->_getResource()->loadByOrderIdAndQuoteId($this, $orderId, $quoteId);
        return $this;
    }
}
