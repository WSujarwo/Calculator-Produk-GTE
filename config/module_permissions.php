<?php

return [
    'actions' => [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'import' => 'Import',
        'export' => 'Export',
    ],

    'groups' => [
        'Master Data' => [
            'master.products' => 'Master Products',
            'master.shapes' => 'Master Shapes',
            'master.product-shapes' => 'Product-Shape Mapping',
            'master.type-configs' => 'Type / Configuration',
            'master.cost-products' => 'Cost Product',
            'master.materials' => 'Materials',
        ],
        'Settings' => [
            'settings.companies' => 'Companies',
            'settings.marketings' => 'Marketings',
            'settings.ejm-validation' => 'Validasi Data EJM',
        ],
        'Sales' => [
            'quotations' => 'Quotations',
            'pce-headers' => 'PCE Headers',
        ],
    ],
];
