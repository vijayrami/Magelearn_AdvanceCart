<?php
namespace Magelearn\AdvanceCart\Model\Config\Source;

class Upselltype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Dynamic')],
            ['value' => '2', 'label' => __('Static')]
        ];
    }
}
