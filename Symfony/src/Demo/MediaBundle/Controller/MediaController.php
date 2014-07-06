<?php

namespace Demo\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MediaController extends Controller
{
    /**
     * Home page
     * 
     * @Route("/", name="home")
     * @Template("DemoMediaBundle::index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * List user's music
     * 
     * @Route("/media/list", name="list")
     * @Template("DemoMediaBundle::list.html.twig")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQuery('SELECT m FROM Demo\MediaBundle\Entity\Media m WHERE m.userid = :id ORDER BY m.id DESC');
        $query->setParameter('id', $this->getUserId());
        
        return array('tracks' => $query->getResult());
    }
    
    /**
     * Add a new music file
     * 
     * @Route("/media/add", name="add")
     * @Template("DemoMediaBundle::add.html.twig")
     */
    public function addMediaAction(Request $request)
    {
        // Validate a music file
        // Upload it to a safe place. Make sure existing files are not overridden
        
        $userId = $this->getUserId();
        $dir = $this->container->getParameter('upload_dir');

        $media = new \Demo\MediaBundle\Entity\Media();
        $form = $this->createForm(new \Demo\MediaBundle\Form\MediaForm(), $media);
        $form->handleRequest($request);
        
        if($form->isValid()) {

            //Get the file
            $file = $media->getFile();
            
            /**
             * At this point there should be more checks to see if it is a proper
             * mp3 file. Check header of the file, or run it through some decoder.
             * For brevity, I'm skipping such tests here.
             */
            
            /*
             * Move the file to web accessible dir.
             * Sanitize file name. This is a simple example
             */
            try {
                $filename = "{$userId}:" . str_replace(' ','_', $file->getClientOriginalName());
                $file->move($dir, $filename);
                $media->setFile($filename);

                //Update title if empty
                $title = $media->getTitle();
                if(empty($title)) {
                    $title = str_replace(
                            array(".{$file->getClientOriginalExtension()}", "_"), 
                            array('', ' '), 
                            $file->getClientOriginalName());

                    $media->setTitle($title);
                }

                $media->setUserid($userId);


                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($media);
                $em->flush();

                return $this->redirect($this->generateUrl('upload_success'));
            } catch (\Exception $e) {
                //Catch File upload or doctrine errors
                $er = new \Symfony\Component\Form\FormError($e->getMessage());
                $form->addError($er);
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * Success page when a music file is saved
     * 
     * @Route("/media/upload_success", name="upload_success")
     * @Template("DemoMediaBundle::success.html.twig")
     */
    public function successMediaAction()
    {
        return array();
    }
    
    /**
     * Play last 10 tracks by default
     * 
     * @Route("/media/play/{limit}", defaults={"limit" = 10}, name="play")
     * @Template("DemoMediaBundle::play.html.twig")
     */
    public function playMediaAction($limit)
    {
//        $results = $this->getDoctrine()->getManager()
//                ->getRepository('Demo\MediaBundle\Entity\Media')->getByUserId($this->getUserId(), $limit);
        $query =  $this->getDoctrine()->getManager()->createQuery('SELECT m FROM Demo\MediaBundle\Entity\Media m WHERE m.userid = :id ORDER BY m.id DESC');
        $query->setParameter('id', $this->getUserId());
        $query->setMaxResults((int)$limit);
        $results = $query->getResult();
        
        return array('tracks' => $results);
    }
    
    /**
     * Delete a music file
     * 
     * @Route("/media/delete/{id}", name="delete")
     * @Template()
     */
    public function deleteMediaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery("DELETE Demo\MediaBundle\Entity\Media m WHERE m.id = :id AND m.userid = :userid");
        $query->setParameter('id', $id);
        $query->setParameter('userid', $this->getUserId());
        $query->execute();
        
        return $this->redirect($this->generateUrl('list'));
    }
    
    /**
     * This just a placeholder UserId
     * 
     * @return int
     */
    private function getUserId()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $user->getId();
    }
}
