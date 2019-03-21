<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Controller\Base;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ContentModule\Helper\WorkflowHelper;

/**
 * Ajax controller base class.
 */
abstract class AbstractAjaxController extends AbstractController
{
    
    /**
     * Retrieve item list for finder selections, for example used in Scribite editor plug-ins.
     *
     * @param Request $request
     * @param ControllerHelper $controllerHelper
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     * @param EntityDisplayHelper $entityDisplayHelper
     *
     * @return JsonResponse
     */
    public function getItemListFinderAction(
        Request $request,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper
    )
     {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }
        
        if (!$this->hasPermission('ZikulaContentModule::Ajax', '::', ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        
        $objectType = $request->query->getAlnum('ot', 'page');
        $contextArgs = ['controller' => 'ajax', 'action' => 'getItemListFinder'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $contextArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $contextArgs);
        }
        
        $repository = $entityFactory->getRepository($objectType);
        $descriptionFieldName = $entityDisplayHelper->getDescriptionFieldName($objectType);
        
        $sort = $request->query->getAlnum('sort', '');
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }
        
        $sdir = strtolower($request->query->getAlpha('sortdir', ''));
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }
        
        $where = ''; // filters are processed inside the repository class
        $searchTerm = $request->query->get('q', '');
        $sortParam = $sort . ' ' . $sdir;
        
        $entities = [];
        if ($searchTerm != '') {
            list ($entities, $totalAmount) = $repository->selectSearch($searchTerm, [], $sortParam, 1, 50);
        } else {
            $entities = $repository->selectWhere($where, $sortParam);
        }
        
        $slimItems = [];
        foreach ($entities as $item) {
            if (!$permissionHelper->mayRead($item)) {
                continue;
            }
            $itemId = $item->getKey();
            $slimItems[] = $this->prepareSlimItem($controllerHelper, $repository, $entityDisplayHelper, $item, $itemId, $descriptionFieldName);
        }
        
        // return response
        return $this->json($slimItems);
    }
    
    /**
     * Builds and returns a slim data array from a given entity.
     *
     * @param ControllerHelper $controllerHelper
     * @param EntityRepository $repository Repository for the treated object type
     * @param EntityDisplayHelper $entityDisplayHelper
     * @param object $item The currently treated entity
     * @param string $itemId Data item identifier(s)
     * @param string $descriptionField Name of item description field
     *
     * @return array The slim data representation
     */
    protected function prepareSlimItem(ControllerHelper $controllerHelper, $repository, EntityDisplayHelper $entityDisplayHelper, $item, $itemId, $descriptionField)
    {
        $objectType = $item->get_objectType();
        $previewParameters = [
            $objectType => $item
        ];
        $contextArgs = ['controller' => $objectType, 'action' => 'display'];
        $previewParameters = $controllerHelper->addTemplateParameters($objectType, $previewParameters, 'controllerAction', $contextArgs);
    
        $previewInfo = base64_encode($this->get('twig')->render('@ZikulaContentModule/External/' . ucfirst($objectType) . '/info.html.twig', $previewParameters));
    
        $title = $entityDisplayHelper->getFormattedTitle($item);
        $description = $descriptionField != '' ? $item[$descriptionField] : '';
    
        return [
            'id'          => $itemId,
            'title'       => str_replace('&amp;', '&', $title),
            'description' => $description,
            'previewInfo' => $previewInfo
        ];
    }
    
    /**
     * Checks whether a field value is a duplicate or not.
     *
     * @param Request $request
     * @param ControllerHelper $controllerHelper
     * @param EntityFactory $entityFactory
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function checkForDuplicateAction(
        Request $request,
        ControllerHelper $controllerHelper,
        EntityFactory $entityFactory
    )
     {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }
        
        if (!$this->hasPermission('ZikulaContentModule::Ajax', '::', ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        
        $objectType = $request->query->getAlnum('ot', 'page');
        $contextArgs = ['controller' => 'ajax', 'action' => 'checkForDuplicate'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $contextArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $contextArgs);
        }
        
        $fieldName = $request->query->getAlnum('fn', '');
        $value = $request->query->get('v', '');
        
        if (empty($fieldName) || empty($value)) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        
        // check if the given field is existing and unique
        $uniqueFields = [];
        switch ($objectType) {
            case 'page':
                $uniqueFields = ['slug'];
                break;
        }
        if (!count($uniqueFields) || !in_array($fieldName, $uniqueFields)) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $exclude = $request->query->getInt('ex', '');
        
        $result = false;
        switch ($objectType) {
            case 'page':
                $repository = $entityFactory->getRepository($objectType);
                switch ($fieldName) {
                    case 'slug':
                        $entity = $repository->selectBySlug($value, false, false, $exclude);
                        $result = null !== $entity && isset($entity['slug']);
                        break;
                }
                break;
        }
        
        // return response
        return $this->json(['isDuplicate' => $result]);
    }
    
    /**
     * Changes a given flag (boolean field) by switching between true and false.
     *
     * @param Request $request
     * @param EntityFactory $entityFactory
     * @param CurrentUserApiInterface $currentUserApi
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function toggleFlagAction(
        Request $request,
        EntityFactory $entityFactory,
        CurrentUserApiInterface $currentUserApi
    )
     {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }
        
        if (!$this->hasPermission('ZikulaContentModule::Ajax', '::', ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        
        $objectType = $request->request->getAlnum('ot', 'page');
        $field = $request->request->getAlnum('field', '');
        $id = $request->request->getInt('id', 0);
        
        if ($id == 0
            || ($objectType != 'page' && $objectType != 'contentItem')
        || ($objectType == 'page' && !in_array($field, ['active', 'inMenu']))
        || ($objectType == 'contentItem' && !in_array($field, ['active']))
        ) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        
        // select data from data source
        $repository = $entityFactory->getRepository($objectType);
        $entity = $repository->selectById($id, false);
        if (null === $entity) {
            return $this->json($this->__('No such item.'), JsonResponse::HTTP_NOT_FOUND);
        }
        
        // toggle the flag
        $entity[$field] = !$entity[$field];
        
        // save entity back to database
        $entityFactory->getEntityManager()->flush($entity);
        
        $logger = $this->get('logger');
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'field' => $field, 'entity' => $objectType, 'id' => $id];
        $logger->notice('{app}: User {user} toggled the {field} flag the {entity} with id {id}.', $logArgs);
        
        // return response
        return $this->json([
            'id' => $id,
            'state' => $entity[$field],
            'message' => $this->__('The setting has been successfully changed.')
        ]);
    }
    
    /**
     * Performs different operations on tree hierarchies.
     *
     * @param Request $request
     * @param EntityFactory $entityFactory
     * @param EntityDisplayHelper $entityDisplayHelper
     * @param CurrentUserApiInterface $currentUserApi
     * @param UserRepositoryInterface $userRepository
     * @param WorkflowHelper $workflowHelper
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function handleTreeOperationAction(
        Request $request,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository,
        WorkflowHelper $workflowHelper
    )
     {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }
        
        if (!$this->hasPermission('ZikulaContentModule::Ajax', '::', ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->request->getAlnum('ot', 'page');
        // ensure that we use only object types with tree extension enabled
        if (!in_array($objectType, ['page'])) {
            $objectType = 'page';
        }
        
        $returnValue = [
            'data'    => [],
            'result'  => 'success',
            'message' => ''
        ];
        
        $op = $request->request->getAlpha('op', '');
        if (!in_array($op, ['addRootNode', 'addChildNode', 'deleteNode', 'moveNode', 'moveNodeTo'])) {
            $returnValue['result'] = 'failure';
            $returnValue['message'] = $this->__('Error: invalid operation.');
        
            return $this->json($returnValue);
        }
        
        // Get id of treated node
        $id = 0;
        if (!in_array($op, ['addRootNode', 'addChildNode'])) {
            $id = $request->request->getInt('id', 0);
            if (!$id) {
                $returnValue['result'] = 'failure';
                $returnValue['message'] = $this->__('Error: invalid node.');
        
                return $this->json($returnValue);
            }
        }
        
        $createMethod = 'create' . ucfirst($objectType);
        $repository = $entityFactory->getRepository($objectType);
        
        $rootId = 1;
        if (!in_array($op, ['addRootNode'])) {
            $rootId = $request->request->getInt('root', 0);
            if (!$rootId) {
                $returnValue['result'] = 'failure';
                $returnValue['message'] = $this->__('Error: invalid root node.');
        
                return $this->json($returnValue);
            }
        }
        
        $entityManager = $entityFactory->getEntityManager();
        $titleFieldName = $entityDisplayHelper->getTitleFieldName($objectType);
        $descriptionFieldName = $entityDisplayHelper->getDescriptionFieldName($objectType);
        
        $logger = $this->get('logger');
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $objectType];
        
        $currentUserId = $currentUserApi->isLoggedIn() ? $currentUserApi->get('uid') : 1;
        $currentUser = $userRepository->find($currentUserId);
        
        switch ($op) {
            case 'addRootNode':
                $entity = $entityFactory->$createMethod();
                if (!empty($titleFieldName)) {
                    $entity[$titleFieldName] = $this->__('New root node');
                }
                if (!empty($descriptionFieldName)) {
                    $entity[$descriptionFieldName] = $this->__('This is a new root node');
                }
                if (method_exists($entity, 'setCreatedBy')) {
                    $entity->setCreatedBy($currentUser);
                    $entity->setUpdatedBy($currentUser);
                }
                
                // save new object to set the root id
                $action = 'submit';
                try {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($entity, $action);
                    if (!$success) {
                        $returnValue['result'] = 'failure';
                    }
                } catch (\Exception $exception) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . '  ' . $exception->getMessage();
                
                    return $this->json($returnValue);
                }
        
                $logger->notice('{app}: User {user} added a new root node in the {entity} tree.', $logArgs);
                break;
            case 'addChildNode':
                $parentId = $request->request->getInt('pid', 0);
                if (!$parentId) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('Error: invalid parent node.');
                
                    return $this->json($returnValue);
                }
                
                $childEntity = $entityFactory->$createMethod();
                $childEntity[$titleFieldName] = $this->__('New child node');
                if (!empty($descriptionFieldName)) {
                    $childEntity[$descriptionFieldName] = $this->__('This is a new child node');
                }
                if (method_exists($childEntity, 'setCreatedBy')) {
                    $childEntity->setCreatedBy($currentUser);
                    $childEntity->setUpdatedBy($currentUser);
                }
                $parentEntity = $repository->selectById($parentId, false);
                if (null === $parentEntity) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('No such item.');
                
                    return $this->json($returnValue);
                }
                $childEntity->setParent($parentEntity);
                
                // save new object
                $action = 'submit';
                try {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($childEntity, $action);
                    if (!$success) {
                        $returnValue['result'] = 'failure';
                    } else {
                        if (in_array($objectType, ['page'])) {
                            $needsArg = in_array($objectType, ['page']);
                            $urlArgs = $needsArg ? $childEntity->createUrlArgs(true) : $childEntity->createUrlArgs();
                            $returnValue['returnUrl'] = $this->get('router')->generate('zikulacontentmodule_' . strtolower($objectType) . '_edit', $urlArgs, UrlGeneratorInterface::ABSOLUTE_URL);
                        }
                    }
                } catch (\Exception $exception) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . '  ' . $exception->getMessage();
                
                    return $this->json($returnValue);
                }
        
                $logger->notice('{app}: User {user} added a new child node in the {entity} tree.', $logArgs);
                break;
            case 'deleteNode':
                // remove node from tree and reparent all children
                $entity = $repository->selectById($id, false);
                if (null === $entity) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('No such item.');
                
                    return $this->json($returnValue);
                }
                
                // delete the object
                $action = 'delete';
                try {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($entity, $action);
                    if (!$success) {
                        $returnValue['result'] = 'failure';
                    }
                } catch (\Exception $exception) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . '  ' . $exception->getMessage();
                
                    return $this->json($returnValue);
                }
                
                $repository->removeFromTree($entity);
                $entityManager->clear(); // clear cached nodes
        
                $logger->notice('{app}: User {user} deleted a node from the {entity} tree.', $logArgs);
                break;
            case 'moveNode':
                $moveDirection = $request->request->getAlpha('direction', '');
                if (!in_array($moveDirection, ['top', 'up', 'down', 'bottom'])) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('Error: invalid direction.');
                
                    return $this->json($returnValue);
                }
                
                $entity = $repository->selectById($id, false);
                if (null === $entity) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('No such item.');
                
                    return $this->json($returnValue);
                }
                
                if ($moveDirection == 'top') {
                    $repository->moveUp($entity, true);
                } elseif ($moveDirection == 'up') {
                    $repository->moveUp($entity, 1);
                } elseif ($moveDirection == 'down') {
                    $repository->moveDown($entity, 1);
                } elseif ($moveDirection == 'bottom') {
                    $repository->moveDown($entity, true);
                }
                $entityManager->flush();
        
                $logger->notice('{app}: User {user} moved a node in the {entity} tree.', $logArgs);
                break;
            case 'moveNodeTo':
                $moveDirection = $request->request->getAlpha('direction', '');
                if (!in_array($moveDirection, ['after', 'before', 'bottom'])) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('Error: invalid direction.');
                
                    return $this->json($returnValue);
                }
                
                $destId = $request->request->getInt('destid', 0);
                if (!$destId) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('Error: invalid destination node.');
                
                    return $this->json($returnValue);
                }
                
                $entity = $repository->selectById($id, false);
                $destEntity = $repository->selectById($destId, false);
                if (null === $entity || null === $destEntity) {
                    $returnValue['result'] = 'failure';
                    $returnValue['message'] = $this->__('No such item.');
                
                    return $this->json($returnValue);
                }
                
                if ($moveDirection == 'after') {
                    $repository->persistAsNextSiblingOf($entity, $destEntity);
                } elseif ($moveDirection == 'before') {
                    $repository->persistAsPrevSiblingOf($entity, $destEntity);
                } elseif ($moveDirection == 'bottom') {
                    $repository->persistAsLastChildOf($entity, $destEntity);
                }
                
                $entityManager->flush();
        
                $logger->notice('{app}: User {user} moved a node in the {entity} tree.', $logArgs);
                break;
        }
        
        $returnValue['message'] = $this->__('The operation was successful.');
        
        // Renew tree
        /** postponed, for now we do a page reload
        $returnValue['data'] = $repository->selectTree($rootId);
        */
        
        return $this->json($returnValue);
    }
}
