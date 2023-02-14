<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=db;dbname=google_maps_project',
    'username' => 'workana',
    'password' => 'workana',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public', //specify your schema here, public is the default schema
            'columnSchemaClass' => [
                'class' => yii\db\pgsql\ColumnSchema::class,
                // Reduce memory usage
                'disableJsonSupport' => true,
            ],
        ]
    ]
];
