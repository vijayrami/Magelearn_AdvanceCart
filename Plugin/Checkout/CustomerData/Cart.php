<?php
namespace Magelearn\AdvanceCart\Plugin\Checkout\CustomerData;

use Magelearn\AdvanceCart\Helper\Data as helperData;

class Cart
{
	/**
     * @var Magelearn\AdvanceCart\Helper\Data
     */
    protected $helperData;
	
    protected $layoutFactory;
	
	protected $checkoutSession;
    protected $checkoutHelper;
    protected $quote;
	/**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;
	
	/**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;
	
	/**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
	
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
	/**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
	protected $_logger;
	
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        helperData $helperData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\UrlInterface $urlInterface,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->layoutFactory = $layoutFactory;
		$this->helperData = $helperData;
		$this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
		$this->cartTotalRepository = $cartTotalRepository;
		$this->priceHelper = $priceHelper;
		$this->quoteIdMaskFactory = $quoteIdMaskFactory;
		$this->customerSession = $customerSession;
		$this->urlInterface = $urlInterface;
		$this->_logger = $logger;
    }

    /**
     * Add grand total to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
    	$upsell_view = $this->helperData->getGeneralConfig('upsell_view');
		if($upsell_view == 1) {
			$upselldata = $this->layoutFactory->create()
	          ->createBlock(\Magelearn\AdvanceCart\Block\Cart\Upsell::class)
	          ->setTemplate('Magelearn_AdvanceCart::minicart/slider.phtml')
	          ->toHtml();
	        $result['advancecart']['upsell_data'] = $upselldata;
		} else {
	        $upselldata = $this->layoutFactory->create()
	          ->createBlock(\Magelearn\AdvanceCart\Block\Cart\Upsell::class)
	          ->setTemplate('Magelearn_AdvanceCart::minicart/grid.phtml')
	          ->toHtml();
	        $result['advancecart']['upsell_data'] = $upselldata;
		}
		
		$result['advancecart']['coupon_enable'] = boolval($this->helperData->getGeneralConfig('coupon'));
        $result['advancecart']['totals_enable'] = boolval($this->helperData->getGeneralConfig('totals'));
        $result['advancecart']['totals_title'] = $this->helperData->getGeneralConfig('totals_title');
		
		$coupon_code = $this->getQuote()->getCouponCode();
		$result['advancecart']['coupon_code'] = ($coupon_code != '' ? $coupon_code : null);
		
		$quoteId = $this->getQuote()->getId();
		$quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
		
		if(isset($quoteId) && $quoteId != null) {
			$totals = $this->cartTotalRepository->get($quoteId)->getTotalSegments();
		} else {
			$totals = '';
		}
		
		$_totals = [];
		
		if(isset($totals) && $totals != '') {
	        foreach ($totals as $key => $value) {
	            $_totals[] = [
	                'title' => $value->getTitle(),
	                'value' => $this->priceHelper->currency($value->getValue(), true, false),
	            ];
	        }
        }
		
		$result['advancecart']['totals'] = $_totals;
		$result['advancecart']['grand_total'] = isset($totals['grand_total'])
            ? $this->priceHelper->currency($totals['grand_total']->getValue(), true, false)
            : 0;
			
		$isLoggedIn = $this->customerSession->isLoggedIn();
		$result['advancecart']['isLoggedIn'] = ($isLoggedIn != '' ? true : false);
		$result['advancecart']['apiUrl'] = $this->urlInterface->getBaseUrl().'rest/default/V1/';
		$result['advancecart']['quoteId'] = $quoteIdMask->getMaskedId();
        
        return $result;
    }
	/**
     * Get active quote.
     *
     * @return Quote
     */
    protected function getQuote()
    {
        if ($this->quote === null) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }
}
