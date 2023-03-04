<?php
// src/EventListener/RendezVousListener.php
namespace App\EventListener;

use App\Entity\Notify;
use App\Entity\RendezVous;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class RendezVousListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // On ne traite que les entités de type RendezVous
        if (!$entity instanceof RendezVous) {
            return;
        }

        $em = $args->getObjectManager();

        // Création d'une nouvelle entité Notify avec le message "ajout success"
        $Notify = new Notify();
        $Notify->setMessage('ajout success');
        $Notify->setRendezvous($entity);

        // Enregistrement de la nouvelle entité Notify
        $em->persist($Notify);
        $em->flush();
    }
}