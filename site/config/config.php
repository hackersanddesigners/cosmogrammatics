<?php

return [
    'debug' => true,
    'panel' => [
        'css' => 'assets/css/panel.css'
    ],

    'gearsdigital.enhanced-toolbar-link-dialog' => [
        'title' => '{{ page.title }}',
        'filter' => null,
        'sort' => null,
        'qualified' => false,
        'translations' => []
    ],

    // 'routes' => [
    //     [
    //         'pattern' => 'sitemap.xml',
    //         'action'  => function() {
    //             // <https://getkirby.com/docs/cookbook/content/sitemap>

    //             $pages = site()->index()->listed()->limit(500);

    //             // fetch the pages to ignore from the config settings,
    //             // if nothing is set, we ignore the error page
    //             $ignore = kirby()->option('sitemap.ignore', ['error']);

    //             $content = snippet('sitemap', compact('pages', 'ignore'), true);

    //             // return response with correct header type
    //             return new Kirby\Cms\Response($content, 'application/xml');
    //         }
    //     ],
    //     [
    //         'pattern' => 'sitemap',
    //         'action'  => function() {
    //             return go('sitemap.xml', 301);
    //         }
    //     ],
    // ],

    // enable subpage-builder from blueprint settings
    // <https://getkirby.com/docs/cookbook/extensions/subpage-builder>
    'hooks' => [
        'page.create:after' => function ($page) {
            buildPageTree($page);
        },
    ],


    'sylvainjule.bouncer.list' => [
        'contributor' => [
            'fieldname' => 'canaccess'
        ]
    ]

];
