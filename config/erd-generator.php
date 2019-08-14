<?php declare(strict_types = 1);

return [

	/*
	 * All models in these directories will be scanned for ER diagram generation.
	 * By default, the `app` directory will be scanned recursively for models.
	 */
    'directories' => [
        base_path('app'),
    ],

    /*
    * If you want to ignore complete models or certain relations of a specific model,
    * you can specify them here.
    * To ignore a model completely, just add the fully qualified classname.
    * To ignore only a certain relation of a model, enter the classname as the key
    * and an array of relation names to ignore.
    */
    'ignore' => [
    // User::class,
    // Post::class => [
    //     'user'
    // ]
    ],

    /*
    * If true, all directories specified will be scanned recursively for models.
    * Set this to false if you prefer to explicitly define each directory that should
    * be scanned for models.
    */
    'recursive' => true,

    /*
    * The generator will automatically try to look up the model specific columns
    * and add them to the generated output. If you do not wish to use this
    * feature, you can disable it here.
    */
    'use_db_schema' => true,

    /*
    * This setting toggles weather the column types (VARCHAR, INT, TEXT, etc.)
    * should be visible on the generated diagram. This option requires
    * 'use_db_schema' to be set to true.
    */
    'use_column_types' => true,

    /*
    * These colors will be used in the table representation for each entity in
    * your graph.
    */
    'table' => [
        'header_background_color' => '#28bb9c',
        'header_font_color' => '#ffffff',
        'row_background_color' => '#ffffff',
        'row_font_color' => '#333333',
    ],

    /*
    * Here you can define all the available Graphviz attributes that should be applied to your graph,
    * to its nodes and to the edge (the connection between the nodes). Depending on the size of
    * your diagram, different settings might produce better looking results for you.
    *
    * See http://www.graphviz.org/doc/info/attrs.html#d:label for a full list of attributes.
    */
    'graph' => [
        'style' => 'filled',
        'bgcolor' => '#F7F7F7',
        'fontsize' => 42,
        'labelloc' => 't',
        'concentrate' => false,
        'splines' => 'polyline',
        'overlap' => true,
        'nodesep' => 0.45,
        'rankdir' => 'LR',
        'pad' => 2,
        'ranksep' => 20,
        'esep' => true,
        'fontname' => 'Helvetica Neue',
    ],

    'node' => [
        'margin' => 0,
        'shape' => 'rectangle',
        'fontname' => 'Helvetica Neue',
        'fontsize' => 35,
    ],

    'edge' => [
        'color' => '#003049',
        'penwidth' => 5,
        'fontname' => 'Helvetica Neue',
    ],

    'relations' => [
        'HasOne' => [
            'dir' => 'both',
            'color' => '#D62828',
            'arrowhead' => 'tee',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
        'BelongsTo' => [
            'dir' => 'both',
            'color' => '#F77F00',
            'arrowhead' => 'tee',
            'arrowtail' => 'crow',
            'fontsize' => 42,
        ],
        'HasMany' => [
            'dir' => 'both',
            'color' => '#FCBF49',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
        'BelongsToMany' => [
            'dir' => 'both',
            'color' => '#006666',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
        'MorphMany' => [
            'dir' => 'both',
            'color' => '#05A05A',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
        'MorphTo' => [
            'dir' => 'both',
            'color' => '#844185',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
        'HasManyThrough' => [
            'dir' => 'both',
            'color' => '#3399FF',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
        'HasOneThrough' => [
            'dir' => 'both',
            'color' => '#FF7F50',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'fontsize' => 42,
        ],
    ],

];
