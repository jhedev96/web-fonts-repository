<?php

/*
 * An example of settings that vary on different servers. To change the settings you should copy this file to
 * config/settings-local.php and edit the file.
 */

return [
    'displayErrorDetails' => true,
    'logger' => [
        'level' => \Psr\Log\LogLevel::DEBUG
    ],
    'icons' => [
        'Material Icons' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'MaterialIcons/MaterialIcons-Regular.*'
                ],
            ]
        ]
    ],
    'fonts' => [
        'PlusJakarta Sans' => [
            'styles' => [
                '700' => 'PlusJakartaSans/PlusJakartaSans-Bold.*',
                '700i' => 'PlusJakartaSans/PlusJakartaSans-BoldItalic.*',
                '800' => 'PlusJakartaSans/PlusJakartaSans-ExtraBold.*',
                '800i' => 'PlusJakartaSans/PlusJakartaSans-ExtraBoldItalic.*',
                '200' => 'PlusJakartaSans/PlusJakartaSans-ExtraLight.*',
                '200i' => 'PlusJakartaSans/PlusJakartaSans-ExtraLightItalic.*',
                '400i' => 'PlusJakartaSans/PlusJakartaSans-Italic.*',
                '300' => 'PlusJakartaSans/PlusJakartaSans-Light.*',
                '300i' => 'PlusJakartaSans/PlusJakartaSans-LightItalic.*',
                '500' => 'PlusJakartaSans/PlusJakartaSans-Medium.*',
                '500i' => 'PlusJakartaSans/PlusJakartaSans-MediumItalic.*',
                '400' => 'PlusJakartaSans/PlusJakartaSans-Regular.*',
            ]
        ],
        'Mohave' => [
            'styles' => [
                '700' => 'Mohave/Mohave-Bold.*',
                '700i' => 'Mohave/Mohave-BoldItalic.*',
                '400i' => 'Mohave/Mohave-Italic.*',
                '300' => 'Mohave/Mohave-Light.*',
                '300i' => 'Mohave/Mohave-LightItalic.*',
                '500' => 'Mohave/Mohave-Medium.*',
                '500i' => 'Mohave/Mohave-MediumItalic.*',
                '400' => 'Mohave/Mohave-Regular.*',
            ]
        ],
        'Roboto' => [
            'styles' => [
                '900' => 'Roboto/Roboto-Black.*',
                '900i' => 'Roboto/Roboto-BlackItalic.*',
                '700' => 'Roboto/Roboto-Bold.*',
                '700i' => 'Roboto/Roboto-BoldItalic.*',
                '400i' => 'Roboto/Roboto-Italic.*',
                '300' => 'Roboto/Roboto-Light.*',
                '300i' => 'Roboto/Roboto-LightItalic.*',
                '500' => 'Roboto/Roboto-Medium.*',
                '500i' => 'Roboto/Roboto-MediumItalic.*',
                '400' => 'Roboto/Roboto-Regular.*',
                '100' => 'Roboto/Roboto-Thin.*',
                '100i' => 'Roboto/Roboto-ThinItalic.*',
            ]
        ],
        'Clear Sans' => [
            'styles' => [
                '700' => 'ClearSans/ClearSans-Bold.*',
                '700i' => 'ClearSans/ClearSans-BoldItalic.*',
                '400i' => 'ClearSans/ClearSans-Italic.*',
                '300' => 'ClearSans/ClearSans-Light.*',
                '500' => 'ClearSans/ClearSans-Medium.*',
                '500i' => 'ClearSans/ClearSans-MediumItalic.*',
                '400' => 'ClearSans/ClearSans-Regular.*',
                '100' => 'ClearSans/ClearSans-Thin.*',
            ]
        ],
        'SanFrancisco' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'SanFrancisco/sanfranciscodisplay-regular-webfont.*'
                ],
            ]
        ],
        'HelveticaNeue' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'HelveticaNeue/helveticaneue.*'
                ],
                '500' => [
                    'forbidLocal' => true,
                    'files' => 'HelveticaNeue/helveticaneue-med.*'
                ],
            ]
        ],
        'FrutigerLtStd' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'FrutigerLtStd/FrutigerLTStd-BoldCn.*'
                ],
            ]
        ],
        'JurassicPark' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'JurassicPark/JurassicPark.*'
                ],
            ]
        ],
        'NeutraText Bold' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'NeutraTextBold/NeutraText-Bold.*'
                ],
            ]
        ],
        'TradeGothicLtStd' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'TradeGothicLtCondensed18/TradeGothicLTStd-Cn18.*'
                ],
            ]
        ],
        'Univers' => [
            'forbidLocal' => true,
            'styles' => [
                '400' => 'Univers/Univers.*',
                '800' => [
                    'forbidLocal' => false,	// Overrides the family rule
                    'files' => 'Univers/Univers-Condensed.*'
                ],
            ]
        ],
        'Flappy Bird' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'FlappyBird/flappybird.*'
                ],
            ]
        ],
        'AvQest' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'AvQest/AvQest.*'
                ],
            ]
        ],
        'Game of Thrones' => [
            'styles' => [
                '400' => [
                    'forbidLocal' => true,
                    'files' => 'GameOfThrones/GameofThrones.*'
                ],
            ]
        ],
        'Noto Sans' => [
            'styles' => [
                '400' => [
                    'files' => [
                        'NotoSans/NotoSans-Regular-webfont.ttf'
                    ]
                ],
            ]
        ],
        'Open Sans' => [
            'styles' => [
                '400' => [
                    'files' => [
                        'OpenSans/OpenSans-Regular.ttf'
                    ]
                ],
                '600' => [
                    'files' => [
                        'OpenSans/OpenSans-Semibold.ttf'
                    ]
                ],
            ]
        ],
        'Work Sans' => [
            'styles' => [
                '400' => [
                    'files' => [
                        'WorkSans/WorkSans-Thin.otf'
                    ]
                ],
            ]
        ],
        'Poppins' => [
            'styles' => [
                '600' => [
                    'files' => [
                        'Poppins/Poppins-SemiBold.*'
                    ]
                ],
                '700' => [
                    'files' => [
                        'Poppins/Poppins-Bold.*'
                    ]
                ],
            ]
        ],
        
        /*'Open Sans' => [
            'directory' => 'OpenSans',
            'styles' => [
                '300' => [
                    'directory' => 'light',
                    'files' => 'font.*'
                ],
                '400' => [
                    'directory' => 'regular',
                    'files' => 'font.*'
                ]
            ]
        ],
        'Open Sans' => [
            'styles' => [
                '500i' => [
                    'name' => 'DemiBold Oblique',	// The `local` names are `Open Sans DemiBold Oblique` and `OpenSans-DemiBoldOblique`
                    'files' => 'opensans_demibold_oblique.*'
                ]
            ]
        ],*/
    ],
];
