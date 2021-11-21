<?php
namespace Magelearn\AdvanceCart\Block\Cart;

/**
 * Upsell content block
 */
class Upsell extends \Magento\Framework\View\Element\Template
{
    protected $_productCollectionFactory;
    protected $_imageHelper;
    protected $_cartHelper;
    protected $_imageBuilder;
    protected $_scopeConfig;
    protected $_productRepository;
    protected $_checkoutSession;
    protected $_cart;
    protected $_storeScope;
    protected $_priceHelper;
    protected $_formKey;
	protected $_logger;
    
    const XML_PATH_KEY = 'advancecart_setting/minicart_settings/';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\Context $productcontext,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_imageHelper = $productcontext->getImageHelper();
        $this->_imageBuilder = $productcontext->getImageBuilder();
        $this->_cartHelper = $productcontext->getCartHelper();
        $this->_scopeConfig = $scopeConfig;
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->_priceHelper = $priceHelper;
        $this->_formKey = $formKey;
		$this->_logger = $logger;

        parent::__construct($context);
    }
    
    public function getTitle()
    {
        $upsell_title = $this->_scopeConfig->getValue(self::XML_PATH_KEY.'upsell_title', $this->_storeScope);
        return $upsell_title;
    }
    public function getProductCollection()
    {
        $upsell_limit = $this->_scopeConfig->getValue(self::XML_PATH_KEY.'upsell_limit', $this->_storeScope);
        $upsell_sku = $this->_scopeConfig->getValue(self::XML_PATH_KEY.'upsell_sku', $this->_storeScope);
        $upsell_type = $this->_scopeConfig->getValue(self::XML_PATH_KEY.'upsell_type', $this->_storeScope);
        
        $ids = [];
        $productInfo = $this->_cart->getQuote()->getItemsCollection();
		foreach ($productInfo as $item){
       		$ids[] = $item['product_id'];
    	}
		
        $sku = [];
		
        if ($upsell_sku != '') {
            if (strpos($upsell_sku, ',') !== false) {
                $sku1 = explode(',', $upsell_sku);
                foreach ($sku1 as $skuchild) {
                    if (trim($skuchild) != '') {
                        $sku[] = trim($skuchild);
                    }
                }
            } else {
                $sku = [trim($upsell_sku)];
            }
        }
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFilter('type_id', 'simple');
        try {
            if ($upsell_type == '1') {
                $ids = $this->getUpsellProductID();
                if (count($ids) > 0) {
                    $collection->addFieldToFilter('entity_id', ['in' => $ids]);
                }
            } else {
                if (count($sku) > 0) {
                    $collection->addFieldToFilter('sku', ['in' => $sku]);
                }
            }

        } catch (\Exception $e) {
            return false;
        }

        $collection->setPageSize($upsell_limit); // fetching only limited products
        return $collection;
    }
    
    public function getUpsellProductID()
    {
        $upsellProduct = [];
        try {
            $productInfo = $this->_cart->getQuote()->getItemsCollection();
            if (count($productInfo) == 0) {
                return $upsellProduct;
            }
            foreach ($productInfo as $item) {
                $product = $this->_productRepository->getById($item->getProductId());
                $upsell = $product->getUpSellProducts();
                if (count($upsell)) {
                    foreach ($upsell as $item) {
                        $upsellProduct[$item->getProductId()][] = $item->getId();
                    }
                }
            }
            if (count($upsellProduct) == 0) {
                return $upsellProduct;
            }
            $finalarray = [];
            $cnt = 0;
            for ($i=0; $i<1000; $i++) {
                foreach ($upsellProduct as $upsellProduct1) {
                    foreach ($upsellProduct1 as $upsellProduct2) {
                        if (!in_array($upsellProduct2, $finalarray)) {
                            $finalarray[]= $array2;
                            break;
                        }
                    }
                }
                $i++;
                if (count($finalarray) >= $cnt) {
                    break;
                }
            }
            return $finalarray;
        } catch (\Exception $e) {
            return $upsellProduct;
        }
        return $upsellProduct;
    }
    public function hasProductUrl($product)
    {
		if(strtolower($product->getAttributeText('visibility')) == 'not visible individually') {
			return false;
		} else {
			return true;
		}
		
        return false;
    }
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->_imageBuilder->create($product, $imageId, $attributes);
    }
    public function getAddToCartUrl($product, $additional = [])
    {
        if (!$product->getTypeInstance()->isPossibleBuyFromList($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }
        return $this->_cartHelper->getAddUrl($product, $additional);
    }
    public function getFormattedCurrency($price)
    {
        return $this->_priceHelper->currency($price, true, false);
    }
    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }
}
