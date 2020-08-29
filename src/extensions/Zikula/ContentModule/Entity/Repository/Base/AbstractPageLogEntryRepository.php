<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Entity\Repository\Base;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Gedmo\Loggable\LoggableListener;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the base repository class for page log entry entities.
 */
abstract class AbstractPageLogEntryRepository extends LogEntryRepository
{
    /**
     * Selects all log entries for removals to determine deleted pages.
     */
    public function selectDeleted(int $limit = null): array
    {
        $objectClass = str_replace('LogEntry', '', $this->_entityName);
    
        // avoid selecting logs for those entries which already had been undeleted
        $qbExisting = $this->getEntityManager()->createQueryBuilder();
        $qbExisting->select('tbl.id')
            ->from($objectClass, 'tbl');
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('log')
           ->from($this->_entityName, 'log')
           ->andWhere('log.objectClass = :objectClass')
           ->setParameter('objectClass', $objectClass)
           ->andWhere('log.action = :action')
           ->setParameter('action', LoggableListener::ACTION_REMOVE)
           ->andWhere($qb->expr()->notIn('log.objectId', $qbExisting->getDQL()))
           ->orderBy('log.loggedAt', 'DESC')
        ;
    
        $query = $qb->getQuery();
    
        if (null !== $limit) {
            $query->setMaxResults($limit);
        }
    
        return $query->getResult();
    }
    
    /**
     * Removes (or rather conflates) all obsolete log entries.
     *
     * @param string $revisionHandling The currently configured revision handling mode
     * @param string $limitParameter Optional parameter for limitation (maximum revision amount or date interval)
     */
    public function purgeHistory(string $revisionHandling = 'unlimited', string $limitParameter = ''): void
    {
        if (
            'unlimited' === $revisionHandling
            || !in_array($revisionHandling, ['limitedByAmount', 'limitedByDate'], true)
        ) {
            // nothing to do
            return;
        }
    
        $objectClass = str_replace('LogEntry', '', $this->_entityName);
    
        // step 1 - determine obsolete revisions
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('log')
           ->from($this->_entityName, 'log')
           ->andWhere('log.objectClass = :objectClass')
           ->setParameter('objectClass', $objectClass)
           ->addOrderBy('log.objectId', 'ASC')
           ->addOrderBy('log.version', 'ASC')
        ;
    
        $logAmountMap = [];
        if ('limitedByAmount' === $revisionHandling) {
            $limitParameter = (int) $limitParameter;
            if (!$limitParameter) {
                $limitParameter = 25;
            }
            $limitParameter++; // one more for the initial creation entry
    
            $qbMatchingObjects = $this->getEntityManager()->createQueryBuilder();
            $qbMatchingObjects->select('log.objectId, COUNT(log.objectId) amountOfRevisions')
                ->from($this->_entityName, 'log')
                ->andWhere('log.objectClass = :objectClass')
                ->setParameter('objectClass', $objectClass)
                ->groupBy('log.objectId')
                ->andHaving('amountOfRevisions > :maxAmount')
                ->setParameter('maxAmount', $limitParameter)
            ;
            $result = $qbMatchingObjects->getQuery()->getScalarResult();
            $identifiers = array_column($result, 'objectId');
            foreach ($result as $row) {
                $logAmountMap[$row['objectId']] = $row['amountOfRevisions'];
            }
    
            $qb->andWhere('log.objectId IN (:identifiers)')
               ->setParameter('identifiers', $identifiers)
            ;
        } elseif ('limitedByDate' === $revisionHandling) {
            if (!$limitParameter) {
                $limitParameter = 'P1Y0M0DT0H0M0S';
            }
            $thresholdDate = new DateTime(date('Ymd'));
            $thresholdDate->sub(new DateInterval($limitParameter));
    
            $qb->andWhere('log.loggedAt <= :thresholdDate')
               ->setParameter('thresholdDate', $thresholdDate)
            ;
        }
    
        // we do not need to filter specific actions, but may remove/conflate log entries with all actions
        // this does not affect detection of deleted pages
        // because in those cases the remove log entry is always the newest one (otherwise an undeletion has been done)
    
        $query = $qb->getQuery();
        $result = $query->getResult();
        if (!count($result)) {
            return;
        }
    
        $entityManager = $this->getEntityManager();
        $keepPerObject = 'limitedByAmount' === $revisionHandling ? $limitParameter : -1;
        $thresholdForObject = 0;
        $counterPerObject = 0;
    
        // loop through the log entries
        $dataForObject = [];
        $lastObjectId = 0;
        $lastLogEntry = null;
        foreach ($result as $logEntry) {
            // step 2 - conflate data arrays
            $objectId = $logEntry->getObjectId();
            if ($lastObjectId !== $objectId) {
                if ($lastObjectId > 0) {
                    if (count($dataForObject)) {
                        // write conflated data into last obsolete version (which will be kept)
                        $lastLogEntry->setData($dataForObject);
                    }
                    // this becomes a creation entry now
                    $lastLogEntry->setAction(LoggableListener::ACTION_CREATE);
                    // we keep the old loggedAt value though
                } else {
                    // very first loop execution, nothing special to do here
                }
                $counterPerObject = 1;
                $thresholdForObject = 0 < $keepPerObject && isset($logAmountMap[$objectId])
                    ? ($logAmountMap[$objectId] - $keepPerObject)
                    : 1
                ;
                $dataForObject = $logEntry->getData();
            } else {
                // we have a another log entry for the same object
                if (0 > $keepPerObject || $counterPerObject < $thresholdForObject) {
                    if (null !== $logEntry->getData()) {
                        $dataForObject = array_merge($dataForObject, $logEntry->getData());
                    }
                    // thus we may remove the last one
                    $entityManager->remove($lastLogEntry);
                }
            }
    
            $lastObjectId = $objectId;
            if (0 > $keepPerObject || $counterPerObject < $thresholdForObject) {
                $lastLogEntry = $logEntry;
            }
            $counterPerObject++;
        }
    
        // do not forget to save values for the last objectId
        if (null !== $lastLogEntry) {
            if (count($dataForObject)) {
                $lastLogEntry->setData($dataForObject);
            }
            $lastLogEntry->setAction(LoggableListener::ACTION_CREATE);
        }
    
        // step 3 - push changes into database
        $entityManager->flush();
    }
}
