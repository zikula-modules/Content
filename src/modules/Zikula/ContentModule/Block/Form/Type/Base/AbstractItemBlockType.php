<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Block\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\EntityDisplayHelper;

/**
 * Detail block form type base class.
 */
abstract class AbstractItemBlockType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * ItemBlockType constructor.
     *
     * @param TranslatorInterface $translator          Translator service instance
     * @param EntityFactory       $entityFactory       EntityFactory service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addObjectTypeField($builder, $options);
        $this->addIdField($builder, $options);
        $this->addTemplateField($builder, $options);
    }

    /**
     * Adds an object type field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('objectType', HiddenType::class, [
            'label' => $this->__('Object type', 'zikulacontentmodule') . ':',
            'empty_data' => 'page'
        ]);
    }

    /**
     * Adds a item identifier field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIdField(FormBuilderInterface $builder, array $options = [])
    {
        $repository = $this->entityFactory->getRepository($options['object_type']);
        // select without joins
        $entities = $repository->selectWhere('', '', false);
    
        $choices = [];
        foreach ($entities as $entity) {
            $choices[$this->entityDisplayHelper->getFormattedTitle($entity)] = $entity->getKey();
        }
        ksort($choices);
    
        $builder->add('id', ChoiceType::class, [
            'multiple' => false,
            'expanded' => false,
            'choices' => $choices,
            'required' => true,
            'label' => $this->__('Entry to display', 'zikulacontentmodule') . ':'
        ]);
    }

    /**
     * Adds template fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addTemplateField(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('customTemplate', TextType::class, [
                'label' => $this->__('Custom template', 'zikulacontentmodule') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 80,
                    'title' => $this->__('Example', 'zikulacontentmodule') . ': displaySpecial.html.twig'
                ],
                'help' => [
                    $this->__('Example', 'zikulacontentmodule') . ': <em>displaySpecial.html.twig</em>',
                    $this->__('Needs to be located in the "External/YourEntity/" directory.', 'zikulacontentmodule')
                ]
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_detailblock';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'page'
            ])
            ->setRequired(['object_type'])
            ->setAllowedTypes('object_type', 'string')
        ;
    }
}
