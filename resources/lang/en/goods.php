<?php

return [
    'labels' => [
        'Goods' => 'Goods',
        'goods' => 'Goods',
    ],
    'fields' => [
        'actual_price' => 'Actual Price',
        'group_id' => 'Category',
        'api_hook' => 'Callback Event',
        'buy_prompt' => 'Buy Prompt',
        'description' => 'Description',
        'gd_name' => 'Goods Name',
        'gd_description' => 'Goods Description',
        'gd_keywords' => 'Goods Keywords',
        'in_stock' => 'In Stock',
        'ord' => 'Sort Weight',
        'other_ipu_cnf' => 'Other Input Config',
        'picture' => 'Goods Picture',
        'retail_price' => 'Retail Price',
        'sales_volume' => 'Sales Volume',
        'type' => 'Goods Type',
        'buy_limit_num' => 'Max Purchase Quantity Per Order',
        'wholesale_price_cnf' => 'Wholesale Price Config',
        'automatic_delivery' => 'Automatic Delivery',
        'manual_processing' => 'Manual Processing',
        'is_open' => 'Is On Sale',
        'coupon_id' => 'Available Coupon Code'
    ],
    'options' => [
    ],
    'helps' => [
        'retail_price' => 'Optional, mainly for display purposes',
        'picture' => 'Optional, default image if not uploaded',
        'in_stock' => 'When the product type is "Manual Processing", the manually entered stock quantity will take effect. For "Automatic Delivery" products, the system will automatically identify the stock quantity.',
        'buy_limit_num' => 'Prevent malicious stock manipulation, 0 means no limit on the maximum quantity a customer can order at once.',
        'other_ipu_cnf' => 'Format: [unique_identifier(english)=input_name=required]. For example: qq_account=QQ Account=true indicates a new [QQ Account] input box will be added to the product details page where customers can enter their [QQ Account]. true for required, false for optional. (One per line)',
        'wholesale_price_cnf' => 'For example: 5=3 indicates that when a customer buys 5 or more items, the price per item is 3 yuan. One per line',
    ]
];
