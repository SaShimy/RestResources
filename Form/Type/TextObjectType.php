<?php
namespace Components\Restresources\Form\Type;

use Components\Restresources\Form\DataTransformer\ObjectToIdTransformer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TextObjectType
 * @package Components\Restresources\Form\Type
 */
class TextObjectType extends AbstractType
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdTransformer($this->registry, $options['class'], $options['property'], $options['multiple']);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['class']);
        $resolver->setDefaults([
            'multiple'        => false,
            'data_class'      => null,
            'invalid_message' => 'The object does not exist.',
            'property'        => 'id'
        ]);

        $resolver->setAllowedTypes('invalid_message', [
            'null',
            'string'
        ]);
        $resolver->setAllowedTypes('property', [
            'null',
            'string'
        ]);
        $resolver->setAllowedTypes('multiple', ['boolean']);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'text_object';
    }
}