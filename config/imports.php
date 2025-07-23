<?php

return [
    'orders' => [
        'label' => 'Import Orders',
        'permission_required' => 'import-orders',
        'files' => [
            'file1' => [
                'label' => 'File 1',
                'model' => new \App\Models\Order(),
                'headers_to_db' => [
                    'order_date' => [
                        'label'      => 'Order Date',
                        'type'       => 'date',
                        'validation' => ['required']
                    ],
                    'channel' => [
                        'label'      => 'Channel',
                        'type'       => 'string',
                        'validation' => ['required']
                    ],
                    'sku' => [
                        'label'      => 'SKU',
                        'type'       => 'string',
                        'validation' => ['required', 'exists' => ['table' => 'products', 'column' => 'sku']]
                    ],
                    'item_description' => [
                        'label'      => 'Item Description',
                        'type'       => 'string',
                        'validation' => ['nullable']
                    ],
                    'origin' => [
                        'label'      => 'Origin',
                        'type'       => 'string',
                        'validation' => ['required']
                    ],
                    'so_num' => [
                        'label'      => 'SO#',
                        'type'       => 'string',
                        'validation' => ['required']
                    ],
                    'cost' => [
                        'label'      => 'Cost',
                        'type'       => 'double',
                        'validation' => ['required']
                    ],
                    'shipping_cost' => [
                        'label'      => 'Shipping Cost',
                        'type'       => 'double',
                        'validation' => ['required']
                    ],
                    'total_price' => [
                        'label'      => 'Total Price',
                        'type'       => 'double',
                        'validation' => ['required']
                    ]
                ],
                'update_or_create' => ['so_num', 'sku']
            ]
        ]
    ],
    'products' => [
        'label' => 'Import Products',
        'permission_required' => 'import-products',
        'files' => [
            'file1' => [
                'label' => 'File 1',
                'model' => new \App\Models\Product(),
                'headers_to_db' => [
                    'sku' => [
                        'label'      => 'SKU',
                        'type'       => 'string',
                        'validation' => ['required']
                    ],
                    'title' => [
                        'label'      => 'Title',
                        'type'       => 'string',
                        'validation' => ['required']
                    ],
                    'item_description' => [
                        'label'      => 'Item Description',
                        'type'       => 'string',
                        'validation' => ['nullable']
                    ],
                ],
                'update_or_create' => ['sku']
            ]
        ]
    ],
    'prices' => [
        'label' => 'Import Prices',
        'permission_required' => 'import-prices',
        'files' => [
            'file1' => [
                'label' => 'File 1',
                'model' => new \App\Models\Price(),
                'headers_to_db' => [
                    'sku' => [
                        'label'      => 'SKU',
                        'type'       => 'string',
                        'validation' => ['required', 'exists' => ['table' => 'products', 'column' => 'sku']]
                    ],
                    'price' => [
                        'label'      => 'Price',
                        'type'       => 'double',
                        'validation' => ['required', 'numeric']
                    ]
                ],
                'update_or_create' => ['sku']
            ],
            'file2' => [
                'label' => 'File 2',
                'model' => new \App\Models\PriceDiscount(),
                'headers_to_db' => [
                    'price_id' => [
                        'label'      => 'Price ID',
                        'type'       => 'string',
                        'validation' => ['required', 'exists' => ['table' => 'prices', 'column' => 'id']]
                    ],
                    'qty' => [
                        'label'      => 'qty',
                        'type'       => 'integer',
                        'validation' => ['required', 'numeric']
                    ],
                    'discount' => [
                        'label'      => 'Discount',
                        'type'       => 'double',
                        'validation' => ['required', 'numeric']
                    ]
                ]
            ]
        ]
    ]
];
