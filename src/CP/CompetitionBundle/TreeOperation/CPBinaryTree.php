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
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

    }

    /**
     * Fonction qui prend en entrée le nombre de joueurs du tournoi et qui génère l'arbre correspondant.
     * La fonction retourne le Round "father" qui est le niveau 0 de l'arbre et qui va permettre de parcourir toutes les branches de l'arbre
     *
     * @param $taille
     * @return Round
     */
    public function simpleTreeGenerator($taille)
{
    if( ($taille & ($taille - 1)) != 0){
        throw new NotFoundHttpException('Le nombre de joueurs : "'.$taille.'" n \'est pas valable pour génerer un arbre binaire.');
    }
    $this->em = $this->doctrine->getManager();

    $fatherRound = $this->genererArbre(null, $taille);
    $this->em->flush();

    return $fatherRound;
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
    $tab[$level][count( $tab[$level])] = $round->getGame();

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