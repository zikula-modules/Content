<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Helper\Base;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * Helper base class for list field entries related methods.
 */
abstract class AbstractListEntriesHelper
{
    use TranslatorTrait;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }
    
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
    
    /**
     * Return the name or names for a given list item.
     */
    public function resolve(
        string $value,
        string $objectType = '',
        string $fieldName = '',
        string $delimiter = ', '
    ): string {
        if ((empty($value) && '0' !== $value) || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        $isMulti = $this->hasMultipleSelection($objectType, $fieldName);
        $values = $isMulti ? $this->extractMultiList($value) : [];
    
        $options = $this->getEntries($objectType, $fieldName);
        $result = '';
    
        if (true === $isMulti) {
            foreach ($options as $option) {
                if (!in_array($option['value'], $values, true)) {
                    continue;
                }
                if (!empty($result)) {
                    $result .= $delimiter;
                }
                $result .= $option['text'];
            }
        } else {
            foreach ($options as $option) {
                if ($option['value'] !== $value) {
                    continue;
                }
                $result = $option['text'];
                break;
            }
        }
    
        return $result;
    }
    
    
    /**
     * Extract concatenated multi selection.
     */
    public function extractMultiList(string $value): array
    {
        $listValues = explode('###', $value);
        $amountOfValues = count($listValues);
        if ($amountOfValues > 1 && '' === $listValues[$amountOfValues - 1]) {
            unset($listValues[$amountOfValues - 1]);
        }
        if ('' === $listValues[0]) {
            // use array_shift instead of unset for proper key reindexing
            // keys must start with 0, otherwise the dropdownlist form plugin gets confused
            array_shift($listValues);
        }
    
        return $listValues;
    }
    
    
    /**
     * Determine whether a certain dropdown field has a multi selection or not.
     */
    public function hasMultipleSelection(string $objectType, string $fieldName): bool
    {
        if (empty($objectType) || empty($fieldName)) {
            return false;
        }
    
        $result = false;
        switch ($objectType) {
            case 'page':
                switch ($fieldName) {
                    case 'workflowState':
                        $result = false;
                        break;
                    case 'scope':
                        $result = true;
                        break;
                }
                break;
            case 'contentItem':
                switch ($fieldName) {
                    case 'workflowState':
                        $result = false;
                        break;
                    case 'scope':
                        $result = true;
                        break;
                }
                break;
            case 'appSettings':
                switch ($fieldName) {
                    case 'stateOfNewPages':
                        $result = false;
                        break;
                    case 'permalinkSuffix':
                        $result = false;
                        break;
                    case 'enabledFinderTypes':
                        $result = true;
                        break;
                    case 'revisionHandlingForPage':
                        $result = false;
                        break;
                    case 'maximumAmountOfPageRevisions':
                        $result = false;
                        break;
                }
                break;
        }
    
        return $result;
    }
    
    
    /**
     * Get entries for a certain dropdown field.
     */
    public function getEntries(string $objectType, string $fieldName): array
    {
        if (empty($objectType) || empty($fieldName)) {
            return [];
        }
    
        $entries = [];
        switch ($objectType) {
            case 'page':
                switch ($fieldName) {
                    case 'workflowState':
                        $entries = $this->getWorkflowStateEntriesForPage();
                        break;
                    case 'scope':
                        $entries = $this->getScopeEntriesForPage();
                        break;
                }
                break;
            case 'contentItem':
                switch ($fieldName) {
                    case 'workflowState':
                        $entries = $this->getWorkflowStateEntriesForContentItem();
                        break;
                    case 'scope':
                        $entries = $this->getScopeEntriesForContentItem();
                        break;
                }
                break;
            case 'appSettings':
                switch ($fieldName) {
                    case 'stateOfNewPages':
                        $entries = $this->getStateOfNewPagesEntriesForAppSettings();
                        break;
                    case 'permalinkSuffix':
                        $entries = $this->getPermalinkSuffixEntriesForAppSettings();
                        break;
                    case 'enabledFinderTypes':
                        $entries = $this->getEnabledFinderTypesEntriesForAppSettings();
                        break;
                    case 'revisionHandlingForPage':
                        $entries = $this->getRevisionHandlingForPageEntriesForAppSettings();
                        break;
                    case 'maximumAmountOfPageRevisions':
                        $entries = $this->getMaximumAmountOfPageRevisionsEntriesForAppSettings();
                        break;
                }
                break;
        }
    
        return $entries;
    }
    
    
    /**
     * Get 'workflow state' list entries.
     */
    public function getWorkflowStateEntriesForPage(): array
    {
        $states = [];
        $states[] = [
            'value'   => 'deferred',
            'text'    => $this->trans('Deferred'),
            'title'   => $this->trans('Content has not been submitted yet or has been waiting, but was rejected.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'approved',
            'text'    => $this->trans('Approved'),
            'title'   => $this->trans('Content has been approved and is available online.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'trashed',
            'text'    => $this->trans('Trashed'),
            'title'   => $this->trans('Content has been marked as deleted, but is still persisted in the database.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!deferred',
            'text'    => $this->trans('All except deferred'),
            'title'   => $this->trans('Shows all items except these which are deferred'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!approved',
            'text'    => $this->trans('All except approved'),
            'title'   => $this->trans('Shows all items except these which are approved'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!trashed',
            'text'    => $this->trans('All except trashed'),
            'title'   => $this->trans('Shows all items except these which are trashed'),
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'scope' list entries.
     */
    public function getScopeEntriesForPage(): array
    {
        $states = [];
        $states[] = [
            'value'   => '0',
            'text'    => $this->trans('Public (all)'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => '-1',
            'text'    => $this->trans('All logged in members'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '-2',
            'text'    => $this->trans('All not logged in people'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'workflow state' list entries.
     */
    public function getWorkflowStateEntriesForContentItem(): array
    {
        $states = [];
        $states[] = [
            'value'   => 'approved',
            'text'    => $this->trans('Approved'),
            'title'   => $this->trans('Content has been approved and is available online.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'trashed',
            'text'    => $this->trans('Trashed'),
            'title'   => $this->trans('Content has been marked as deleted, but is still persisted in the database.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!approved',
            'text'    => $this->trans('All except approved'),
            'title'   => $this->trans('Shows all items except these which are approved'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!trashed',
            'text'    => $this->trans('All except trashed'),
            'title'   => $this->trans('Shows all items except these which are trashed'),
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'scope' list entries.
     */
    public function getScopeEntriesForContentItem(): array
    {
        $states = [];
        $states[] = [
            'value'   => '0',
            'text'    => $this->trans('Public (all)'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => '-1',
            'text'    => $this->trans('All logged in members'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '-2',
            'text'    => $this->trans('All not logged in people'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'state of new pages' list entries.
     */
    public function getStateOfNewPagesEntriesForAppSettings(): array
    {
        $states = [];
        $states[] = [
            'value'   => '1',
            'text'    => $this->trans('New pages will be active and available in the menu'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => '2',
            'text'    => $this->trans('New pages will be inactive and available in the menu'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '3',
            'text'    => $this->trans('New pages will be active and not available in the menu'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '4',
            'text'    => $this->trans('New pages will be inactive and not available in the menu'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'permalink suffix' list entries.
     */
    public function getPermalinkSuffixEntriesForAppSettings(): array
    {
        $states = [];
        $states[] = [
            'value'   => 'none',
            'text'    => $this->trans('No suffix'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => 'html',
            'text'    => $this->trans('Html'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'htm',
            'text'    => $this->trans('Htm'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'phtml',
            'text'    => $this->trans('Phtml'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'shtml',
            'text'    => $this->trans('Shtml'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'enabled finder types' list entries.
     */
    public function getEnabledFinderTypesEntriesForAppSettings(): array
    {
        $states = [];
        $states[] = [
            'value'   => 'page',
            'text'    => $this->trans('Page'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
    
        return $states;
    }
    
    /**
     * Get 'revision handling for page' list entries.
     */
    public function getRevisionHandlingForPageEntriesForAppSettings(): array
    {
        $states = [];
        $states[] = [
            'value'   => 'unlimited',
            'text'    => $this->trans('Unlimited revisions'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => 'limitedByAmount',
            'text'    => $this->trans('Limited revisions by amount of revisions'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'limitedByDate',
            'text'    => $this->trans('Limited revisions by date interval'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'maximum amount of page revisions' list entries.
     */
    public function getMaximumAmountOfPageRevisionsEntriesForAppSettings(): array
    {
        $states = [];
        $states[] = [
            'value'   => '1',
            'text'    => $this->trans('1'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '5',
            'text'    => $this->trans('5'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '10',
            'text'    => $this->trans('10'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '25',
            'text'    => $this->trans('25'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => '50',
            'text'    => $this->trans('50'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '100',
            'text'    => $this->trans('100'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '250',
            'text'    => $this->trans('250'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '500',
            'text'    => $this->trans('500'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
}
