<?php

namespace App\Form;

use App\Entity\FileUpload;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class FileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('file', FileType::class, [
                'label' => 'File Upload ( required )',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => true,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                                 'maxSize' => '10M',  // max 10 MB
                                 'mimeTypes' => [
                                     'image/*',
                                     'application/pdf',
                                     'application/x-pdf',
                                     'application/msword',
                                     'application/vnd.ms-excel',
                                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                     'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                     'text/plain'
                                 ],
                                 'mimeTypesMessage' => 'Please upload a valid PDF document',
                             ]),
                    new NotBlank()
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileUpload::class,
        ]);
    }
}
