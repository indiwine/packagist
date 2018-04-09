<?php
/**
 * Created by PhpStorm.
 * User: indiwine
 * Date: 4/9/2018
 * Time: 1:53 PM
 */

namespace Packagist\WebBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Packagist\WebBundle\Entity\Package;
use Packagist\WebBundle\Service\Encryption;

class PackageEncryptionSubscriber implements EventSubscriber
{
    /**
     * @var Encryption
     */
    private $encryption;

    public function __construct(Encryption $encryption)
    {
        $this->encryption = $encryption;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->encrypt($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->decrypt($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->encrypt($args);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->decrypt($args);
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'postPersist',
            'preUpdate',
            'postLoad',
        ];
    }


    private function encrypt(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Package) {
            $plainPassword = $entity->getVcsPassword();
            if (!empty($plainPassword)) {
                $password = $this->encryption->encrypt($plainPassword);
                $entity->setVcsPassword($password);

            }
        }
    }

    private function decrypt(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Package) {
            $encryptedPass = $entity->getVcsPassword();
            if (!empty($encryptedPass)) {
                $password = $this->encryption->decrypt($encryptedPass);
                $entity->setVcsPassword($password);

            }
        }
    }
}