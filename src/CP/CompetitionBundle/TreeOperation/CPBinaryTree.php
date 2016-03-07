<?php
namespace CP\CompetitionBundle\TreeOperation;

use CP\CompetitionBundle\Entity\Game;
use CP\CompetitionBundle\Entity\Round;

class CPBinaryTree
{
    private $doctrine;
    private $em;
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

    }
public function simpleTreeGenerator($taille)
{
    $this->em = $this->doctrine->getManager();

    $fatherRound = $this->genererArbre(null, $taille);
    $this->em->flush();
    /*$tab = array();
    $tab = $this->parcourir_arbre($fatherRound, 0, $tab);
    $jstab = array(
                        "team"=>array(
                                    array("Team1","Team2"),array("Team3","Team4")
                                )
    ,                    "score"=>array(
                                            array("score1","score2"),array("score3","score4")));
    $tab[1][0] = new Game();
    $tab[1][0]->setTeam1("om");
    $tab[1][0]->setTeam1("psg");
    $tab[1][0]->setScore(2);
    $jstab = $this->bracketJSData($tab);
    return $jstab; */

    return $fatherRound;
}

public function simpleTreeJS($competitionID){
    $repository = $this->doctrine->getManager()->getRepository('CPCompetitionBundle:Competition');
    $competition = $repository->find($competitionID);
$fatherRound = $competition->getFatherRound();
    $tab = array();
    $tab = $this->parcourir_arbre($fatherRound, 0, $tab);
    $jstab = $this->bracketJSData($tab);
    return $jstab;
}


public function genererArbre($round,$taille)
{
    $mainRound = new Round();
    $this->em->persist($mainRound);
    $mainRound->setParentRound($round);
    $mainRound->setNumRound($taille/2);

    if($taille<=2){
        $game = new Game();
        $game->setTeam1("test");
        $game->setTeam2("test2");
        $game->setScore1(0);
        $game->setScore2(0);
        $mainRound->setGame($game);

    }
    else{

        $mainRound->setRightRound($this->genererArbre($mainRound, $taille/2,$this->em));
        $mainRound->setLeftRound($this->genererArbre($mainRound, $taille/2,$this->em));


    }

    return $mainRound;
}

public function parcourir_arbre($round, $level,$tab){

    if(!isset($tab[$level])) {
        $tab[$level] = array();
    }
    $tab[$level][count( $tab[$level])] = $round->getGame();

    if($round->getRightRound()!=null){
        $tab = $this->parcourir_arbre($round->getRightRound(),$level+1,$tab);
    }

    if($round->getLeftRound()!=null) {
        $tab = $this->parcourir_arbre($round->getLeftRound(), $level + 1, $tab);

    }


    return $tab;

}
public function bracketJSData($tab)
{
    $json = array();
    $json["teams"] = array();
    for ($i = 0; $i < count($tab[count($tab) - 1]); $i = $i + 1) {
        $matchup = array($tab[count($tab) - 1][$i]->getTeam1(), $tab[count($tab) - 1][$i]->getTeam2());
        $json["teams"][$i] = $matchup;
    }


    $resultLevel = 0;
    for ($level = count($tab) - 1; $level >= 0; $level--) {
        global $resultLevel;
        for ($j = 0; $j < count($tab[$level]); $j++) {
            if ($tab[$level][$j] != null) {
                $matchupResult = array($tab[$level][$j]->getScore1(), $tab[$level][$j]->getScore2(),$tab[$level][$j]->getId());
                $json["results"][$resultLevel][$j] = $matchupResult;
            }
        }
        $resultLevel++;
    }
    $json['teams'] = array_values($json['teams']);
    $json['results'] = array_values($json['results']);
    return $json;

}
}
?>