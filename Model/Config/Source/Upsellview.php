<?php
namespace Magelearn\AdvanceCart\Model\Config\Source;

class Upsellview implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Slider View')],
            ['value' => '2', 'label' => __('Scroll View')]
        ];
    }
}
