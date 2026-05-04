<?php

namespace Database\Seeders\Geography;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemChapter;
use App\Models\SystemCountry;
use App\Models\SystemRegion;
use App\Models\SystemChapterContact;

class SystemChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chapters = array(
            // Arizona chapters (already provided)
            [
                'country_code' => 'US',
                'region_code' => 'AR',
                'name' => 'Fayetteville Achilles',
                'city' => 'Fayetteville',
                'email' => 'achillesarkansas@gmail.com',
                'phone' => '479-871-2409',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/achillesarkansas/',
                    'instagram' => 'https://www.instagram.com/achillesarkansas'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Rob Lambert',
                        'email' => 'achillesarkansas@gmail.com',
                        'phone' => '479-871-2409',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'CA',
                'name' => 'Los Angeles Achilles',
                'city' => 'Los Angeles',
                'email' => 'la@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'instagram' => 'https://www.instagram.com/achilleslosangeles/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Christina Swanson',
                        'email' => 'la@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'CA',
                'name' => 'San Francisco Bay Area Achilles',
                'city' => 'San Francisco',
                'email' => 'achillessfbayarea@gmail.com',
                'phone' => '908-868-7770',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/achillesbayarea/',
                    'instagram' => 'https://www.instagram.com/achillessfbayarea/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Julia Harbaugh',
                        'email' => 'achillessfbayarea@gmail.com',
                        'phone' => '908-868-7770',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'CO',
                'name' => 'Boulder Achilles',
                'city' => 'Boulder',
                'email' => 'judydixon@comcast.net',
                'phone' => '303-842-2097',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/groups/228528602986710'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Judy Mares-Dixon / Annette Kissinger',
                        'email' => 'judydixon@comcast.net',
                        'phone' => '303-842-2097',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'CO',
                'name' => 'Denver Achilles',
                'city' => 'Denver',
                'email' => 'achillescolorado@gmail.com',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/achillescolorado/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Amelia Dickerson',
                        'email' => 'achillescolorado@gmail.com',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'CO',
                'name' => 'Pikes Peak Achilles',
                'city' => 'Colorado Springs',
                'email' => 'achillespikespeak@gmail.com',
                'phone' => '828-712-3737',
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Brandon Stapanowich',
                        'email' => 'achillespikespeak@gmail.com',
                        'phone' => '828-712-3737',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'CT',
                'name' => 'Connecticut Achilles',
                'city' => 'Connecticut',
                'email' => 'president@achillesCT.org',
                'phone' => '203-361-6176',
                'website' => 'achillesct.org',
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Erin Spaulding',
                        'email' => 'president@achillesCT.org',
                        'phone' => '203-361-6176',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'IL',
                'name' => 'Chicago Achilles',
                'city' => 'Chicago',
                'email' => 'kcassarini@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Kristen Cassarini',
                        'email' => 'kcassarini@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'MA',
                'name' => 'Boston Achilles',
                'city' => 'Boston',
                'email' => 'jlemar@achillesinternational.org',
                'phone' => '508-345-5485',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/AchillesInternationalBoston/',
                    'instagram' => 'https://www.instagram.com/achillesboston/',
                    'twitter' => 'https://mobile.twitter.com/bostonachilles'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Joseph LeMar',
                        'email' => 'jlemar@achillesinternational.org',
                        'phone' => '508-345-5485',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'MN',
                'name' => 'Twin Cities Achilles',
                'city' => 'Minneapolis',
                'email' => 'twincitiesmn@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/AchillesMinnesota/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Dr. Ivonne Mosquera-Schmidt',
                        'email' => 'twincitiesmn@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'MO',
                'name' => 'St. Louis Achilles',
                'city' => 'St. Louis',
                'email' => 'achillesstl@gmail.com',
                'phone' => '314-440-4691',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/achillesstlouis/',
                    'instagram' => 'https://www.instagram.com/achillesstl/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Annie Donnell',
                        'email' => 'achillesstl@gmail.com',
                        'phone' => '314-440-4691',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'MO',
                'name' => 'Kansas City Achilles',
                'city' => 'Kansas City',
                'email' => 'kansascity@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'instagram' => 'https://www.instagram.com/achilleskansascity/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Abbey O\'Neil',
                        'email' => 'kansascity@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NV',
                'name' => 'Las Vegas Achilles',
                'city' => 'Las Vegas',
                'email' => 'achilleslasvegas@gmail.com',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Merie McGrath and Ed Robichaud',
                        'email' => 'achilleslasvegas@gmail.com',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NJ',
                'name' => 'New Jersey Achilles',
                'city' => 'New Jersey',
                'email' => 'achilles.newjersey@gmail.com',
                'phone' => '201-637-4792',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/GoAchillesNJ/',
                    'instagram' => 'https://www.instagram.com/achillesnj/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Joseph Sorbanelli',
                        'email' => 'achilles.newjersey@gmail.com',
                        'phone' => '201-637-4792',
                        'is_primary' => true
                    ]
                ]
            ],
            // New York has multiple chapters
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Brooklyn Achilles',
                'city' => 'Brooklyn',
                'email' => 'brooklynny@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Brooklyn Chapter',
                        'email' => 'brooklynny@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Long Island Achilles',
                'city' => 'Long Island',
                'email' => 'longislandachilles@gmail.com',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/LongIslandAchilles',
                    'instagram' => 'https://www.instagram.com/longislandachilles/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Long Island Chapter',
                        'email' => 'longislandachilles@gmail.com',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Bronx Achilles',
                'city' => 'Bronx',
                'email' => 'BronxNY@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Bronx Chapter',
                        'email' => 'BronxNY@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Manhattan Achilles',
                'city' => 'Manhattan',
                'email' => 'fmagisano@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Manhattan Chapter',
                        'email' => 'fmagisano@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Queens Achilles',
                'city' => 'Queens',
                'email' => 'queensny@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Queens Chapter',
                        'email' => 'queensny@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Staten Island Achilles',
                'city' => 'Staten Island',
                'email' => 'egulati@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Staten Island Chapter',
                        'email' => 'egulati@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NY',
                'name' => 'Westchester Achilles',
                'city' => 'Westchester',
                'email' => 'rosenv@jccany.org',
                'phone' => '914-646-3385',
                'website' => null,
                'social_media' => [],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Valerie Rosen',
                        'email' => 'rosenv@jccany.org',
                        'phone' => '914-646-3385',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'NC',
                'name' => 'Charlotte Achilles',
                'city' => 'Charlotte',
                'email' => 'achillescharlotte@gmail.com',
                'phone' => '402-871-9514',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/AchillesCLT/',
                    'instagram' => 'https://www.instagram.com/achillescharlotte/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Eric Strong',
                        'email' => 'achillescharlotte@gmail.com',
                        'phone' => '402-871-9514',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'PA',
                'name' => 'Philadelphia Achilles',
                'city' => 'Philadelphia',
                'email' => 'melissa@phillyachilles.com',
                'phone' => null,
                'website' => 'phillyachilles.com',
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/groups/678101945544265',
                    'instagram' => 'https://www.instagram.com/phillyachilles/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Melissa Wilcox',
                        'email' => 'melissa@phillyachilles.com',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'TN',
                'name' => 'Nashville Achilles',
                'city' => 'Nashville',
                'email' => 'achillesnashville@gmail.com',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/AchillesInternationalNashville',
                    'instagram' => 'https://www.instagram.com/achillesnashville/',
                    'twitter' => 'https://twitter.com/AchillesNash'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Amy Harris / Sara "Lizzy" Harris',
                        'email' => 'achillesnashville@gmail.com',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'TX',
                'name' => 'Houston Achilles',
                'city' => 'Houston',
                'email' => 'mcueto@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'instagram' => 'https://www.instagram.com/achilleshouston/'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Michaela Cueto',
                        'email' => 'mcueto@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'UT',
                'name' => 'Salt Lake City Achilles',
                'city' => 'Salt Lake City',
                'email' => 'utah@achillesinternational.org',
                'phone' => '801-330-0581',
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/groups/226825971728820'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Ken Duke',
                        'email' => 'utah@achillesinternational.org',
                        'phone' => '801-330-0581',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'country_code' => 'US',
                'region_code' => 'DC',
                'name' => 'Washington DC Achilles',
                'city' => 'Washington',
                'email' => 'hmcfadden@achillesinternational.org',
                'phone' => null,
                'website' => null,
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/AchillesInternationalDC',
                    'instagram' => 'https://www.instagram.com/achillesinternationaldc/',
                    'twitter' => 'https://twitter.com/AchillesDc'
                ],
                'contacts' => [
                    [
                        'user_id' => null,
                        'name' => 'Hannah McFadden',
                        'email' => 'hmcfadden@achillesinternational.org',
                        'phone' => null,
                        'is_primary' => true
                    ]
                ]
            ],
            array(
                'country_code' => 'AU',
                'region_code' => 'APAC',
                'name' => 'Adelaide',
                'city' => 'Adelaide',
                'email' => 'achilles-adelaide@outlook.com',
                'phone' => '(+61) 439-600-546',
                'website' => 'achillesaustralia.org.au/adelaide',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/profile.php?id=61561613546490',
                    'instagram' => 'https://www.instagram.com/achillesadelaide/',
                    'linkedin' => 'https://www.linkedin.com/company/achilles-adelaide/'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 1,
                        'name' => 'Justine Crawford',
                        'email' => 'achilles-adelaide@outlook.com',
                        'phone' => '(+61) 439-600-546',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'AU',
                'region_code' => 'APAC',
                'name' => 'Brisbane',
                'city' => 'Brisbane',
                'email' => 'achillesbrisbane@gmail.com',
                'website' => 'achillesaustralia.org.au/brisbane',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/AchillesBrisbane/'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 2,
                        'name' => 'Enrique Suana',
                        'email' => 'achillesbrisbane@gmail.com',
                        'is_primary' => true
                    )
                )
            ),
            // Additional chapters to add to the array
            array(
                'country_code' => 'AU',
                'region_code' => 'APAC',
                'name' => 'Canberra',
                'city' => 'Canberra',
                'email' => 'achillescanberra@gmail.com',
                'website' => 'achillesaustralia.org.au/canberra',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/AchillesCanberra/'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 3,
                        'name' => 'Peter Ralston',
                        'email' => 'achillescanberra@gmail.com',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'AU',
                'region_code' => 'APAC',
                'name' => 'Hunter Central Coast',
                'city' => 'Hunter Central Coast',
                'social_media' => array(
                    'facebook' => 'https://m.facebook.com/achilleshuntercentralcoast/'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 4,
                        'name' => 'Claire Northrup',
                        'email' => 'claire_northrop@me.com',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'AU',
                'region_code' => 'APAC',
                'name' => 'Melbourne',
                'city' => 'Melbourne',
                'email' => 'melbourne@achillesaustralia.org.au',
                'phone' => '(+61) 468-373-373',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/AchillesMelbourne',
                    'instagram' => 'https://www.instagram.com/achillesmelbourne/'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 5,
                        'name' => 'Amanda Kwong',
                        'email' => 'melbourne@achillesaustralia.org.au',
                        'phone' => '(+61) 468-373-373',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'AU',
                'region_code' => 'APAC',
                'name' => 'Sydney',
                'city' => 'Sydney',
                'email' => 'info@achilles-sydney.org.au',
                'phone' => '(+61) 405-533-707',
                'website' => 'achillesaustralia.org.au/sydney',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/AchillesSydney'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 6,
                        'name' => 'Ellis Janks',
                        'email' => 'info@achilles-sydney.org.au',
                        'phone' => '(+61) 405-533-707',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'BR',
                'region_code' => 'SA',
                'name' => 'Sao Paulo',
                'city' => 'Sao Paulo',
                'email' => 'edson.santi@achillesinternationalbrazil.com',
                'phone' => '(+55) 11-981359132',
                'website' => 'achillesinternationalbrazil.com',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/AchillesIB',
                    'instagram' => 'https://www.instagram.com/achilles_brazil/'
                ),
                'contacts' => array(
                    array(
                        'user_id' => 7,
                        'name' => 'Edson Santi',
                        'email' => 'edson.santi@achillesinternationalbrazil.com',
                        'phone' => '(+55) 11-981359132',
                        'is_primary' => true
                    )
                )
            ),

            // Canada chapters
            array(
                'country_code' => 'CA',
                'region_code' => 'NA',
                'name' => 'Toronto, The Beach',
                'city' => 'Toronto',
                'state' => 'Ontario',
                'is_headquarters' => false,
                'email' => 'c_w_h_i_t_e@hotmail.com',
                'phone' => '(+1) 416-651-2092',
                'contacts' => array(
                    array(
                        'user_id' => 8,
                        'name' => 'Chris White',
                        'email' => 'c_w_h_i_t_e@hotmail.com',
                        'phone' => '(+1) 416-651-2092',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'CA',
                'region_code' => 'NA',
                'name' => 'National Office',
                'city' => 'Toronto',
                'state' => 'Ontario',
                'is_headquarters' => true,
                'email' => 'achillescanada@achillesinternational.org',
                'website' => 'www.achillesinternationalcanada.org'
            ),
            array(
                'country_code' => 'CA',
                'region_code' => 'NA',
                'name' => 'Ottawa',
                'city' => 'Ottawa',
                'state' => 'Ontario',
                'email' => 'ashland2dog@gmail.com',
                'phone' => '613-290-1462',
                'website' => 'www.achillesottawa.ca',
                'contacts' => array(
                    array(
                        'user_id' => 9,
                        'name' => 'Richard Marsolais',
                        'email' => 'ashland2dog@gmail.com',
                        'phone' => '613-290-1462',
                        'is_primary' => true
                    )
                )
            ),
            // Colombia
            array(
                'country_code' => 'CO',
                'region_code' => 'SA',
                'name' => 'Bogota',
                'city' => 'Bogota',
                'email' => 'AOSPITIA@UT.EDU.CO',
                'phone' => '(+57) 316-478-1794',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/achillesinternationalcol/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Alexander Ospitia',
                        'email' => 'AOSPITIA@UT.EDU.CO',
                        'phone' => '(+57) 316-478-1794',
                        'is_primary' => true
                    )
                )
            ),

