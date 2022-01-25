<?php

namespace PN\SeoBundle\Form\Translation;

use PN\SeoBundle\Entity\Translation\SeoSocialTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoSocialTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, ["attr" => ["class" => "countLength", "data-max-length" => "60"]])
            ->add('description', TextareaType::class, [
                "attr" => [
                    "class" => "countLength",
                    "data-preview" => "snippetPreviewMetaDescription",
                    "data-max-length" => "300",
                ],
            ])
            ->add('imageUrl', UrlType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SeoSocialTranslation::class,
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'pn_bundle_seobundle_seo';
    }

}
