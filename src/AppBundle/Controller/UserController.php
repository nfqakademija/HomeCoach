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
        $repo = $this->getDoctrine()->getRepository('UserBundle:User');

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
        $repo = $this->getDoctrine()->getRepository('UserBundle:User');

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
        $repo = $this->getDoctrine()->getRepository('UserBundle:User');
        $user = $repo
            ->find($id);
        
        //sutvarkyti kad rodytu tik tavo svori, o kitu ne
//        if ($id === $this->getUser()->getId()) {

            $weightForm = $this->createForm(WeightType::class);
            $weightForm->handleRequest($request);
            $weight = $weightForm->get('svoris')->getData();

            if (isset($weight)) {
                if ($weight != 0) {
                    $user->addWeight(date('Y m d i s'), $weight);  // date formate 'is' po to nutrinti. kol kas uzdetas testavimo sumetimais, kad galeciau ta pacia diena pridejes matyti skirtingus rezultatus
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($user);
                    $em->flush();
                }
            };

            $workout_history = $user->getWorkoutHistory();
            $workouts_arr = [];
            foreach ($workout_history as $work_hist) {
                $workouts_arr[$work_hist->getDate()->format('Y m d i s')] = $work_hist->getWorkout()->getTitle(); // date formate 'is' po to nutrinti. kol kas uzdetas testavimo sumetimais, kad galeciau ta pacia diena pridejes matyti skirtingus rezultatus
            }
        
            $weights_arr = $user->getWeight();    

            $merged_array = $workouts_arr + $weights_arr;
            ksort($merged_array);
        
            $data = json_encode($merged_array);
        
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
