<?php

namespace App\Form;

use App\Entity\Videos;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('nom', TextType::class)
            ->add('title', TextType::class)
			->add('description', TextType::class)
			->add('genre', ChoiceType::class, [
				'choices' => [
					'Meme' => 'meme',
					'Music' => 'music'
				]
			])
			->add('videoFile', VichFileType::class, [
				'required' => false,
				'allow_delete' => true,
            ])
            ->add('save', SubmitType::class)
        ;
    }
	
	public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Videos::class,
        ]);
    }
}