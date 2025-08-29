<?php

namespace App\Controller;

use App\Service\RickAndMortyApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    public function __construct(
        private readonly RickAndMortyApiClient $apiClient
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $locations = $this->apiClient->getAllLocations();
        $episodes = $this->apiClient->getAllEpisodes();
        
        return $this->render('default/index.html.twig', [
            'locations' => $locations,
            'episodes' => $episodes
        ]);
    }

    #[Route('/episodes', name: 'app_episodes')]
    public function episodes(): Response
    {
        $episodes = $this->apiClient->getAllEpisodes();
        
        return $this->render('episodes/index.html.twig', [
            'episodes' => $episodes
        ]);
    }

    // API endpoints for AJAX calls
    #[Route('/api/characters/dimension', name: 'api_characters_by_dimension')]
    public function apiByDimension(Request $request): Response
    {
        $dimension = $request->query->get('dimension');
        $characters = $dimension ? $this->apiClient->getCharactersByDimension($dimension) : [];

        return $this->json([
            'characters' => $characters,
            'title' => 'Characters in ' . $dimension,
            'filterType' => 'dimension',
            'filterValue' => $dimension
        ]);
    }

    #[Route('/api/characters/location', name: 'api_characters_by_location')]
    public function apiByLocation(Request $request): Response
    {
        $location = $request->query->get('location');
        $characters = $location ? $this->apiClient->getCharactersByLocation($location) : [];

        return $this->json([
            'characters' => $characters,
            'title' => 'Characters in ' . $location,
            'filterType' => 'location',
            'filterValue' => $location
        ]);
    }

    #[Route('/api/characters/episode', name: 'api_characters_by_episode')]
    public function apiByEpisode(Request $request): Response
    {
        $episodeName = $request->query->get('episode');
        $characters = $episodeName ? $this->apiClient->getCharactersByEpisode($episodeName) : [];

        return $this->json([
            'characters' => $characters,
            'title' => 'Characters in ' . $episodeName,
            'filterType' => 'episode',
            'filterValue' => $episodeName
        ]);
    }

    #[Route('/character/{id}', name: 'app_character_detail')]
    public function detail(string $id): Response
    {
        $character = $this->apiClient->getCharacter($id);

        return $this->render('character/detail.html.twig', [
            'character' => $character,
        ]);
    }

    #[Route('/episode/{id}', name: 'app_episode_detail')]
    public function episodeDetail(string $id): Response
    {
        $episode = $this->apiClient->getEpisodeWithCharacters($id);

        return $this->render('episode/detail.html.twig', [
            'episode' => $episode,
        ]);
    }

    #[Route('/api/episodes/search', name: 'api_episodes_search')]
    public function apiSearchEpisodes(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $episodes = $query ? $this->apiClient->searchEpisodes($query) : [];

        return $this->json([
            'episodes' => $episodes,
            'total' => count($episodes)
        ]);
    }
}