// Ecuador
            array(
                'country_code' => 'EC',
                'region_code' => 'SA',
                'name' => 'Quito',
                'city' => 'Quito',
                'email' => 'stalinpozo@yahoo.es',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/pages/category/Charity-Organization/Achilles-Ecuador-601394203699668/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Byron S. Pozo',
                        'email' => 'stalinpozo@yahoo.es',
                        'is_primary' => true
                    )
                )
            ),

// Germany
            array(
                'country_code' => 'DE',
                'region_code' => 'EU',
                'name' => 'Main Office',
                'is_headquarters' => true,
                'email' => 'tobiasfraas@achillesinternational-germany.org',
                'website' => 'http://www.achillesinternational-germany.org/',
                'contacts' => array(
                    array(
                        'name' => 'Tobias Fraas',
                        'email' => 'tobiasfraas@achillesinternational-germany.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'DE',
                'region_code' => 'EU',
                'name' => 'Hamburg',
                'city' => 'Hamburg',
                'email' => 'tobiasfraas@achillesinternational-germany.org',
                'website' => 'http://www.achillesinternational-germany.org/',
                'contacts' => array(
                    array(
                        'name' => 'Thomas Schwandt',
                        'email' => 'tobiasfraas@achillesinternational-germany.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'DE',
                'region_code' => 'EU',
                'name' => 'Munich',
                'city' => 'Munich',
                'email' => 'local-club-muenchen@achillesinternational-germany.org',
                'website' => 'http://www.achillesinternational-germany.org/',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/achillesinternational.localclub.muenchen/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Mariana Hille',
                        'email' => 'local-club-muenchen@achillesinternational-germany.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'DE',
                'region_code' => 'EU',
                'name' => 'Stuttgart',
                'city' => 'Stuttgart',
                'email' => 'local-club-stuttgart@achillesinternational-germany.org',
                'website' => 'http://www.achillesinternational-germany.org/',
                'contacts' => array(
                    array(
                        'name' => 'Marieke Dressler',
                        'email' => 'local-club-stuttgart@achillesinternational-germany.org',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Alex Roempler Dellien',
                        'is_primary' => false
                    )
                )
            ),
            // Italy
            array(
                'country_code' => 'IT',
                'region_code' => 'EU',
                'name' => 'Rome',
                'city' => 'Rome',
                'email' => 'ada.ammirata@gmail.com',
                'phone' => '(+011) 39-393-1053915',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/Achilles-International-Roma-1649305482024896/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Ada Ammirata',
                        'email' => 'ada.ammirata@gmail.com',
                        'phone' => '(+011) 39-393-1053915',
                        'is_primary' => true
                    )
                )
            ),

// Japan
            array(
                'country_code' => 'JP',
                'region_code' => 'APAC',
                'name' => 'Tokyo',
                'city' => 'Tokyo',
                'contacts' => array(
                    array(
                        'name' => 'Shinji Yamamoto',
                        'email' => 'kysk0518@gmail.com',
                        'phone' => '+81-(0)90-8509-4631',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Michio Toyohara',
                        'email' => 'toiawase@achillesinternational-japan.org',
                        'phone' => '+81-(0)90-3914-2125',
                        'is_primary' => false
                    )
                )
            ),

// Mexico
            array(
                'country_code' => 'MX',
                'region_code' => 'NA',
                'name' => 'Main Office',
                'city' => 'Mexico City',
                'is_headquarters' => true,
                'phone' => '(+152) 618-112-87-06',
                'email' => 'tererobledo@econsultoria.mx',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/achillesmexico',
                    'instagram' => 'https://www.instagram.com/halconesachillesmexico/',
                    'tiktok' => 'https://www.tiktok.com/@achillesmexico',
                    'linkedin' => 'https://www.linkedin.com/company/achilles-mexico/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Teresita de Jesús Robledo Ríos',
                        'email' => 'tererobledo@econsultoria.mx',
                        'phone' => '(+152) 618-112-87-06',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'MX',
                'region_code' => 'NA',
                'name' => 'Mexico State',
                'phone' => '(+152) 55 4800 5850',
                'email' => 'achillescapitulomexico@gmail.com',
                'contacts' => array(
                    array(
                        'name' => 'Carolina Cháirez',
                        'email' => 'achillescapitulomexico@gmail.com',
                        'phone' => '(+152) 55 4800 5850',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'MX',
                'region_code' => 'NA',
                'name' => 'Guaymas',
                'city' => 'Guaymas',
                'phone' => '(+152) 55-25-23-10-07',
                'email' => 'briandaivette@hotmail.com',
                'contacts' => array(
                    array(
                        'name' => 'Brianda Ivette Rascón Corona',
                        'email' => 'briandaivette@hotmail.com',
                        'phone' => '(+152) 55-25-23-10-07',
                        'is_primary' => true
                    )
                )
            ),
            // Mongolia
            array(
                'country_code' => 'MN',
                'region_code' => 'APAC',
                'name' => 'Ulaan Baatar',
                'city' => 'Ulaan Baatar',
                'website' => 'www.achillesmongolia.mn',
                'social_media' => array(
                    'facebook' => array(
                        'https://www.facebook.com/achillesinternationalmongolia/',
                        'https://www.facebook.com/groups/www.achillesmongolia.mn/',
                        'https://www.facebook.com/AchillesKidsMongolia/',
                        'https://www.facebook.com/achillesvolunteers/'
                    )
                ),
                'contacts' => array(
                    array(
                        'name' => 'Tumurkhuu Davaakhuu',
                        'title' => 'Chairman',
                        'email' => 'tumurkhuu@davaakhuu.com',
                        'phone' => '+1 (971) 202-6931',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Saranchuluun Otgon',
                        'title' => 'CEO',
                        'email' => 'otgonsaranchuluun@gmail.com',
                        'is_primary' => false
                    ),
                    array(
                        'name' => 'Amarjargal Davaa',
                        'title' => 'Program Director',
                        'email' => 'amarjargald@gmail.com',
                        'is_primary' => false
                    ),
                    array(
                        'name' => 'Tsatsralt Naran',
                        'title' => 'Achilles Kids Mongolia',
                        'email' => 'ntsatsralt@gmail.com',
                        'is_primary' => false
                    )
                )
            ),

// New Zealand
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'National Office',
                'is_headquarters' => true,
                'contacts' => array(
                    array(
                        'name' => 'Maia Lewis',
                        'title' => 'National Administration Manager',
                        'email' => 'maia@achillesnewzealand.org',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Thomas Coysh',
                        'title' => 'Event and Projects Coordinator',
                        'email' => 'thomas@achillesnewzealand.org',
                        'is_primary' => false
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Auckland',
                'city' => 'Auckland',
                'email' => 'achillesauckland@gmail.com',
                'contacts' => array(
                    array(
                        'name' => 'Lars Madsen',
                        'phone' => '(+021) 224-8290',
                        'email' => 'achillesauckland@gmail.com',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Brennan Loft',
                        'phone' => '(+021) 063-3371',
                        'email' => 'Brennan_loft@hotmail.com',
                        'is_primary' => false
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Christ Church',
                'city' => 'Christ Church',
                'email' => 'achilleschristchurch@outlook.com',
                'contacts' => array(
                    array(
                        'name' => 'Nicola Asmussen',
                        'phone' => '(+021) 645-099',
                        'email' => 'achilleschristchurch@outlook.com',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Heather McGill',
                        'phone' => '(+027) 212-6321',
                        'is_primary' => false
                    )
                )
            ),
            // More New Zealand chapters
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Dunedin',
                'city' => 'Dunedin',
                'contacts' => array(
                    array(
                        'name' => 'Stacey Pearson',
                        'phone' => '(+021) 645-099',
                        'email' => 'dunedin@achillesnewzealand.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Hamilton',
                'city' => 'Hamilton',
                'contacts' => array(
                    array(
                        'name' => 'Peter Loft',
                        'phone' => '(+021) 355-866',
                        'email' => 'hamilton@achillesnewzealand.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Invercargill',
                'city' => 'Invercargill',
                'email' => 'invercargill@achillesnewzealand.org',
                'contacts' => array(
                    array(
                        'name' => 'Greg Houkamau',
                        'phone' => '(+022) 186-1824',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Debbie Houkamau',
                        'phone' => '(+027) 273-2938',
                        'is_primary' => false
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Rotorua',
                'city' => 'Rotorua',
                'contacts' => array(
                    array(
                        'name' => 'Faustinah Ndlovu',
                        'phone' => '(+021) 214-3452',
                        'email' => 'rotorua@achillesnewzealand.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Tauranga',
                'city' => 'Tauranga',
                'contacts' => array(
                    array(
                        'name' => 'Victoria Wicks-Brown',
                        'phone' => '(+027) 672-1917',
                        'email' => 'tauranga@achillesnewzealand.org',
                        'is_primary' => true
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Wellington',
                'city' => 'Wellington',
                'contacts' => array(
                    array(
                        'name' => 'Will Bell',
                        'phone' => '(+027) 341-7865',
                        'email' => 'achilleswellington@gmail.com',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Matt McNeil',
                        'phone' => '(+021) 456-470',
                        'email' => 'matt@thedigitalcafe.co.nz',
                        'is_primary' => false
                    )
                )
            ),
            array(
                'country_code' => 'NZ',
                'region_code' => 'APAC',
                'name' => 'Whangarei',
                'city' => 'Whangarei',
                'contacts' => array(
                    array(
                        'name' => 'Craig Jessop',
                        'phone' => '(+021) 780-067',
                        'email' => 'whangarei@achillesnewzealand.org',
                        'is_primary' => true
                    )
                )
            ),
            // Norway
            array(
                'country_code' => 'NO',
                'region_code' => 'EU',
                'name' => 'Main Office',
                'is_headquarters' => true,
                'website' => 'www.achillesnorway.no',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/AchillesNorway/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Bedir Yiyit',
                        'title' => 'President',
                        'phone' => '(+47) 9987-3434',
                        'email' => 'post@achillesnorway.no',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Ivar Wigaard',
                        'title' => 'Board Member',
                        'email' => 'wigaa@hotmail.com',
                        'is_primary' => false
                    ),
                    array(
                        'name' => 'Johan Stanghelle',
                        'phone' => '(+47) 9822-4331',
                        'email' => 'johanstanghelle@hotmail.com',
                        'is_primary' => false
                    )
                )
            ),

// Panama
            array(
                'country_code' => 'PA',
                'region_code' => 'NA',
                'name' => 'Panama Chapter',
                'phone' => '+507 6614 0649',
                'email' => 'ge_petterson@yahoo.com',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/achillespty/',
                    'instagram' => 'https://www.instagram.com/AchillesPanama/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Geraldine Petterson',
                        'phone' => '+507 6614 0649',
                        'email' => 'ge_petterson@yahoo.com',
                        'is_primary' => true
                    )
                )
            ),

            // Peru
            array(
                'country_code' => 'PE',
                'region_code' => 'SA',
                'name' => 'Lima',
                'city' => 'Lima',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/goachillesperu/'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Claudia Gamarra Verástegui',
                        'phone' => '(+51) 944-494-351',
                        'email' => 'cloudartgraphics@gmail.com',
                        'is_primary' => true
                    )
                )
            ),

            // Russia
            array(
                'country_code' => 'RU',
                'region_code' => 'EU',
                'name' => 'St. Petersburg',
                'city' => 'St. Petersburg',
                'website' => 'http://vk.com/club61902879',
                'contacts' => array(
                    array(
                        'name' => 'Dima Pavlov',
                        'phone' => '(+7) 911-019-8971',
                        'email' => 'achillesdima-p@mail.ru',
                        'is_primary' => true
                    )
                )
            ),
            // South Africa
            array(
                'country_code' => 'ZA',
                'region_code' => 'AF',
                'name' => 'KwaZulu-Natal',
                'social_media' => array(
                    'facebook' => 'https://www.facebook.com/groups/100764406670925'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Braam Mouton',
                        'phone' => '(+27) 8244-86841',
                        'email' => 'moutonb@mweb.co.za',
                        'is_primary' => true
                    ),
                    array(
                        'name' => 'Denis Tabakin',
                        'phone' => '(+011) 2782-456-1773',
                        'email' => 'denistabakin@gmail.com',
                        'is_primary' => false
                    )
                )
            ),

            // United Kingdom
            array(
                'country_code' => 'GB',
                'region_code' => 'EU',
                'name' => 'London',
                'city' => 'London',
                'email' => 'achillesuk@achillesinternational.org',
                'social_media' => array(
                    'facebook' => 'https://m.facebook.com/groups/414743312609938'
                ),
                'contacts' => array(
                    array(
                        'name' => 'Chris Blackabee',
                        'phone' => '(+074) 0187-3088',
                        'email' => 'achillesuk@achillesinternational.org',
                        'is_primary' => true
                    )
                ),
                'metadata' => array(
                    'facebook_note' => 'This is a private Facebook group; please contact leader to join'
                )
            )
        );

        foreach ($chapters as $chapterData) {
            $country = SystemCountry::where('iso2', $chapterData['country_code'])->first();
            $region = SystemRegion::where('code', $chapterData['region_code'])->first();

            if ($country && $region) {
                $chapter = SystemChapter::create(array(
                    'system_country_id' => $country->id,
                    'system_region_id' => $region->id,
                    'name' => $chapterData['name'],
                    'city' => isset($chapterData['city']) ? $chapterData['city'] : null,
                    'email' => isset($chapterData['email']) ? $chapterData['email'] : null,
                    'phone' => isset($chapterData['phone']) ? $chapterData['phone'] : null,
                    'website' => isset($chapterData['website']) ? $chapterData['website'] : null,
                    'social_media' => isset($chapterData['social_media']) ? $chapterData['social_media'] : null,
                    'active' => true
                ));

                if (isset($chapterData['contacts'])) {
                    foreach ($chapterData['contacts'] as $contactData) {
                        SystemChapterContact::create(array_merge(
                            $contactData,
                            array('system_chapter_id' => $chapter->id)
                        ));
                    }
                }
            }
        }
//        $this->command->info(class_basename(static::class) . ' seeded successfully!');
    }
}
