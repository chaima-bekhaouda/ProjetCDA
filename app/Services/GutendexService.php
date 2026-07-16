<?php

namespace App\Services;

class GutendexService
{
    private const API_BASE = 'https://gutendex.com/books';

    /**
     * Recherche des livres dans le catalogue Project Gutenberg.
     *
     * @return array<int, array{
     *   volume_id: string,
     *   title: string,
     *   author: string,
     *   cover_url: ?string,
     *   page_count: null,
     *   categories: string,
     *   preview_link: string,
     *   embeddable: bool
     * }>
     */
    public function searchBooks(string $query, int $maxResults = 20): array
    {
        $query = trim($query) !== '' ? $query : 'fiction';

        $url = self::API_BASE . '?' . http_build_query([
            'search' => $query,
            'languages' => 'fr,en',
        ]);

        $response = $this->fetchJson($url);

        if ($response === null || empty($response['results'])) {
            return [];
        }

        $books = array_slice($response['results'], 0, $maxResults);

        return array_map([$this, 'parseBook'], $books);
    }

    /**
     * Récupère un livre précis par son identifiant Gutendex.
     */
    public function findBook(string $id): ?array
    {
        $response = $this->fetchJson(self::API_BASE . '/' . urlencode($id));

        if ($response === null) {
            return null;
        }

        return $this->parseBook($response);
    }

    /**
     * Télécharge et nettoie le texte intégral d'un livre (retire le
     * préambule/postambule légal de Project Gutenberg).
     */
    public function fetchFullText(string $textUrl): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'ignore_errors' => true,
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36\r\n",
            ],
        ]);

        $raw = @file_get_contents($textUrl, false, $context, 0, 5_000_000); // limite 5 Mo
        if ($raw === false || trim($raw) === '') {
            return null;
        }

        return $this->stripGutenbergBoilerplate($raw);
    }

    private function parseBook(array $item): array
    {
        $formats = $item['formats'] ?? [];

        $textUrl = null;
        foreach ($formats as $mime => $url) {
            if (str_starts_with($mime, 'text/plain')) {
                $textUrl = $url;
                break;
            }
        }

        $coverUrl = null;
        foreach ($formats as $mime => $url) {
            if (str_starts_with($mime, 'image/')) {
                $coverUrl = $url;
                break;
            }
        }

        $authors = array_map(
            static fn(array $author): string => $author['name'] ?? '',
            $item['authors'] ?? []
        );
        $authors = array_filter($authors);

        $subjects = array_slice($item['subjects'] ?? [], 0, 3);

        return [
            'volume_id' => (string) ($item['id'] ?? ''),
            'title' => $item['title'] ?? 'Sans titre',
            'author' => !empty($authors) ? implode(', ', $authors) : 'Auteur inconnu',
            'cover_url' => $coverUrl,
            'page_count' => null,
            'categories' => !empty($subjects) ? implode(', ', $subjects) : '',
            'preview_link' => 'https://www.gutenberg.org/ebooks/' . ($item['id'] ?? ''),
            'embeddable' => $textUrl !== null,
            'text_url' => $textUrl,
        ];
    }

    /**
     * Retire le préambule/postambule légal standard de Project Gutenberg
     * pour ne garder que le texte de l'œuvre elle-même.
     */
    private function stripGutenbergBoilerplate(string $text): string
    {
        $startPattern = '/\*\*\*\s*START OF (THE|THIS) PROJECT GUTENBERG EBOOK.*?\*\*\*/is';
        $endPattern = '/\*\*\*\s*END OF (THE|THIS) PROJECT GUTENBERG EBOOK.*?\*\*\*/is';

        $start = 0;
        if (preg_match($startPattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            $start = $matches[0][1] + strlen($matches[0][0]);
        }

        $end = strlen($text);
        if (preg_match($endPattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            $end = $matches[0][1];
        }

        $body = substr($text, $start, $end - $start);

        return trim($body) !== '' ? trim($body) : trim($text);
    }

    private function fetchJson(string $url, int $retries = 2): ?array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 6,
                'ignore_errors' => true,
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36\r\n"
                          . "Accept: application/json\r\n",
            ],
        ]);

        for ($attempt = 0; $attempt <= $retries; $attempt++) {
            error_clear_last();
            $raw = @file_get_contents($url, false, $context);

            if ($raw === false) {
                // DIAGNOSTIC TEMPORAIRE — à retirer une fois le problème identifié.
                // Capture la vraie erreur au lieu de la laisser silencieuse.
                $lastError = error_get_last();
                error_log(sprintf(
                    '[Gutendex] Échec sur %s (tentative %d) : %s',
                    $url,
                    $attempt + 1,
                    $lastError['message'] ?? 'aucun détail disponible'
                ));
                continue;
            }

            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                error_log(sprintf(
                    '[Gutendex] Réponse reçue mais JSON invalide sur %s (tentative %d) : %s',
                    $url,
                    $attempt + 1,
                    substr($raw, 0, 200)
                ));
                if ($attempt < $retries) {
                    usleep(300000);
                    continue;
                }
                return null;
            }

            return $decoded;
        }

        return null;
    }
}
