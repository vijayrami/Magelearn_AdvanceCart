<?php
namespace Magelearn\AdvanceCart\Plugin;
use Magento\Quote\Model\Quote\Item;

class DefaultItem
{
    public function aroundGetItemData($subject, \Closure $proceed, Item $item)
    {
        $data = $proceed($item);
        $atts = [];
        
        $product_desc = $item->getProduct()->getShortDescription(); 
 
        $atts = [
            "short_description" => $product_desc
        ];
 
        return array_merge($data, $atts);
    }
}
