<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\CommandeMateriel;
use App\Entity\Materiel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;


class CommandeMaterielType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'nomClient',
            ])
            ->add('materiel', EntityType::class, [
                'class' => Materiel::class,
                'choice_label' => 'nom',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $form->getData();
            $materiel = $data->getMateriel();

            if ($materiel && $data->getQuantite() > $materiel->getQuantite()) {
                $form->get('quantite')->addError(new FormError(
                    sprintf('La quantité demandée (%d) dépasse la quantité disponible (%d).', 
                    $data->getQuantite(), $materiel->getQuantite())
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandeMateriel::class,
        ]);
    }
}
