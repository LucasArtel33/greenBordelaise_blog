<?php

namespace App\Form;

use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
                ->add('subtitle')
                ->add('mainImage', FileType::class)
                ->add('content', CKEditorType::class, array(
                    'config' => array(
                        'filebrowserBrowseRoute' => 'elfinder',
                        'filebrowserBrowseRouteParameters' => array('instance' => 'default')
                    ),
                ))
                ->add('category', EntityType::class,
                    [
                        'class' => Category::class,
                        'choice_label' => 'name'
                    ]
                )
                ->add('submit', SubmitType::class);


    }
}