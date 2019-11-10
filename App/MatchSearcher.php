<?php


namespace App;


use DiDom\Document;
use GuzzleHttp\Client;

class MatchSearcher
{
    /**
     * Найти матчи с dotaix.xyz
     */
    public function findMatches()
    {
        for ($i = 1; $i <= 1248; $i++) {
            $matchIds = [];
            echo "\rpage $i";
            $document = new Document($this->getPage('https://dotaix.xyz/page/' . $i . '/'));
            $matches = $document->find('article > div > p');
            foreach ($matches as $match) {
                $lines = explode("\n", $match->html());
                $matchIds[] = preg_replace('/[^0-9]/', '', $lines[1]);
            }
            file_put_contents(__DIR__ . '/../ix.csv', implode(',', $matchIds) . ',', FILE_APPEND);
        }
    }

    /**
     * Получить страницу
     * @param string $url
     * @return string
     */
    private function getPage(string $url)
    {
        $client = new Client();
        $response = $client->get($url);

        return $response->getBody()->getContents();
    }
}