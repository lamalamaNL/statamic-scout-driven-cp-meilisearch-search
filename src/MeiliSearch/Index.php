<?php

namespace LamaLamaNl\StatamicScoutDrivenCpMeilisearchSearch\MeiliSearch;

use Illuminate\Support\Str;
use MeiliSearch\Client;
use MeiliSearch\Exceptions\ApiException;
use Statamic\Assets\Asset;
use Statamic\Auth\User;
use Statamic\Entries\Entry;
use Statamic\Search\Documents;
use Statamic\Search\Index as BaseIndex;
use Statamic\Taxonomies\LocalizedTerm;
use Statamic\Taxonomies\Term;

class Index extends BaseIndex
{
    protected $client;

    public function __construct(Client $client, $name, $config)
    {
        $this->client = $client;

        parent::__construct($name, $config);
    }

    public function search($query)
    {
        return (new Query($this))->query($query);
    }

    public function insert($document)
    {
        // No action here. Let laravel Scout handle thi
    }

    public function delete($document)
    {
        // No action here. Let laravel Scout handle thi
    }

    public function exists()
    {
        // No action here. Let laravel Scout handle thi
    }

    protected function insertDocuments(Documents $documents)
    {
        // No action here. Let laravel Scout handle thi
    }

    protected function deleteIndex()
    {
        // No action here. Let laravel Scout handle thi
    }

    protected function createIndex()
    {
        // No action here. Let laravel Scout handle thi
    }

    public function update()
    {
        // No action here. Let laravel Scout handle thi
    }

    public function searchUsingApi($query)
    {
        try {
            $queryParams = [];
            $index = $this->getIndex();
            $config = config('statamic.search.meilisearch-cp-collections.' . $this->name);
            if ($config) {
                $index = $this->client->index($config['index']);
                if ($config['filter']) {
                    $queryParams = ['filter' => $config['filter']];
                }
            }

            $searchResults = $index->search($query, $queryParams);
        } catch (\Exception $e) {
            throw $e;
        }

        return collect($searchResults->getHits())->map(function ($hit) {
            $hit['reference'] = $hit['id'];

            return $hit;
        });
    }

    private function getIndex()
    {
        return $this->client->index($this->name);
    }

    private function getDefaultFields(Entry|Term|LocalizedTerm|Asset|User $entry)
    {
        return [
            'id' => $this->getSafeDocmentID($entry->reference()),
            'reference' => $entry->reference(),
        ];
    }

    private function handleMeiliSearchException($e, $method)
    {
        throw $e;
    }

    /**
     * Get the document ID for the given entry.
     * As a document id is only allowed to be an integer or string composed only of alphanumeric characters (a-z A-Z 0-9), hyphens (-), and underscores (_) we need to make sure that the ID is safe to use.
     * More under https://docs.meilisearch.com/reference/api/error_codes.html#invalid-document-id
     *
     * @param string $entryReference
     * @return string
     */
    private function getSafeDocmentID(string $entryReference)
    {
        return Str::of($entryReference)
            ->explode('::')
            ->map(function ($part) {
                return Str::slug($part);
            })
            ->implode('---');
    }
}
