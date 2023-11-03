<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{


    use Nette\StaticClass;


    /**
     * @return Nette\Application\IRouter
     * @throws Nette\Application\BadRequestException
     */
    public static function createRouter()
    {
        $router = new RouteList;

        //admin route
        $router[] = new Route('admin/<presenter>/<action>', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Admin',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Homepage',
            ],
            'action' => [
                Route::VALUE => 'default',
            ],
            'locale' => 'cs'
        ]);

        //periskop routes
        //say hello to max length of url inputs in Periskop system
        $router[] = new Route('peo/<token>', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Periskop',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Export',
            ],
            'action' => [
                Route::VALUE => 'order',
            ],
        ]);
        $router[] = new Route('pec/<token>', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Periskop',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Export',
            ],
            'action' => [
                Route::VALUE => 'customer',
            ],
        ]);

        //front routes
        $router[] = new Route('feed/<action>.xml', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Front',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Feed',
            ],
        ]);
	    //static pages
	    $router[] = new Route('vanocni-zvyhodneni', [
		    Route::MODULE_KEY => [
			    Route::VALUE => 'Front',
		    ],
		    Route::PRESENTER_KEY => [
			    Route::VALUE => 'ChristmasSale',
		    ],
		    'action' => [
			    Route::VALUE => 'default',
		    ],
	    ]);
				$router[] = new Route('podporujeme-nova-manzelstvi', [
					Route::MODULE_KEY => [
						Route::VALUE => 'Front',
					],
					Route::PRESENTER_KEY => [
						Route::VALUE => 'Wedding',
					],
					'action' => [
						Route::VALUE => 'default',
					],
				]);			
        $router[] = new Route('[<asdf=cs cs>/]kategorie/<url>[/<productParametersFiltration [a-zA-Z0-9/\-_]+>]', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Front',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Category',
                Route::FILTER_TABLE => [
                    'kategorie' => 'Category',
                ],
            ],
            'action' => [
                Route::VALUE => 'default',
            ],
            'productParametersFiltration' => [
                //parse filter parameters from url
                Route::FILTER_IN => function ($params) {
                    return explode('/', $params);
                }, //collect friendly url
                Route::FILTER_OUT => function ($params) {
                    //clear from NULL params
                    foreach ($params as $key => $p) {
                        if ($p === null) {
                            unset($params[$key]);
                        }
                    }
                    ksort($params); //sort ascendant by parameter id
                    return implode('/', $params) ?: null;
                },
            ],
        ]);
        $router[] = new Route('[<asdf=cs cs>/]<presenter stranka|produkt|clanek>/<url>', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Front',
            ],
            Route::PRESENTER_KEY => [
                Route::FILTER_TABLE => [
                    'clanek' => 'Article',
                    'stranka' => 'Page',
                    'produkt' => 'Product',
                ],
            ],
            'action' => [
                Route::VALUE => 'detail',
            ],
        ]);
        $router[] = new Route('[<asdf=cs cs>/]<presenter clanky>/<url>[/<category>]', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Front',
            ],
            Route::PRESENTER_KEY => [
                Route::FILTER_TABLE => [
                    'clanky' => 'Article',
                ],
            ],
            'action' => [
                Route::VALUE => 'list',
            ],
        ]);
        $router[] = new Route('<presenter sitemap>/<action>.xml', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Front',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Sitemap',
            ],
            'action' => [
                Route::FILTER_TABLE => [
                    'category-parameter-group' => 'categoryParameterGroup',
                ],
            ],
        ]);
        $router[] = new Route('[<asdf=cs cs>/]<presenter>/<action>', [
            Route::MODULE_KEY => [
                Route::VALUE => 'Front',
            ],
            Route::PRESENTER_KEY => [
                Route::VALUE => 'Homepage',
                Route::FILTER_TABLE => [
                    'kosik' => 'ShoppingCart',
                    'platebni-brana' => 'PaymentGateway',
                    'kosik-produkt' => 'Product',
                    'ucet' => 'Sign',
                    'muj-ucet' => 'Account',
                    'vyhledavani' => 'Search',
                    'oblibene' => 'Favourite',
                ],
            ],
            'action' => [
                Route::VALUE => 'default',
                Route::FILTER_TABLE => [
                    'cekajici' => 'pending',
                    'clanky' => 'article',
                    'kategorie' => 'category',
                    'nove-heslo' => 'setNewPassword',
                    'objednavky' => 'orderList',
                    'objednavka' => 'orderDetail',
                    'odhlaseni' => 'out',
                    'osobni-udaje' => 'personalData',
                    'pridan' => 'addedIntoShoppingCart',
                    'prihlaseni' => 'in',
                    'registrace-prodejna-dokonceni' => 'storeRegistration',
                    'produkty' => 'product',
                    'registrace' => 'up',
                    'registrace-prodejna' => 'storeRegistrationRequest',
                    'platba' => 'createRequest',
                    'prehled' => 'step1',
                    'doprava-a-platba' => 'step2',
                    'dodaci-udaje' => 'step3',
                    'zpusob-dokonceni-nakupu' => 'step1links',
                    'rekapitulace' => 'step3Recapitulation',
                    'objednavka-odeslana' => 'step4',
                    'zaplaceno' => 'paid',
                    'zapomenute-heslo' => 'forgottenPassword',
                    'zmena-hesla' => 'passwordChange',
                    'zruseno' => 'cancelled',
                    'pridat' => 'add',
                    'odebrat' => 'remove',
                ],
            ],
        ]);


        return $router;
    }

}