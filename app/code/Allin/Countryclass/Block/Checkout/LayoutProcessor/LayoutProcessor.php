<?php

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

class LayoutProcessor implements LayoutProcessorInterface
{
public function process($jsLayout)
{
$component = &$jsLayout['components']['checkout']['children']['steps']['children'];
$componentBilling = &$component['billing-step']['children']['payment']['children']['payments-list']['children'];
foreach ($componentBilling as $keyBilling => $value) {
if (preg_match('/-form/', $keyBilling)) { //I have my own check function here for 'contains'
unset($componentBilling[$keyBilling]['children']['form-fields']['children']['postcode']);
}
}
}
}`


