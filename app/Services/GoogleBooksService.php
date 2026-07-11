<?php

namespace App\Services;

class GoogleBooksService
{
    private const API_BASE = 'https://www.googleapis.com/books/v1/volumes';

    /**
     * Recherche des livres en accès libre complet (domaine public).
     *
     * @return array<int, array{
     *   volume_id: string,
     *   title: string,
     *   author: string,
     *   description: string,
     *   cover_url: ?string,
     *   page_count: ?int,
     *   categories: string,
     *   preview_link: ?string
     * }>
     */
    public function searchFreeBooks(string $query, int $maxResults = 20): array
    {
        $query = trim($query) !== '' ? $query : 'fiction';

        $params = [
            'q' => $query,
            'filter' => 'free-ebooks',
            'maxResults' => min($maxResults, 40),
            'printType' => 'books',
        ];
        $this->appendApiKey($params);

        $url = self::API_BASE . '?' . http_build_query($params);
        $response = $this->fetch($url);

        if ($response === null || empty($response['items'])) {
            return [];
        }

        $books = [];
        foreach ($response['items'] as $item) {
            $parsed = $this->parseVolume($item);
            if ($parsed !== null) {
                $books[] = $parsed;
            }
        }

        return $books;
    }

    /**
     * Récupère un volume précis par son identifiant Google Books.
     */
    public function findVolume(string $volumeId): ?array
    {
        $params = [];
        $this->appendApiKey($params);
        $suffix = !empty($params) ? '?' . http_build_query($params) : '';

        $url = self::API_BASE . '/' . urlencode($volumeId) . $suffix;
        $response = $this->fetch($url);

        if ($response === null) {
            return null;
        }

        return $this->parseVolume($response);
    }

    private function appendApiKey(array &$params): void
    {
        $key = $_ENV['GOOGLE_BOOKS_API_KEY'] ?? '';
        if ($key !== '') {
            $params['key'] = $key;
        }
    }

    private function parseVolume(array $item): ?array
    {
        $volumeInfo = $item['volumeInfo'] ?? null;
        if ($volumeInfo === null) {
            return null;
        }

        $imageLinks = $volumeInfo['imageLinks'] ?? [];
        $coverUrl = $imageLinks['thumbnail']
            ?? $imageLinks['smallThumbnail']
            ?? null;

        // Google renvoie parfois des URLs en http:// — on force https pour éviter le contenu mixte
        if ($coverUrl !== null) {
            $coverUrl = str_replace('http://', 'https://', $coverUrl);
        }

        $authors = $volumeInfo['authors'] ?? [];

        return [
            'volume_id' => $item['id'] ?? '',
            'title' => $volumeInfo['title'] ?? 'Sans titre',
            'author' => !empty($authors) ? implode(', ', $authors) : 'Auteur inconnu',
            'description' => $volumeInfo['description'] ?? '',
            'cover_url' => $coverUrl,
            'page_count' => $volumeInfo['pageCount'] ?? null,
            'categories' => !empty($volumeInfo['categories']) ? implode(', ', $volumeInfo['categories']) : '',
            'preview_link' => $volumeInfo['previewLink'] ?? null,
        ];
    }

    private function fetch(string $url, int $retries = 2): ?array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);

        for ($attempt = 0; $attempt <= $retries; $attempt++) {
            $raw = @file_get_contents($url, false, $context);
            if ($raw === false) {
                continue;
            }

            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                continue;
            }

            // Erreur temporaire côté Google (503/backendFailed) : on retente,
            // sauf s'il s'agit d'une erreur définitive (quota, clé invalide...).
            if (isset($decoded['error'])) {
                $code = $decoded['error']['code'] ?? 0;
                $isTransient = in_array($code, [500, 502, 503, 504], true);

                if ($isTransient && $attempt < $retries) {
                    usleep(300000); // 300ms avant de retenter
                    continue;
                }

                return null;
            }

            return $decoded;
        }

        return null;
    }
}