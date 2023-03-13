<?php return array(
    'root' => array(
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => 'wedevs/dokan',
        'dev' => false,
    ),
    'versions' => array(
        'appsero/client' => array(
            'pretty_version' => 'dev-develop',
            'version' => 'dev-develop',
            'type' => 'library',
            'install_path' => __DIR__ . '/../appsero/client',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'reference' => '169978e17a1aa765fc12fb008569c3e1e8740002',
            'dev_requirement' => false,
        ),
        'jakeasmith/http_build_url' => array(
            'pretty_version' => '1.0.1',
            'version' => '1.0.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../jakeasmith/http_build_url',
            'aliases' => array(),
            'reference' => '93c273e77cb1edead0cf8bcf8cd2003428e74e37',
            'dev_requirement' => false,
        ),
        'wedevs/dokan' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
    ),
);
