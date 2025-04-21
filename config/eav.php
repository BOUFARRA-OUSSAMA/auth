<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Entity Types Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the default entity types for your application.
    | You can modify or extend these as needed.
    |
    */
    'entity_types' => [
        'user' => [
            'name' => 'User',
            'description' => 'User entity type',
        ],
        'product' => [
            'name' => 'Product',
            'description' => 'Product entity type',
        ],
        // Add more entity types as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Attribute Types
    |--------------------------------------------------------------------------
    |
    | This section defines the supported attribute data types.
    | Each type can have specific validation rules.
    |
    */
    'attribute_types' => [
        'string' => [
            'validation' => 'string|max:255',
            'cast'       => 'string',
        ],
        'text' => [
            'validation' => 'string',
            'cast'       => 'string',
        ],
        'textarea' => [
            'validation' => 'string',
            'cast'       => 'string',
        ],
        'integer' => [
            'validation' => 'integer',
            'cast'       => 'integer',
        ],
        'decimal' => [
            'validation' => 'numeric',
            'cast'       => 'float',
        ],
        'boolean' => [
            'validation' => 'boolean',
            'cast'       => 'boolean',
        ],
        'date' => [
            'validation' => 'date',
            'cast'       => 'date',
        ],
        'datetime' => [
            'validation' => 'date',
            'cast'       => 'datetime',
        ],
        'json' => [
            'validation' => 'json',
            'cast'       => 'array',
        ],
        'select' => [
            'validation' => 'string',
            'cast'       => 'string',
        ],
        'multiselect' => [
            'validation' => 'array',
            'cast'       => 'array',
        ],
        'file' => [
            'validation' => 'file',
            'cast'       => 'string',
        ],
        'image' => [
            'validation' => 'image',
            'cast'       => 'string',
        ],
        // Add more types as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for EAV data to improve performance.
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // Time to live in seconds (1 hour)
        'prefix' => 'eav_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Value Serialization
    |--------------------------------------------------------------------------
    |
    | Configuration for how values are stored and retrieved
    |
    */
    'serialization' => [
        // Whether to automatically serialize/deserialize complex values
        'auto_serialize' => true,

        // Types that should be serialized when stored
        'serialize_types' => [
            'json',
            'array',
            'object',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Attributes
    |--------------------------------------------------------------------------
    |
    | Default attributes to create for each entity type
    |
    */
    'default_attributes' => [
        'user' => [
            [
                'code' => 'address',
                'name' => 'Address',
                'type' => 'text',
                'is_required' => false,
                'description' => 'User address information',
            ],
            [
                'code' => 'date_of_birth',
                'name' => 'Date of Birth',
                'type' => 'date',
                'is_required' => false,
                'description' => 'User birth date',
            ],
            // Add more default user attributes
        ],
        // Define defaults for other entity types
    ],
];
