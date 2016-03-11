<?php
namespace CP\CompetitionBundle\TreeOperation;

use CP\CompetitionBundle\Entity\Game;
use CP\CompetitionBundle\Entity\Round;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Service qui va gérer les arbres de type binaire permettant les tournois avec élimination directe
 *
 * Class CPBinaryTree
 * @package CP\CompetitionBundle\TreeOperation
 */
class CPBinaryTree
{
    private $doctrine;
    private $em;
    private $players;
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

    }

    /**
     * Fonction qui prend en entrée le nombre de joueurs du tournoi  ainsi que les joueurs et qui génère l'arbre correspondant.
     * La fonction retourne le Round "father" qui est le niveau 0 de l'arbre et qui va permettre de parcourir toutes les branches de l'arbre
     *
     * @param $taille
     * @return Round
     */
    public function simpleTreeGenerator($taille, $players)
{
    if( ($taille & ($taille - 1)) != 0){
        throw new NotFoundHttpException('Le nombre de joueurs : "'.$taille.'" n \'est pas valable pour génerer un arbre binaire.');
    }
    $this->em = $this->doctrine->getManager();
    $this->players = $players;

    $fatherRound = $this->genererArbre(null, $taille);
    $this->em->flush();

    return $fatherRound;
}

public function doubleTreeGenerator($taille, $players)
{
    if( ($taille & ($taille - 1)) != 0){
        throw new NotFoundHttpException('Le nombre de joueurs : "'.$taille.'" n \'est pas valable pour génerer un arbre binaire.');
    }
    $this->em = $this->doctrine->getManager();
    $this->players = $players;

    $fatherWinRound = $this->simpleTreeGenerator($taille, $players);
    $fatherWinRounds=array();
    $fatherWinRounds=$this->parcourir_arbre_double($fatherWinRound,0,$fatherWinRounds);
    $firstLevel = count($fatherWinRounds);


        $parentLoseRounds = array();
        $parentLoseRounds[$firstLevel]= array();
        for($i = 0;$i<$taille/2;$i++){

            if($i==0 || ($i %2==0)){
                $round = new Round();
                $this->em->persist($round);

                array_push($parentLoseRounds[$firstLevel],$round);
            }
            $parentLoseRound=$parentLoseRounds[$firstLevel][count($parentLoseRounds[$firstLevel])-1];
            $fatherWinRound = $fatherWinRounds[$firstLevel-1][$i];
           $fatherWinRound->setParentLoserRound( $parentLoseRound);
            if($parentLoseRound->getRightRound() == null){
                $parentLoseRound->setRightRound($fatherWinRound);
            }
            else{
                $parentLoseRound->setLeftRound($fatherWinRound);
            }

        }
        $parentLoseRounds[$firstLevel-1]= array();
        for($i = 0;$i<$taille/4;$i++){
            $round = new Round();
            $this->em->persist($round);

            array_push($parentLoseRounds[$firstLevel-1],$round);

            $parentLoseRound=$parentLoseRounds[$firstLevel-1][count($parentLoseRounds[$firstLevel-1])-1];
            $fatherWinRound = $fatherWinRounds[$firstLevel-2][count($fatherWinRounds[$firstLevel-2]) - $i - 1];
            $fatherWinRound->setParentLoserRound( $parentLoseRound);

                $parentLoseRound->setLeftRound($fatherWinRound);
            $parentLoseRound->setRightRound($parentLoseRounds[$firstLevel][$i]);
           $parentLoseRounds[$firstLevel][$i]->setParentRound( $parentLoseRound);



        }

    if($taille==16){

    }

        $round = new Round();
        $this->em->persist($round);
        $parentLoseRounds[$firstLevel-2]= array();
        array_push($parentLoseRounds[$firstLevel-2],$round);
        for($i = 0;$i<$taille/4;$i++){
            $parentLoseRounds[$firstLevel-1][$i]->setParentRound( $parentLoseRounds[$firstLevel-2][0]);
        }
        $parentLoseRounds[$firstLevel-2][0]->setRightRound($parentLoseRounds[$firstLevel-1][0]) ;
        $parentLoseRounds[$firstLevel-2][0]->setLeftRound($parentLoseRounds[$firstLevel-1][1]) ;



    $round = new Round();
    $this->em->persist($round);
    $parentLoseRounds[$firstLevel-3]= array();
    array_push($parentLoseRounds[$firstLevel-3],$round);
    $parentLoseRounds[$firstLevel-3][0]->setLeftRound($fatherWinRounds[$firstLevel-3][0]) ;
    $parentLoseRounds[$firstLevel-3][0]->setRightRound($parentLoseRounds[$firstLevel-2][0]) ;
    $parentLoseRounds[$firstLevel-2][0]->setParentRound($parentLoseRounds[$firstLevel-3][0]) ;


    $fatherWinRounds[$firstLevel-3][0]->setParentLoserRound($parentLoseRounds[$firstLevel-3][0]);

    $fatherBracketRound = new Round();
    $this->em->persist($fatherBracketRound);
    $fatherWinRounds[$firstLevel-3][0]->setParentRound($fatherBracketRound);

    $parentLoseRounds[$firstLevel-3][0]->setParentRound($fatherBracketRound);
    $fatherBracketRound->setRightRound( $fatherWinRounds[$firstLevel-3][0]);
    $fatherBracketRound->setLeftRound($parentLoseRounds[$firstLevel-3][0]);



    $this->em->flush();
    return $fatherBracketRound;

}

    /**
     * Fonction qui permet de génerer un tableau au format JSON permettant de génerer l'arbre avec JBracket.
     *
     *
     * @param $competitionID
     * @return array
     */
    public function simpleTreeJS($competitionID){
    $repository = $this->doctrine->getManager()->getRepository('CPCompetitionBundle:Competition');
    $competition = $repository->find($competitionID);
        if($competition==null) {
            throw new NotFoundHttpException('Impossible de trouver la compétition dans la base de données');
        }

$fatherRound = $competition->getFatherRound();
    $tab = array();
    $tab = $this->parcourir_arbre($fatherRound, 0, $tab);
    $jstab = $this->bracketJSData($tab);
    return $jstab;
}

    public function doubleTreeJS($competitionID){
        $repository = $this->doctrine->getManager()->getRepository('CPCompetitionBundle:Competition');
        $competition = $repository->find($competitionID);
        if($competition==null) {
            throw new NotFoundHttpException('Impossible de trouver la compétition dans la base de données');
        }

        $fatherRound = $competition->getFatherRound();
        $tabWinner = array();
        $tabWinner = $this->parcourir_arbre($fatherRound->getRightRound(), 0, $tabWinner);

        $tabLoser = array();
        $tabLoser = $this->parcourir_arbre_double($fatherRound->getLeftRound(), 0, $tabLoser);

        $jstab = $this->bracketJSDataDouble($tabWinner,$tabLoser,$fatherRound);
        return $jstab;
    }


    /**
     * Fonction récursive, qui va peremttre de génerer les 2 round "fils" du round donnée en entrée.
     * Le paramètre taille permet d'avoir la condition d'arret de la fonction.
     *
     * @param $round
     * @param $taille
     * @return Round
     *
     */
    public function genererArbre($round, $taille)
    {
        $mainRound = new Round();
        $this->em->persist($mainRound);
        $mainRound->setParentRound($round);
        $mainRound->setNumRound($taille / 2);

        if ($taille <= 2) {
            $game = new Game();
            $game->setTeam1($this->players[0]);
            $game->setTeam2($this->players[1]);
            $game->setScore1(null);
            $game->setScore2(null);
            $mainRound->setGame($game);

            unset($this->players[0]);
            unset($this->players[1]);
            $this->players = array_values($this->players);

        } else {

            $mainRound->setRightRound($this->genererArbre($mainRound, $taille / 2, $this->em));
            $mainRound->setLeftRound($this->genererArbre($mainRound, $taille / 2, $this->em));


        }
        return $mainRound;
    }

    public function parcourir_arbre_double($round, $level, $tab){

        if(!isset($tab[$level])) {
            $tab[$level] = array();
        }
        $tab[$level][count( $tab[$level])] = $round;
        $rightRound = $round->getRightRound();
        if( $rightRound!=null ){
            if($rightRound->getParentLoserRound() !=$round){
                $tab = $this->parcourir_arbre_double($rightRound,$level+1,$tab);
            }

        }
        $leftRound = $round->getLeftRound();
        if($leftRound!=null) {
            if($leftRound->getParentLoserRound() !=$round) {
                $tab = $this->parcourir_arbre_double($leftRound, $level + 1, $tab);
            }

        }


        return $tab;

    }


    /**
     * Fonction recursive qui va parcourir tout un arbre en partant du fatherRound.
     * Les différents Game parcourut sont ajoutés dans un tableau avec un indice pour chaque niveau de l'arbre.
     *
     * @param $round
     * @param $level
     * @param $tab
     * @return mixed
     */
    public function parcourir_arbre($round, $level, $tab){

    if(!isset($tab[$level])) {
        $tab[$level] = array();
    }
    $tab[$level][count( $tab[$level])] = $round;

    if($round->getRightRound()!=null){
        $tab = $this->parcourir_arbre($round->getRightRound(),$level+1,$tab);
    }

    if($round->getLeftRound()!=null) {
        $tab = $this->parcourir_arbre($round->getLeftRound(), $level + 1, $tab);

    }


    return $tab;

}


    /**
     * Fonction qui va convertir le tableau généré par parcourir_arbre en tableau convertissable au format JSON et lisible par JBracket avec la fonction js_encode.
     *
     * @param $tab
     * @return array
     */
    public function bracketJSData($tab)
{
    $json = array();
    $json["teams"] = array();
    for ($i = 0; $i < count($tab[count($tab) - 1]); $i = $i + 1) {
        $matchup = array($tab[count($tab) - 1][$i]->getGame()->getTeam1(), $tab[count($tab) - 1][$i]->getGame()->getTeam2());
        $json["teams"][$i] = $matchup;
    }




    $resultLevel = 0;
    for ($level = count($tab) - 1; $level >= 0; $level--) {
        global $resultLevel;
        for ($j = 0; $j < count($tab[$level]); $j++) {
            if ($tab[$level][$j]->getGame() != null) {
                $matchupResult = array($tab[$level][$j]->getGame()->getScore1(), $tab[$level][$j]->getGame()->getScore2(),$tab[$level][$j]->getGame()->getId());
                $json["results"][$resultLevel][$j] = $matchupResult;
            }
        }
        $resultLevel++;
    }
    $json['teams'] = array_values($json['teams']);
    $json['results'] = array_values($json['results']);
    return $json;

}

    public function bracketJSDataDouble($tabWinner,$tabLoser,$finalRound)
    {
        $json = array();
        $json["teams"] = array();
        for ($i = 0; $i < count($tabWinner[count($tabWinner) - 1]); $i = $i + 1) {
            $matchup = array($tabWinner[count($tabWinner) - 1][$i]->getGame()->getTeam1(), $tabWinner[count($tabWinner) - 1][$i]->getGame()->getTeam2());
            $json["teams"][$i] = $matchup;
        }


        $resultLevel = 0;
        for ($level = count($tabWinner) - 1; $level >= 0; $level--) {
            global $resultLevel;
            for ($j = 0; $j < count($tabWinner[$level]); $j++) {
                if ($tabWinner[$level][$j]->getGame() != null) {
                    $matchupResult = array($tabWinner[$level][$j]->getGame()->getScore1(), $tabWinner[$level][$j]->getGame()->getScore2(),$tabWinner[$level][$j]->getGame()->getId());
                    $json["results"][0][$resultLevel][$j] = $matchupResult;
                }
            }
            $resultLevel++;
        }
        $json["results"][1]=array();
        $resultLevel = 0;
        for ($level = count($tabLoser) - 1; $level >= 0; $level--) {
            global $resultLevel;
            for ($j = 0; $j < count($tabLoser[$level]); $j++) {
                if ($tabLoser[$level][$j]->getGame() != null) {
                    $matchupResult = array($tabLoser[$level][$j]->getGame()->getScore1(), $tabLoser[$level][$j]->getGame()->getScore2(),$tabLoser[$level][$j]->getGame()->getId());
                    $json["results"][1][$resultLevel][$j] = $matchupResult;
                }
            }

            $resultLevel++;
        }
        $finalGame = $finalRound->getGame();
        if($finalGame ==null){
            $matchupResult = array();
        }
        else{
            $matchupResult = array($finalGame->getScore1(), $finalGame->getScore2(),$finalGame->getId());
        }


        $json["results"][2][0][0] = $matchupResult;

        $json['teams'] = array_values($json['teams']);
        $json['results'][0] = array_values($json['results'][0]);
        $json['results'][1] = array_values($json['results'][1]);
        $json['results'][2] = array_values($json['results'][2]);
        return $json;

    }

    public function game_valid_simple($emanage, $game){


        if($game->getScore1()>$game->getScore2()){
            $winner = $game->getTeam1();
        }
        else if($game->getScore1()==$game->getScore2()){
            $winner = false;
        }
        else {
            $winner = $game->getTeam2();
        }

        $round = $emanage->getRepository('CPCompetitionBundle:Round')->findOneByGame($game->getId());
        if($round == null){
            throw new NotFoundHttpException('Le match n\'est attaché à aucun round');
        }

        else if ($winner !=false) {
            $nextRound = $round->getParentRound();
            if ($nextRound != null) {

                $emanage->persist($nextRound);
                $nextGame = $nextRound->getGame();
                if ($nextGame == null) {
                    $nextGame = new Game();
                }

                $emanage->persist($nextGame);
                $nextRound->setGame($nextGame);
                if ($nextRound->getRightRound() == $round) {

                    $nextGame->setTeam1($winner);
                } else {
                    $nextGame->setTeam2($winner);
                }
            }
        }

        $emanage->flush();
}
    public function game_valid_double($emanage, $game){


        if($game->getScore1()>$game->getScore2()){
            $winner = $game->getTeam1();
            $loser = $game->getTeam2();
        }
        else if($game->getScore1()==$game->getScore2()){
            $winner = false;
        }
        else {
            $winner = $game->getTeam2();
            $loser = $game->getTeam1();
        }

        $round = $emanage->getRepository('CPCompetitionBundle:Round')->findOneByGame($game->getId());
        if($round == null){
            throw new NotFoundHttpException('Le match n\'est attaché à aucun round');
        }

        else if ($winner !=false) {
            $nextRound = $round->getParentRound();
            if ($nextRound != null) {

                $emanage->persist($nextRound);
                $nextGame = $nextRound->getGame();
                if ($nextGame == null) {
                    $nextGame = new Game();
                }

                $emanage->persist($nextGame);
                $nextRound->setGame($nextGame);
                if ($nextRound->getRightRound() == $round) {

                    $nextGame->setTeam1($winner);
                } else {
                    $nextGame->setTeam2($winner);
                }
            }

            $loserRound = $round->getParentLoserRound();
            if ($loserRound != null) {

                $emanage->persist($loserRound);
                $loserGame = $loserRound->getGame();
                if ($loserGame == null) {
                    $loserGame = new Game();
                }

                $emanage->persist($loserGame);
                $loserRound->setGame($loserGame);
                if ($loserRound->getRightRound() == $round) {

                    $loserGame->setTeam1($loser);
                } else {
                    $loserGame->setTeam2($loser);
                }
            }

        }

        $emanage->flush();
    }
}
?>