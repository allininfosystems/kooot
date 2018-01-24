<?php
namespace Allin\Countryclass\Model\Plugin;

class AttributeMergerPlugin
{
    public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
    {
        if (array_key_exists('country_id', $result)) {
            $result['country_id']['additionalClasses'] = 'your_custom_class';
        }

        return $result;
    }
}