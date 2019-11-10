<?php

namespace App;


use DiDom\Document;
use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;

class GetMatchInfo
{
    /**
     * @var Client
     */
    private $client;
    private $matchId;
    private $matchDataEndpoint = 'https://www.trackdota.com/data/game/%s/core.json';
    private $matchResultEndpoint = 'https://www.trackdota.com/data/game/%s/live.json';

    /**
     * GetMatchInfo constructor.
     * @param int $matchId
     */
    public function __construct(int $matchId)
    {
        $this->client = new Client();
        $this->matchId = $matchId;
    }

    /**
     * Главный метод поиска матчей из csv
     */
    public function parseData()
    {
        if (!$this->isParsingNeeded()) {
            return;
        }

        $url = sprintf($this->matchDataEndpoint, $this->matchId);
        $prematchData = $this->parsePrematchData($this->getPage($url));

        $url = sprintf($this->matchResultEndpoint, $this->matchId);
        $aftermatchData = $this->parseAftermatchData($this->getPage($url));

        $this->saveResult([
            'prematchData' => $prematchData,
            'aftermatchData' => $aftermatchData
        ]);
    }

    /**
     * Получаем информацию, которая доступна до начала матча
     * @param \stdClass $matchData
     * @return array
     */
    private function parsePrematchData(\stdClass $matchData): array
    {
        $rosters = [
            'radiant' => [],
            'dire' => []
        ];

        foreach ($matchData->players as $player) {
            if ($player->team === 0) {
                $rosters['radiant'][] = $player;
            } else if ($player->team === 1) {
                $rosters['dire'][] = $player;
            }
        }

        return [
            'teams' => [
                'radiant' => $matchData->radiant_team,
                'dire' => $matchData->dire_team,
            ],
            'bans' => [
                'radiant' => $matchData->radiant_bans,
                'dire' => $matchData->dire_bans,
            ],
            'picks' => [
                'radiant' => $matchData->radiant_picks,
                'dire' => $matchData->dire_picks,
            ],
            'league' => $matchData->league,
            'rosters' => $rosters
        ];
    }

    /**
     * Получаем итоговую информацию о матче
     * @param \stdClass $matchData
     * @return array
     */
    private function parseAftermatchData(\stdClass $matchData): array
    {
        return [
            'score' => [
                'radiant' => $matchData->radiant->score,
                'dire' => $matchData->dire->score,
            ],
            'duration' => $matchData->duration,
            'winner' => $matchData->winner === 0 ? 'radiant' : 'dire'
        ];
    }

    /**
     * Получаем страницу
     * @param string $url
     * @return \stdClass
     */
    private function getPage(string $url): \stdClass
    {
        $request = $this->client->get($url);

        return json_decode($request->getBody()->getContents());
    }

    /**
     * Проверка, нужно ли парсить файл
     * @return bool
     */
    private function isParsingNeeded()
    {
        return !file_exists('output/' . $this->matchId . '.json');
    }

    /**
     * Сохранить json
     * @param array $result
     */
    private function saveResult(array $result): void
    {
        file_put_contents('output/' . $this->matchId . '.json', \GuzzleHttp\json_encode($result));
    }
}