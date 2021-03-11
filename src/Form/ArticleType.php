<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'titre'
            ])
            ->add('category', EntityType::class, [
                   'label' => false,
                  'class' => Category::class,
                  'choice_label' => 'title',
                  'attr' => [
                      'placeholder' => 'Categorie'
                  ]
            ])
            ->add('content',TextareaType::class,[
                'label' => 'contenu'
            ])
            ->add('image', FileType::class,[
                'required' => false,
                'data_class' => null
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}