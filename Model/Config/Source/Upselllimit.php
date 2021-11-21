<?php
namespace Magelearn\AdvanceCart\Model\Config\Source;

class Upselllimit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach (range(1, 10) as $number) {
		    $options[] = ['value' => $number, 'label' => __($number)];
		}
        return $options;
    }
}
