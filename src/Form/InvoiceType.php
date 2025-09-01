<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('amount', MoneyType::class, [
                'currency' => 'XOF',
                'label' => 'Montant',
            ])
            ->add('dueDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Non payé' => 'unpaid',
                    'Payé' => 'paid',
                    'En retard' => 'late',
                ],
                'label' => 'Statut',
            ])
            ->add('assignedTo', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Assigné à',
                'required' => false,
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => 'Notes internes',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
