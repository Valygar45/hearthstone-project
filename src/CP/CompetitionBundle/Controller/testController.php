<?php

namespace CP\CompetitionBundle\Controller;

use CP\CompetitionBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CP\CompetitionBundle\Entity\Round;


class testController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

$fatherRound = $this->genererArbre(null,8,$em);
        $em->flush();
        $tab = array();
        $tab = $this->parcourir_arbre($fatherRound,0,$tab);
        /*$jstab = array(
                            "team"=>array(
                                        array("Team1","Team2"),array("Team3","Team4")
                                    )
        ,                    "score"=>array(
                                                array("score1","score2"),array("score3","score4")));*/
        $tab[1][0]= new Game();
        $tab[1][0]->setTeam1("om");
        $tab[1][0]->setTeam1("psg");
        $tab[1][0]->setScore(2);
      $jstab = $this->bracketJSData($tab);

        return $this->render('CPCompetitionBundle:index.html.twig',array("tab"=>$tab,"father"=>$fatherRound,"tabtest"=>$jstab));
    }

    public function genererArbre($round,$taille,$em)
    {
        $mainRound = new Round();
        $em->persist($mainRound);
        $mainRound->setParentRound($round);
        $mainRound->setNumRound($taille/2);

        if($taille<=2){
           $game = new Game();
            $game->setTeam1("test");
            $game->setTeam2("test2");
            $game->setScore(1);
            $mainRound->setGame($game);

        }
        else{

            $mainRound->setRightRound($this->genererArbre($mainRound, $taille/2,$em));
            $mainRound->setLeftRound($this->genererArbre($mainRound, $taille/2,$em));


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
   public function bracketJSData($tab){
       $json = array();
       $json["teams"] = array();
       for ($i=0; $i<count($tab[count($tab)-1]);$i=$i+1){
           $matchup = array($tab[count($tab)-1][$i]->getTeam1(),$tab[count($tab)-1][$i]->getTeam2());
           $json["teams"][$i]=$matchup;
       }


       $resultLevel = 0;
for($level=count($tab)-1;$level>=0;$level--){
    global $resultLevel;
    for($j=0;$j<count($tab[$level]);$j++){
        if($tab[$level][$j]!=null) {
            $matchupResult = array($tab[$level][$j]->getScore()+2, $tab[$level][$j]->getScore());
            $json["results"][$resultLevel][$j] = $matchupResult;
        }
    }
    $resultLevel++;
}
       $json['teams']= array_values($json['teams']);
       $json['results']= array_values($json['results']);
       return $json;

   }

}
