<?php

namespace Demo\MediaBundle\Tests;

use Demo\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MediaTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }
    
    /**
     * This function tests if Media is saved and retrieved properly
     */
    public function testRepository()
    {
        //Sample user to test
        $userId = 1500;
        
        //First delete
        $query = $this->em->createQuery('DELETE Demo\MediaBundle\Entity\Media m WHERE m.userid = :u');
        $query->setParameter('u', $userId);
        $query->execute();
        
        //Create a sample media
        $media = new Media();
        $media->setAlbum('Album');
        $media->setGenre('Pop');
        $media->setArtist('Blah');
        $media->setFile('/someplace/on/internet');
        $media->setTitle('My Song');
        $media->setUserid($userId);
        
        //Save it
        $this->em->persist($media);
        $this->em->flush();
        
        //Save again. This should NOT result in another entry, as the entity is dirty
        $this->em->persist($media);
        $this->em->flush();
        
        //Fetch results
        $query = $this->em->createQuery('SELECT m FROM Demo\MediaBundle\Entity\Media m WHERE m.userid = :u');
        $query->setParameter('u', $userId);
        $r = $query->getResult();
        
        //Total
        $this->assertCount(1, $r);

        //See if data comes in correctly
        $r1 = $r[0];
        $this->assertEquals($r1->getAlbum(), 'Album');
        $this->assertEquals($r1->getGenre(), 'Pop');
        $this->assertEquals($r1->getArtist(), 'Blah');
        $this->assertEquals($r1->getFile(), '/someplace/on/internet');
        $this->assertEquals($r1->getTitle(), 'My Song');
        $this->assertEquals($r1->getUserid(), $userId);
        
        try {
            //Try saving an empty Media
            $media = new Media();
            $this->em->persist($media);
            $this->em->flush();
            
            $this->assertTrue(false, 'Empty media inserted');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }
        
        //Delete test results
        $query = $this->em->createQuery('DELETE Demo\MediaBundle\Entity\Media m WHERE m.userid = :u');
        $query->setParameter('u', $userId);
        $query->execute();
    }
    
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}

