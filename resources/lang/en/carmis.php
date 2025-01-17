<?php

return [
    'labels' => [
        'Carmis' => 'Carmis',
        'carmis' => 'Carmis',
    ],
    'fields' => [
        'goods_id' => 'Goods ID',
        'status' => 'Status',
        'carmi' => 'Carmi Content',
        'status_unsold' => 'Unsold',
        'status_sold' => 'Sold',
        'is_loop' => 'Loop Carmi',
        'yes'=>'Yes',
        'import_carmis' => 'Import Carmis',
        'carmis_list' => 'Carmis List',
        'carmis_txt' => 'Carmis Text',
        'are_you_import_sure' => 'Are you sure to import carmis?',
        'remove_duplication' => 'Remove Duplication',
    ],
    'options' => [
    ],
    'helps' => [
        'carmis_list' => 'One per line, separated by enter. Please do not import carmis with excessive single text length, which can easily lead to memory overflow. If the carmi is too large, it is recommended to modify the product to manual processing.'
    ],
    'rule_messages' => [
        'carmis_list_and_carmis_txt_can_not_be_empty' => 'Please fill in the carmis to be imported or select the carmi file to be uploaded',
        'import_carmis_success' => 'Import carmis successfully!'
    ]
];
