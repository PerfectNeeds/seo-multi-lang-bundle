<?php

namespace PNSeoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use VM5\EntityTranslationsBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use PNSeoBundle\Form\Translation\SeoSocialTranslationType;

class SeoSocialType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', TextType::class, ["attr" => ["class" => "countLength", "data-max-length" => "60"]])
                ->add('description', TextareaType::class, ["attr" => ["class" => "countLength", "data-preview" => "snippetPreviewMetaDescription", "data-max-length" => "300"]])
                ->add('imageUrl', UrlType::class)
                ->add('translations', TranslationsType::class, [
                    'entry_type' => SeoSocialTranslationType::class,
                    "label" => false,
                    'entry_language_options' => [
                        'en' => [
                            'required' => true,
                        ]
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => \PNSeoBundle\Entity\SeoSocial::class
        ));
    }

    public function getBlockPrefix() {
        return 'pn_bundle_seobundle_seo_social';
    }

}
