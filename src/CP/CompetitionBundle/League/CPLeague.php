<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 12/03/2016
 * Time: 16:48
 */

namespace CP\CompetitionBundle\League;

use CP\CompetitionBundle\Entity\Game;
use CP\CompetitionBundle\Entity\Round;
use CP\CompetitionBundle\Entity\RoundRobin;
use CP\CompetitionBundle\Entity\Competition;
use Doctrine\ORM\Query\ResultSetMapping;
class CPLeague
{

    private $doctrine;
    private $em;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

    }

    /**
     *
     * Permet de génerer une league et donc d'ajouter l'objet RoundRobin à l'entité Competition
     * @param $competition
     * @param $robinplayers Le tableau des joueurs
     * @param $size
     * @return mixed
     */

    public function LeagueGenerator($competition,$robinplayers,$size){
        $this->em = $this->doctrine->getManager();



            $roundRobin = new RoundRobin();
            $roundRobin->setName(0);
            $roundRobin->setNbJoueurs($size);
            $this->em->persist($roundRobin);


            $rounds=$this->roundRobin($robinplayers);
            foreach($rounds as $day => $games){
                foreach($games as $play){
                    $round = new Round();
                    $this->em->persist($round);
                    $round->setNumRound($day);
                    $game = new Game();
                    $game->setJoueur1($play["Home"]);
                    $game->setJoueur2($play["Away"]);
                    $game->setEtat(1);
                    $round->setGame($game);
                    $roundRobin->addRound($round);

                }
            }
            $this->em->persist($competition);
            $competition->addRoundRobin($roundRobin);

        $this->em->flush();
        return $competition;
    }

    /**
     *
     * Create a round robin of teams or numbers
     *
     * @param    array    $teams
     * @return    $array
     *
     */
    function roundRobin( array $teams ){

        if (count($teams)%2 != 0){
            array_push($teams,"bye");
        }
        $away = array_splice($teams,(count($teams)/2));
        $home = $teams;
        $round = array();
        for ($i=0; $i < count($home)+count($away)-1; $i++)
        {
            for ($j=0; $j<count($home); $j++)
            {
                $round[$i][$j]["Home"]=$home[$j];
                $round[$i][$j]["Away"]=$away[$j];
            }
            if(count($home)+count($away)-1 > 2)
            {
                $s = array_splice( $home, 1, 1 );
                $slice = array_shift( $s  );
                array_unshift($away,$slice );
                array_push( $home, array_pop($away ) );
            }
        }
        return $round;
    }
    public function game_valid_simple($emanage, $game){


        if($game->getScore1()>$game->getScore2()){
            $winner = $game->getJoueur1();
        }
        else if($game->getScore1()==$game->getScore2()){
            $winner = false;
        }
        else {
            $winner = $game->getJoueur2();
        }

        $round = $emanage->getRepository('CPCompetitionBundle:Round')->findOneByGame($game->getId());


        $emanage->flush();
    }

    /**
     * Cette fonction effectue une requete SQL qui permet de calculer à la volé le classement de la league
     *
     * @param $competition
     * @return array
     */
    public function roundRobinRankingSQL($competition){
        $sql="SELECT ID, COUNT(*) MatchPlayed,
SUM( CASE WHEN MatchResult = 'Won' THEN 3 WHEN MatchResult = 'Tied' THEN 1 ELSE 0 END ) as Points,
SUM( CASE WHEN MatchResult = 'Won' THEN 1 ELSE 0 END ) MatchWon,
SUM( CASE WHEN MatchResult = 'Tied' THEN 1 ELSE 0 END ) MatchTied,
SUM( CASE WHEN MatchResult = 'Lost' THEN 0 ELSE 0 END ) MatchLost,
SUM(goals_scored-goals_conceded) AS diff
FROM
(
    SELECT joueur1_id AS ID,
	CASE WHEN game.score1>game.score2 THEN 'Won'
	WHEN game.score1<game.score2 THEN 'Lost'
	WHEN game.score1=game.score2 THEN 'Tied'
	END AS MatchResult,game.score1 AS goals_scored, game.score2 as goals_conceded
	FROM game, round, round_robin, round_robin_round
	WHERE game.id=round.game_id and round_robin.id = round_robin_round.round_robin_id and round.id = round_robin_round.round_id and round_robin.id = ?

	UNION ALL
	SELECT joueur2_id AS ID,
	CASE WHEN game2.score1<game2.score2 THEN 'Won'
	WHEN game2.score1>game2.score2 THEN 'Lost'
	WHEN game2.score1=game2.score2 THEN 'Tied'
	END AS MatchResult, game2.score2 AS goals_scored, game2.score1 as goals_conceded
	FROM game as game2, round as round2, round_robin as round_robin2, round_robin_round as round_robin_round2
	WHERE game2.id=round2.game_id and round_robin2.id = round_robin_round2.round_robin_id and round2.id = round_robin_round2.round_id and round_robin2.id = ?

) A
GROUP BY ID
ORDER BY Points desc, diff desc";
        $em=$this->doctrine->getManager();
        $ranking = array();
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('ID', 'team');
        $rsm->addScalarResult('Points', 'points');
        $rsm->addScalarResult('diff', 'diff');
        foreach($competition->getRoundRobins()as $roundRobin) {

            $query = $em->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $roundRobin->getId());
            $query->setParameter(2, $roundRobin->getId());

            $ranking[] = $query->getResult();
        }
return $ranking;
}


}