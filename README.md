# Statamic Control Panel search powered by Meilisearch


This package makes searching possible trough meilisearch in the 
statamic control panel. It does not sync any data with meilsearch 
(that is done trough Laravel Scout). It only makes searching possible.
It uses one meilisearch index for all statamic entries, but gives
you the option to configure filters per collection type.

Based on: https://github.com/elvenstar/statamic-meilisearch


## How to configure
Make sure you have the following in config/statamic/search
```php


    /*
       Make sure your STATAMIC_DEFAULT_SEARCH_INDEX is set to your index that Laravel Scout uses.
        This is used for the global search in the statamic control panel 
    */

    'default' => env('STATAMIC_DEFAULT_SEARCH_INDEX', 'jre-search-ed-local'),

    /*
    |--------------------------------------------------------------------------
    | Search Indexes
    |--------------------------------------------------------------------------
    |
    | Here you can define all of the available search indexes.
    |
    */

    'indexes' => [
        // Make sure your main index uses the meilisearch driver
        'jre-search-ed-local' => [
            'driver' => 'meilisearch',
            'fields' => ['title', 'slug'],
        ],

    ],
    
    /*
        Define your the search for your collections here. Make sure the filter is correct
        To point statamic the use this config make sure you statamic index in the collection point to a 
        collection that is  configured here
    
        E.g.: content/collections/chefs.yaml --> search_index: chefs
        E.g.: content/collections/restaurants.yaml --> search_index: restaurants
    */
    'meilisearch-cp-collections' => [
        'chefs' => [
            'driver' => 'meilisearch',
            'fields' => ['title', 'slug'],
            'index' => 'jre-search-ed-local',
            'filter' => "type = 'chefs'"
        ],
        'restaurants' => [
            'driver' => 'meilisearch',
            'fields' => ['title', 'slug'],
            'index' => 'jre-search-ed-local',
            'filter' => "type = 'restaurants'"
        ],
    ],


```
