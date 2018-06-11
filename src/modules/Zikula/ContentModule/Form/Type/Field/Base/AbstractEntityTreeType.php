<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Form\Type\Field\Base;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ContentModule\Helper\EntityDisplayHelper;

/**
 * Entity tree type base class.
 */
abstract class AbstractEntityTreeType extends AbstractType
{
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * EntityTreeType constructor.
     *
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     */
    public function __construct(EntityDisplayHelper $entityDisplayHelper)
    {
        $this->entityDisplayHelper = $entityDisplayHelper;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'root' => 1,
                'include_leaf_nodes' => true,
                'include_root_node' => false,
                'use_joins' => true,
                'attr' => [
                    'class' => 'entity-tree'
                ],
            ])
            ->setAllowedTypes('root', 'int')
            ->setAllowedTypes('include_leaf_nodes', 'bool')
            ->setAllowedTypes('include_root_node', 'bool')
            ->setAllowedTypes('use_joins', 'bool')
        ;
        $resolver->setNormalizer('choices', function (Options $options, $choices) {
            if (empty($choices)) {
                $choices = $this->loadChoices($options);
            }

            return $choices;
        });
    }

    /**
     * Performs the actual data selection.
     *
     * @param array $options The options
     *
     * @return array List of selected objects
     */
    protected function loadChoices(array $options = [])
    {
        $repository = $options['em']->getRepository($options['class']);
        $treeNodes = $repository->selectTree($options['root'], $options['use_joins']);

        $choices = [];
        foreach ($treeNodes as $node) {
            if (!$this->isIncluded($node, $repository, $options)) {
                continue;
            }

            $choices[$this->createChoiceLabel($node, $options['include_root_node'])] = $node->getKey();
        }

        return $choices;
    }

    /**
     * Determines whether a certain list item should be included or not.
     * Allows to exclude undesired items after the selection has happened.
     *
     * @param object           $item       The treated entity
     * @param EntityRepository $repository The entity repository
     * @param array            $options    The options
     *
     * @return boolean Whether this entity should be included into the list
     */
    protected function isIncluded($item, EntityRepository $repository, array $options = [])
    {
        $nodeLevel = $item->getLvl();

        if (!$options['include_root_node'] && $nodeLevel == 0) {
            // if we do not include the root node skip it
            return false;
        }

        if (!$options['include_leaf_nodes'] && $repository->childCount($item) == 0) {
            // if we do not include leaf nodes skip them
            return false;
        }

        return true;
    }

    /**
     * Creates the label for a choice.
     *
     * @param object  $choice          The object
     * @param boolean $includeRootNode Whether the root node should be included or not
     *
     * @return string The string representation of the object
     */
    public function createChoiceLabel($choice, $includeRootNode = false)
    {
        // determine current list hierarchy level depending on root node inclusion
        $shownLevel = $choice->getLvl();
        if (!$includeRootNode) {
            $shownLevel--;
        }
        $prefix = str_repeat('- - ', $shownLevel);

        $itemLabel = $prefix . $this->entityDisplayHelper->getFormattedTitle($choice);

        return $itemLabel;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_field_entitytree';
    }
}
