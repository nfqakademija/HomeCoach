<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.1
 * Time: 18.40
 */

namespace AppBundle\Controller;

use AppBundle\Form\WeightType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class UserController extends Controller
{

    /**
     * Shows most popular coaches in right sidebar of all pages
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCoachesAction()
    {
        $repo = $this->get('app.repo')
            ->getRepo('UserBundle:User');

        //reiks pakeisti ta findAll ir implementuoti searcho funkcijas
        $coaches = $repo->findAll();
        $json = json_encode($coaches);

        return $this->render('base.html.twig', array(
            'coaches' => $json
        ));
    }

    /**
     * Used when searching for users
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function browseUsersAction()
    {
        $repo = $this->get('app.repo')
            ->getRepo('UserBundle:User');

        //reiks pakeisti ta findAll ir implementuoti searcho funkcijas
        $users = $repo->findAll();
        $json = json_encode($users);

        return $this->render('@App/Home/browseUsers.html.twig', array(
            'users' => $json
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProfileAction($id, Request $request)
    {
        $repo = $this->get('app.repo');
        $user = $repo
            ->getRepo('UserBundle:User')
            ->find($id);
        
//        if ($id === $this->getUser()->getId()) {

            $weightForm = $this->createForm(WeightType::class);
            $weightForm->handleRequest($request);
            $weight = $weightForm->get('weight')->getData();

            if (isset($weight)) {
                if ($weight != 0) {
                    $user->addWeight(date('Ymdis'), $weight);  // date formate 'is' po to nutrinti. kol kas uzdetas testavimo sumetimais, kad galeciau ta pacia diena pridejes matyti skirtingus rezultatus
                    $em = $repo->getEntityManager();
                    $em->persist($user);
                    $em->flush();
                }
            };

            if ($weightForm->isSubmitted() && $weightForm->isValid()) {
                $em = $repo->getEntityManager();

            };
            $workout_history = $user->getWorkoutHistory();
            $workouts_arr = [];
            foreach ($workout_history as $work_hist) {
                $workouts_arr[$work_hist->getDate()->format('Ymdis')] = $work_hist->getWorkout()->getTitle(); // date formate 'is' po to nutrinti. kol kas uzdetas testavimo sumetimais, kad galeciau ta pacia diena pridejes matyti skirtingus rezultatus
            }
        
            $weights_arr = $user->getWeight();    
        
            $data = json_encode($weights_arr);
            return $this->render('@App/Home/showUser.html.twig', array(
                'user' => $user,
                'data' => $data,
                'weightForm' => $weightForm->createView()
            ));
//        };

        return $this->render('@App/Home/showUser.html.twig', array(
            'user' => $user,
        ));
        
    }
}
