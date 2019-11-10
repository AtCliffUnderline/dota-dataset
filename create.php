<?php
//var l=document.querySelectorAll('body > div.container-outer.seemsgood > div.container-inner.container-inner-content > div.content-inner > section.row-12 > div > article > div:nth-child(1) > table > tbody > tr > td.series-game-icons.r-none-mobile > div > span > a');let a = [];for(let i=0;i<l.length;i++){a.push(l[i].href.replace(/^\D+/g,''));}console.log(a.join(','));
use App\GetMatchInfo;

require_once 'vendor/autoload.php';

$matches = explode(',', file_get_contents('matches.csv'));
foreach ($matches as $key => $matchId) {
    echo "\r".'parsed ' . $key . ' matches out of ' . sizeof($matches);
    $parser = new GetMatchInfo($matchId);
    $parser->parseData();
}
