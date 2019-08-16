<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Projects;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', CKEditorType::class, ['label' => 'Description'])
            ->add('link', TextType::class, ['label' => 'Lien'])
            ->add('image', ImageType::class)
            ->add('categories', EntityType::class, [
                'class'        => Category::class,
                'choice_label' => 'name',
                'multiple'     => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Projects::class,
            'csrf_protection' => true
        ]);
    }
}
