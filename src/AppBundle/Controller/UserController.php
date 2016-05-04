<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.1
 * Time: 18.40
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function showProfileAction($id)
    {
        $repo = $this->get('app.repo')
            ->getRepo('UserBundle:User');
        $user = $repo->find($id);
        $workout_history = $user->getWorkoutHistory();
        $data = json_encode(array('lala'=>2, 'baba'=>1));
        return $this->render('@App/Home/showUser.html.twig', array(
            'user'=>$user,
            'data'=>$data
        ));
    }
}
