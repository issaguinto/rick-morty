<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RickAndMortyApiClient
{
    private const BASE_URL = 'https://rickandmortyapi.com/api';

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    public function getCharactersByDimension(string $dimension): array
    {
        try {
            $locations = [];
            $page = 1;
            $maxPages = 3;

            do {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/location', [
                    'query' => ['page' => $page],
                    'timeout' => 5
                ])->toArray();

                foreach ($response['results'] as $location) {
                    if ($location['dimension'] === $dimension) {
                        $locations[] = $location;
                    }
                }

                $page++;
            } while ($response['info']['next'] !== null && $page <= $maxPages);

            if (empty($locations)) {
                return [];
            }

            $characterUrls = [];
            foreach ($locations as $location) {
                $characterUrls = array_merge($characterUrls, $location['residents']);
            }
            $characterUrls = array_unique($characterUrls);

            // Batch character requests
            $characters = [];
            $batchSize = 10;
            foreach (array_chunk($characterUrls, $batchSize) as $urlBatch) {
                $responses = [];
                
                // Make concurrent requests for each batch
                foreach ($urlBatch as $url) {
                    $responses[] = $this->httpClient->request('GET', $url, ['timeout' => 5]);
                }

                // Wait for all requests in the batch
                foreach ($responses as $response) {
                    try {
                        $characters[] = $response->toArray();
                    } catch (\Exception $e) {
                        continue; // Skip failed requests
                    }
                }
            }

            return $characters;
        } catch (\Exception $e) {
            return [];
        }

        return $characters;
    }

    public function getCharactersByLocation(string $location): array
    {
        try {
            $response = $this->httpClient->request('GET', self::BASE_URL . '/location', [
                'query' => ['name' => $location]
            ])->toArray();

            if (!empty($response['results'])) {
                $characters = [];
                foreach ($response['results'][0]['residents'] as $characterUrl) {
                    $characters[] = $this->httpClient->request('GET', $characterUrl)->toArray();
                }
                return $characters;
            }

            // If exact match fails, try to search through all locations
            $page = 1;
            do {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/location', [
                    'query' => ['page' => $page]
                ])->toArray();

                foreach ($response['results'] as $loc) {
                    if (stripos($loc['name'], $location) !== false) {
                        $characters = [];
                        foreach ($loc['residents'] as $characterUrl) {
                            $characters[] = $this->httpClient->request('GET', $characterUrl)->toArray();
                        }
                        return $characters;
                    }
                }

                $page++;
            } while ($response['info']['next'] !== null);

            return [];
        } catch (\Exception $e) {
            return [];
        }

        return $characters;
    }

    public function getCharactersByEpisode(string $episodeName): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . '/episode', [
            'query' => ['name' => $episodeName]
        ])->toArray();

        if (empty($response['results'])) {
            return [];
        }

        $characters = [];
        foreach ($response['results'][0]['characters'] as $characterUrl) {
            $characters[] = $this->httpClient->request('GET', $characterUrl)->toArray();
        }

        return $characters;
    }

    public function getCharacter(string $id): array
    {
        return $this->httpClient->request('GET', self::BASE_URL . '/character/' . $id)->toArray();
    }

    public function getAllLocations(): array
    {
        $locations = [];
        $page = 1;
        $maxPages = 3;
        try {
            do {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/location', [
                    'query' => ['page' => $page],
                    'timeout' => 5
                ])->toArray();

                $locations = array_merge($locations, $response['results']);
                $page++;
            } while ($response['info']['next'] !== null && $page <= $maxPages);

            usort($locations, fn($a, $b) => strcmp($a['name'], $b['name']));

            return $locations;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAllEpisodes(): array
    {
        $episodes = [];
        $page = 1;
        $maxPages = 3;

        try {
            do {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/episode', [
                    'query' => ['page' => $page],
                    'timeout' => 5
                ])->toArray();

                $episodes = array_merge($episodes, $response['results']);
                $page++;
            } while ($response['info']['next'] !== null && $page <= $maxPages);

            usort($episodes, function($a, $b) {
                return strcmp($a['episode'], $b['episode']);
            });

            return $episodes;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getEpisode(string $id): array
    {
        try {
            return $this->httpClient->request('GET', self::BASE_URL . '/episode/' . $id)->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getEpisodeWithCharacters(string $id): array
    {
        try {
            $episode = $this->httpClient->request('GET', self::BASE_URL . '/episode/' . $id)->toArray();
            
            if (empty($episode)) {
                return [];
            }

            // If no characters in episode, return episode data without character_details
            if (empty($episode['characters'])) {
                $episode['character_details'] = [];
                return $episode;
            }

            $characters = [];
            $batchSize = 10;
            $characterUrls = $episode['characters'];
            
            foreach (array_chunk($characterUrls, $batchSize) as $urlBatch) {
                $responses = [];
                
                foreach ($urlBatch as $url) {
                    try {
                        $responses[] = $this->httpClient->request('GET', $url, ['timeout' => 5]);
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                foreach ($responses as $response) {
                    try {
                        $characterData = $response->toArray();
                        if (!empty($characterData['name'])) { // Validate character data
                            $characters[] = $characterData;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            $episode['character_details'] = $characters;
            return $episode;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function searchEpisodes(string $name): array
    {
        try {
            $response = $this->httpClient->request('GET', self::BASE_URL . '/episode', [
                'query' => ['name' => $name],
                'timeout' => 5
            ])->toArray();

            return $response['results'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
}