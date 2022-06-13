<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // si l'utilisateur saisi '00' dans le formulaire, la saisie sera transformée en '95' juste avant la soumission du formulaire

        $builder
            ->add('dpt')
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();  // récupère les données liées à cet event : ici, les données saisies
                $form = $event->getForm();

                if(isset($data['dpt']) && $data['dpt'] === '00')
                {
                    $data['dpt'] = '95';
                    $event->setData($data);
                }
                elseif (isset($data['dpt']) && $data['dpt'] === '99')
                {
                    $form->add('region', TextType::class);
                    $event->setData($data);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
