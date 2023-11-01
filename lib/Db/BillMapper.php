<?php

/**
 * Nextcloud - cospend
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <julien-nc@posteo.net
 * @copyright Julien Veyssier 2019
 */

 namespace OCA\Cospend\Db;

use DateTime;
use Exception;
use OCA\Cospend\AppInfo\Application;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class BillMapper extends QBMapper {
	const TABLENAME = 'cospend_bills';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLENAME, Bill::class);
	}

	/**
	 * @param int $id
	 * @return Bill
	 * @throws \OCP\DB\Exception
	 */
	public function find(int $id): Bill {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
		$result = $qb->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		if ($row === false) {
			throw new Exception('Bill ' . $id . ' not found');
		}

		return $this->mapRowToEntity($row);
	}

	public function findProjectId(int $id): string {
		$qb = $this->db->getQueryBuilder();
		$qb->select('projectid')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
		$result = $qb->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		if ($row === false) {
			throw new Exception('Bill ' . $id . ' not found');
		}

		return $row['projectid'];
	}

	/**
	 * Delete bill owers of given bill
	 *
	 * @param int $billId
	 * @return int
	 * @throws \OCP\DB\Exception
	 */
	public function deleteBillOwersOfBill(int $billId): int {
		$qb = $this->db->getQueryBuilder();
		$qb->delete('cospend_bill_owers')
			->where(
				$qb->expr()->eq('billid', $qb->createNamedParameter($billId, IQueryBuilder::PARAM_INT))
			);
		$nbDeleted = $qb->executeStatement();
		$qb->resetQueryParts();
		return $nbDeleted;
	}

	public function deleteBill(string $projectId, int $billId): array {
		$nbBillOwersDeleted = $this->deleteBillOwersOfBill($billId);

		$qb = $this->db->getQueryBuilder();
		$qb->delete('cospend_bills')
			->where(
				$qb->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->gt('id', $qb->createNamedParameter($billId, IQueryBuilder::PARAM_INT))
			);
		$qb->executeStatement();
		return [
			'billOwers' => $nbBillOwersDeleted,
		];
	}

	/**
	 * @param string $projectId
	 * @param string|null $what
	 * @param int|null $minTimestamp
	 * @return array
	 * @throws \OCP\DB\Exception
	 */
	public function deleteBills(string $projectId, ?string $what = null, ?int $minTimestamp = null): array {
		// first delete the bill owers
		$qb = $this->db->getQueryBuilder();

		$qb2 = $this->db->getQueryBuilder();
		$qb2->select('id')
			->from('cospend_bills')
			->where(
				$qb2->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		if ($what !== null) {
			$qb2->andWhere(
				$qb2->expr()->eq('what', $qb->createNamedParameter($what, IQueryBuilder::PARAM_STR))
			);
		}
		if ($minTimestamp !== null) {
			$qb2->andWhere(
				$qb2->expr()->gt('timestamp', $qb->createNamedParameter($minTimestamp, IQueryBuilder::PARAM_INT))
			);
		}

		$qb->delete('cospend_bill_owers')
			->where(
				$qb2->expr()->in('billid', $qb->createFunction($qb2->getSQL()), IQueryBuilder::PARAM_STR_ARRAY)
			);
		$nbBillOwersDeleted = $qb->executeStatement();
		$qb->resetQueryParts();

		///////////////////
		// delete the bills
		$qb->delete('cospend_bills')
			->where(
				$qb->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		if ($what !== null) {
			$qb->andWhere(
				$qb->expr()->eq('what', $qb->createNamedParameter($what, IQueryBuilder::PARAM_STR))
			);
		}
		if ($minTimestamp !== null) {
			$qb->andWhere(
				$qb->expr()->gt('timestamp', $qb->createNamedParameter($minTimestamp, IQueryBuilder::PARAM_INT))
			);
		}
		$nbBillsDeleted = $qb->executeStatement();
		return [
			'bills' => $nbBillsDeleted,
			'billOwers' => $nbBillOwersDeleted,
		];
	}

	/**
	 * @param string $projectId
	 * @param string|null $what
	 * @param int|null $minTimestamp
	 * @return array
	 * @throws \OCP\DB\Exception
	 */
	public function moveBillsToTrash(string $projectId, ?string $what = null, ?int $minTimestamp = null): array {
		$qb = $this->db->getQueryBuilder();
		$qb->update('cospend_bills')
			->set('deleted', $qb->createNamedParameter(1, IQueryBuilder::PARAM_INT))
			->where(
				$qb->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		if ($what !== null) {
			$qb->andWhere(
				$qb->expr()->eq('what', $qb->createNamedParameter($what, IQueryBuilder::PARAM_STR))
			);
		}
		if ($minTimestamp !== null) {
			$qb->andWhere(
				$qb->expr()->gt('timestamp', $qb->createNamedParameter($minTimestamp, IQueryBuilder::PARAM_INT))
			);
		}
		$nbBillsDeleted = $qb->executeStatement();
		return [
			'bills' => $nbBillsDeleted,
		];
	}

	public function moveBillToTrash(string $projectId, int $billId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->update('cospend_bills')
			->set('deleted', $qb->createNamedParameter(1, IQueryBuilder::PARAM_INT))
			->where(
				$qb->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('id', $qb->createNamedParameter($billId, IQueryBuilder::PARAM_INT))
			);
		$nbBillsDeleted = $qb->executeStatement();
		return [
			'bills' => $nbBillsDeleted,
		];
	}

	/**
	 * @param string $projectId
	 * @param string|null $what
	 * @param int|null $minTimestamp
	 * @return Bill[]
	 * @throws \OCP\DB\Exception
	 */
	public function getBillsToDelete(string $projectId, ?string $what = null, ?int $minTimestamp = null): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('cospend_bills')
			->where(
				$qb->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		if ($what !== null) {
			$qb->andWhere(
				$qb->expr()->eq('what', $qb->createNamedParameter($what, IQueryBuilder::PARAM_STR))
			);
		}
		if ($minTimestamp !== null) {
			$qb->andWhere(
				$qb->expr()->gt('timestamp', $qb->createNamedParameter($minTimestamp, IQueryBuilder::PARAM_INT))
			);
		}
		return $this->findEntities($qb);
	}

	/**
	 * Get bill info
	 *
	 * @param string $projectId
	 * @param int $billId
	 * @return array|null
	 */
	public function getBill(string $projectId, int $billId): ?array {
		$bill = null;
		// get bill owers
		$billOwers = [];
		$billOwerIds = [];

		$qb = $this->db->getQueryBuilder();

		$qb->select('memberid', 'm.name', 'm.weight', 'm.activated')
			->from('cospend_bill_owers', 'bo')
			->innerJoin('bo', 'cospend_members', 'm', $qb->expr()->eq('bo.memberid', 'm.id'))
			->where(
				$qb->expr()->eq('bo.billid', $qb->createNamedParameter($billId, IQueryBuilder::PARAM_INT))
			);
		$req = $qb->executeQuery();

		while ($row = $req->fetch()){
			$dbWeight = (float) $row['weight'];
			$dbName = $row['name'];
			$dbActivated = (((int) $row['activated']) === 1);
			$dbOwerId= (int) $row['memberid'];
			$billOwers[] = [
				'id' => $dbOwerId,
				'weight' => $dbWeight,
				'name' => $dbName,
				'activated' => $dbActivated,
			];
			$billOwerIds[] = $dbOwerId;
		}
		$req->closeCursor();
		$qb = $qb->resetQueryParts();

		// get the bill
		$qb->select('id', 'what', 'comment', 'timestamp', 'amount', 'payerid', 'repeat',
			'repeatallactive', 'paymentmode', 'paymentmodeid', 'categoryid', 'repeatuntil', 'repeatfreq', 'deleted')
			->from('cospend_bills')
			->where(
				$qb->expr()->eq('projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('id', $qb->createNamedParameter($billId, IQueryBuilder::PARAM_INT))
			);
		$req = $qb->executeQuery();
		while ($row = $req->fetch()){
			$bill = [
				'id' => (int) $row['id'],
				'amount' => (float) $row['amount'],
				'what' => $row['what'],
				'comment' => $row['comment'],
				'date' => DateTime::createFromFormat('U', $row['timestamp'])->format('Y-m-d'),
				'timestamp' => (int) $row['timestamp'],
				'payer_id' => (int) $row['payerid'],
				'owers' => $billOwers,
				'owerIds' => $billOwerIds,
				'repeat' => $row['repeat'],
				'repeatallactive' => (int) $row['repeatallactive'],
				'repeatuntil' => $row['repeatuntil'],
				'repeatfreq' => (int) $row['repeatfreq'],
				'paymentmode' => $row['paymentmode'],
				'paymentmodeid' => (int) $row['paymentmodeid'],
				'categoryid' => (int) $row['categoryid'],
				'deleted' => (int) $row['deleted'],
			];
		}
		$req->closeCursor();
		$qb->resetQueryParts();

		return $bill;
	}

	/**
	 * Get filtered list of bills for a project
	 *
	 * @param string $projectId
	 * @param int|null $tsMin
	 * @param int|null $tsMax
	 * @param string|null $paymentMode
	 * @param int|null $paymentModeId
	 * @param int|null $category
	 * @param float|null $amountMin
	 * @param float|null $amountMax
	 * @param int|null $lastchanged
	 * @param int|null $limit
	 * @param bool $reverse
	 * @param int|null $payerId
	 * @param int|null $deleted
	 * @return array
	 * @throws \OCP\DB\Exception
	 */
	public function getBills(string $projectId, ?int $tsMin = null, ?int $tsMax = null,
							 ?string $paymentMode = null, ?int $paymentModeId = null,
							 ?int $category = null, ?float $amountMin = null, ?float $amountMax = null,
							 ?int $lastchanged = null, ?int $limit = null,
							 bool $reverse = false, ?int $payerId = null, ?int $deleted = 0): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('bi.id', 'what', 'comment', 'timestamp', 'amount', 'payerid', 'repeat',
			'paymentmode', 'paymentmodeid', 'categoryid', 'bi.lastchanged', 'repeatallactive', 'repeatuntil', 'repeatfreq',
			'deleted', 'memberid', 'm.name', 'm.weight', 'm.activated')
			->from('cospend_bill_owers', 'bo')
			->innerJoin('bo', 'cospend_bills', 'bi', $qb->expr()->eq('bo.billid', 'bi.id'))
			->innerJoin('bo', 'cospend_members', 'm', $qb->expr()->eq('bo.memberid', 'm.id'))
			->where(
				$qb->expr()->eq('bi.projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		// take bills that have changed after $lastchanged
		if ($lastchanged !== null) {
			$qb->andWhere(
				$qb->expr()->gt('bi.lastchanged', $qb->createNamedParameter($lastchanged, IQueryBuilder::PARAM_INT))
			);
		}
		if ($payerId !== null) {
			$qb->andWhere(
				$qb->expr()->eq('bi.payerid', $qb->createNamedParameter($payerId, IQueryBuilder::PARAM_INT))
			);
		}
		if ($tsMin !== null) {
			$qb->andWhere(
				$qb->expr()->gte('timestamp', $qb->createNamedParameter($tsMin, IQueryBuilder::PARAM_INT))
			);
		}
		if ($tsMax !== null) {
			$qb->andWhere(
				$qb->expr()->lte('timestamp', $qb->createNamedParameter($tsMax, IQueryBuilder::PARAM_INT))
			);
		}
		if ($deleted !== null) {
			$qb->andWhere(
				$qb->expr()->eq('deleted', $qb->createNamedParameter($deleted, IQueryBuilder::PARAM_INT))
			);
		}
		if ($paymentMode !== null && $paymentMode !== '' && $paymentMode !== 'n') {
			$qb->andWhere(
				$qb->expr()->eq('paymentmode', $qb->createNamedParameter($paymentMode, IQueryBuilder::PARAM_STR))
			);
		} elseif (!is_null($paymentModeId)) {
			$qb->andWhere(
				$qb->expr()->eq('paymentmodeid', $qb->createNamedParameter($paymentModeId, IQueryBuilder::PARAM_INT))
			);
		}
		if ($category !== null) {
			if ($category === -100) {
				$or = $qb->expr()->orx();
				$or->add($qb->expr()->isNull('categoryid'));
				$or->add($qb->expr()->neq('categoryid', $qb->createNamedParameter(Application::CAT_REIMBURSEMENT, IQueryBuilder::PARAM_INT)));
				$qb->andWhere($or);
			} else {
				$qb->andWhere(
					$qb->expr()->eq('categoryid', $qb->createNamedParameter($category, IQueryBuilder::PARAM_INT))
				);
			}
		}
		if ($amountMin !== null) {
			$qb->andWhere(
				$qb->expr()->gte('amount', $qb->createNamedParameter($amountMin, IQueryBuilder::PARAM_STR))
			);
		}
		if ($amountMax !== null) {
			$qb->andWhere(
				$qb->expr()->lte('amount', $qb->createNamedParameter($amountMax, IQueryBuilder::PARAM_STR))
			);
		}
		if ($reverse) {
			$qb->orderBy('timestamp', 'DESC');
		} else {
			$qb->orderBy('timestamp', 'ASC');
		}
		if ($limit) {
			$qb->setMaxResults($limit);
		}
		$req = $qb->executeQuery();

		// bills by id
		$billDict = [];
		// ordered list of bill ids
		$orderedBillIds = [];
		while ($row = $req->fetch()){
			$dbBillId = (int) $row['id'];
			// if first time we see the bill : add it to bill list
			if (!isset($billDict[$dbBillId])) {
				$dbAmount = (float) $row['amount'];
				$dbWhat = $row['what'];
				$dbComment = $row['comment'];
				$dbTimestamp = (int) $row['timestamp'];
				$dbDate = DateTime::createFromFormat('U', $dbTimestamp);
				$dbRepeat = $row['repeat'];
				$dbPayerId = (int) $row['payerid'];
				$dbPaymentMode = $row['paymentmode'];
				$dbPaymentModeId = (int) $row['paymentmodeid'];
				$dbCategoryId = (int) $row['categoryid'];
				$dbLastchanged = (int) $row['lastchanged'];
				$dbRepeatAllActive = (int) $row['repeatallactive'];
				$dbRepeatUntil = $row['repeatuntil'];
				$dbRepeatFreq = (int) $row['repeatfreq'];
				$dbDeleted = (int) $row['deleted'];
				$billDict[$dbBillId] = [
					'id' => $dbBillId,
					'amount' => $dbAmount,
					'what' => $dbWhat,
					'comment' => $dbComment,
					'timestamp' => $dbTimestamp,
					'date' => $dbDate->format('Y-m-d'),
					'payer_id' => $dbPayerId,
					'owers' => [],
					'owerIds' => [],
					'repeat' => $dbRepeat,
					'paymentmode' => $dbPaymentMode,
					'paymentmodeid' => $dbPaymentModeId,
					'categoryid' => $dbCategoryId,
					'lastchanged' => $dbLastchanged,
					'repeatallactive' => $dbRepeatAllActive,
					'repeatuntil' => $dbRepeatUntil,
					'repeatfreq' => $dbRepeatFreq,
					'deleted' => $dbDeleted,
				];
				// keep order of bills
				$orderedBillIds[] = $dbBillId;
			}
			// anyway add an ower
			$dbWeight = (float) $row['weight'];
			$dbName = $row['name'];
			$dbActivated = ((int) $row['activated']) === 1;
			$dbOwerId= (int) $row['memberid'];
			$billDict[$dbBillId]['owers'][] = [
				'id' => $dbOwerId,
				'weight' => $dbWeight,
				'name' => $dbName,
				'activated' => $dbActivated,
			];
			$billDict[$dbBillId]['owerIds'][] = $dbOwerId;
		}
		$req->closeCursor();
		$qb->resetQueryParts();

		$resultBills = [];
		foreach ($orderedBillIds as $bid) {
			$resultBills[] = $billDict[$bid];
		}

		return $resultBills;
	}

	/**
	 * Get filtered list of bills for a project
	 *
	 * @param string $projectId
	 * @param int|null $tsMin
	 * @param int|null $tsMax
	 * @param string|null $paymentMode
	 * @param int|null $paymentModeId
	 * @param int|null $category
	 * @param float|null $amountMin
	 * @param float|null $amountMax
	 * @param int|null $lastchanged
	 * @param int|null $limit
	 * @param bool $reverse
	 * @param int|null $offset
	 * @param int|null $payerId
	 * @param int|null $includeBillId
	 * @param string|null $searchTerm
	 * @return array
	 * @throws \OCP\DB\Exception
	 */
	public function getBillsWithLimit(
		string $projectId, ?int $tsMin = null, ?int $tsMax = null,
		?string $paymentMode = null, ?int $paymentModeId = null,
		?int $category = null, ?float $amountMin = null, ?float $amountMax = null,
		?int $lastchanged = null, ?int $limit = null,
		bool $reverse = false, ?int $offset = 0, ?int $payerId = null,
		?int $includeBillId = null, ?string $searchTerm = null, ?int $deleted = 0
	): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('id', 'what', 'comment', 'timestamp', 'amount', 'payerid', 'repeat',
			'paymentmode', 'paymentmodeid', 'categoryid', 'lastchanged', 'repeatallactive',
			'repeatuntil', 'repeatfreq', 'deleted')
			->from('cospend_bills', 'bi')
			->where(
				$qb->expr()->eq('bi.projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		// take bills that have changed after $lastchanged
		if ($lastchanged !== null) {
			$qb->andWhere(
				$qb->expr()->gt('bi.lastchanged', $qb->createNamedParameter($lastchanged, IQueryBuilder::PARAM_INT))
			);
		}
		if ($payerId !== null) {
			$qb->andWhere(
				$qb->expr()->eq('payerid', $qb->createNamedParameter($payerId, IQueryBuilder::PARAM_INT))
			);
		}
		if ($tsMin !== null) {
			$qb->andWhere(
				$qb->expr()->gte('timestamp', $qb->createNamedParameter($tsMin, IQueryBuilder::PARAM_INT))
			);
		}
		if ($tsMax !== null) {
			$qb->andWhere(
				$qb->expr()->lte('timestamp', $qb->createNamedParameter($tsMax, IQueryBuilder::PARAM_INT))
			);
		}
		if ($deleted !== null) {
			$qb->andWhere(
				$qb->expr()->eq('deleted', $qb->createNamedParameter($deleted, IQueryBuilder::PARAM_INT))
			);
		}
		if ($paymentMode !== null && $paymentMode !== '' && $paymentMode !== 'n') {
			$qb->andWhere(
				$qb->expr()->eq('paymentmode', $qb->createNamedParameter($paymentMode, IQueryBuilder::PARAM_STR))
			);
		} elseif (!is_null($paymentModeId)) {
			$qb->andWhere(
				$qb->expr()->eq('paymentmodeid', $qb->createNamedParameter($paymentModeId, IQueryBuilder::PARAM_INT))
			);
		}
		if ($category !== null) {
			if ($category === -100) {
				$or = $qb->expr()->orx();
				$or->add($qb->expr()->isNull('categoryid'));
				$or->add($qb->expr()->neq('categoryid', $qb->createNamedParameter(Application::CAT_REIMBURSEMENT, IQueryBuilder::PARAM_INT)));
				$qb->andWhere($or);
			} else {
				$qb->andWhere(
					$qb->expr()->eq('categoryid', $qb->createNamedParameter($category, IQueryBuilder::PARAM_INT))
				);
			}
		}
		if ($amountMin !== null) {
			$qb->andWhere(
				$qb->expr()->gte('amount', $qb->createNamedParameter($amountMin, IQueryBuilder::PARAM_STR))
			);
		}
		if ($amountMax !== null) {
			$qb->andWhere(
				$qb->expr()->lte('amount', $qb->createNamedParameter($amountMax, IQueryBuilder::PARAM_STR))
			);
		}
		// handle the search term (what, comment, amount+-1)
		if ($searchTerm !== null && $searchTerm !== '') {
			$qb = $this->applyBillSearchTermCondition($qb, $searchTerm, 'bi');
		}
		if ($reverse) {
			$qb->orderBy('timestamp', 'DESC');
		} else {
			$qb->orderBy('timestamp', 'ASC');
		}
		if ($limit) {
			$qb->setMaxResults($limit);
		}
		if ($offset) {
			$qb->setFirstResult($offset);
		}
		$req = $qb->executeQuery();

		$bills = [];
		$includeBillFound = false;
		while ($row = $req->fetch()){
			if ($includeBillId !== null && $includeBillId === (int) $row['id']) {
				$includeBillFound = true;
			}
			$bills[] = $this->getBillFromRow($row);
		}
		$req->closeCursor();

		// look further if we want to include a specific bill
		if ($includeBillId !== null && $includeBillFound === false && $limit && $offset === 0) {
			$lastResultCount = count($bills);
			while ($lastResultCount > 0 && $includeBillFound === false) {
				$offset = $offset + $limit;
				$qb->setFirstResult($offset);
				$req = $qb->executeQuery();
				$lastResultCount = 0;
				while ($row = $req->fetch()){
					if ($includeBillId === (int) $row['id']) {
						$includeBillFound = true;
					}
					$lastResultCount++;
					$bills[] = $this->getBillFromRow($row);
				}
				$req->closeCursor();
			}
		}

		$qb = $qb->resetQueryParts();

		// get owers
		foreach ($bills as $i => $bill) {
			$billId = $bill['id'];
			$billOwers = [];
			$billOwerIds = [];

			$qb->select('memberid', 'm.name', 'm.weight', 'm.activated')
				->from('cospend_bill_owers', 'bo')
				->innerJoin('bo', 'cospend_members', 'm', $qb->expr()->eq('bo.memberid', 'm.id'))
				->where(
					$qb->expr()->eq('bo.billid', $qb->createNamedParameter($billId, IQueryBuilder::PARAM_INT))
				);
			$qb->setFirstResult(0);
			$req = $qb->executeQuery();
			while ($row = $req->fetch()){
				$dbWeight = (float) $row['weight'];
				$dbName = $row['name'];
				$dbActivated = ((int) $row['activated']) === 1;
				$dbOwerId= (int) $row['memberid'];
				$billOwers[] = [
					'id' => $dbOwerId,
					'weight' => $dbWeight,
					'name' => $dbName,
					'activated' => $dbActivated,
				];
				$billOwerIds[] = $dbOwerId;
			}
			$req->closeCursor();
			$qb = $qb->resetQueryParts();
			$bills[$i]['owers'] = $billOwers;
			$bills[$i]['owerIds'] = $billOwerIds;
		}

		return $bills;
	}

	private function getBillFromRow(array $row): array {
		$dbBillId = (int) $row['id'];
		$dbAmount = (float) $row['amount'];
		$dbWhat = $row['what'];
		$dbComment = $row['comment'];
		$dbTimestamp = (int) $row['timestamp'];
		$dbDate = DateTime::createFromFormat('U', $dbTimestamp);
		$dbRepeat = $row['repeat'];
		$dbPayerId = (int) $row['payerid'];
		$dbPaymentMode = $row['paymentmode'];
		$dbPaymentModeId = (int) $row['paymentmodeid'];
		$dbCategoryId = (int) $row['categoryid'];
		$dbLastchanged = (int) $row['lastchanged'];
		$dbRepeatAllActive = (int) $row['repeatallactive'];
		$dbRepeatUntil = $row['repeatuntil'];
		$dbRepeatFreq = (int) $row['repeatfreq'];
		$dbDeleted = (int) $row['deleted'];
		return [
			'id' => $dbBillId,
			'amount' => $dbAmount,
			'what' => $dbWhat,
			'comment' => $dbComment ?? '',
			'timestamp' => $dbTimestamp,
			'date' => $dbDate->format('Y-m-d'),
			'payer_id' => $dbPayerId,
			'owers' => [],
			'owerIds' => [],
			'repeat' => $dbRepeat,
			'paymentmode' => $dbPaymentMode,
			'paymentmodeid' => $dbPaymentModeId,
			'categoryid' => $dbCategoryId,
			'lastchanged' => $dbLastchanged,
			'repeatallactive' => $dbRepeatAllActive,
			'repeatuntil' => $dbRepeatUntil,
			'repeatfreq' => $dbRepeatFreq,
			'deleted' => $dbDeleted,
		];
	}

	private function applyBillSearchTermCondition(IQueryBuilder $qb, string $term, string $billTableAlias): IQueryBuilder {
		$term = strtolower($term);
		$or = $qb->expr()->orx();
		$or->add(
			$qb->expr()->iLike($billTableAlias . '.what', $qb->createNamedParameter('%' . $this->db->escapeLikeParameter($term) . '%', IQueryBuilder::PARAM_STR))
		);
		$or->add(
			$qb->expr()->iLike($billTableAlias . '.comment', $qb->createNamedParameter('%' . $this->db->escapeLikeParameter($term) . '%', IQueryBuilder::PARAM_STR))
		);
		// search amount
		$noCommaTerm = str_replace(',', '.', $term);
		if (is_numeric($noCommaTerm)) {
			$amount = (float) $noCommaTerm;
			$amountMin = $amount - 1.0;
			$amountMax = $amount + 1.0;
			$andExpr = $qb->expr()->andX();
			$andExpr->add(
				$qb->expr()->gte($billTableAlias . '.amount', $qb->createNamedParameter($amountMin, IQueryBuilder::PARAM_STR))
			);
			$andExpr->add(
				$qb->expr()->lte($billTableAlias . '.amount', $qb->createNamedParameter($amountMax, IQueryBuilder::PARAM_STR))
			);
			$or->add($andExpr);
		}
		$qb->andWhere($or);
		return $qb;
	}

	/**
	 * Search bills with query string
	 *
	 * @param string $projectId
	 * @param string $term
	 * @return array
	 */
	public function searchBills(string $projectId, string $term, ?int $deleted = 0): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select(
			'b.id', 'what', 'comment', 'amount', 'timestamp',
			'paymentmode', 'paymentmodeid', 'categoryid',
			'pr.currencyname', 'me.name', 'me.userid'
		)
			->from('cospend_bills', 'b')
			->innerJoin('b', 'cospend_projects', 'pr', $qb->expr()->eq('b.projectid', 'pr.id'))
			->innerJoin('b', 'cospend_members', 'me', $qb->expr()->eq('b.payerid', 'me.id'))
			->where(
				$qb->expr()->eq('b.projectid', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
			);
		if ($deleted !== null) {
			$qb->andWhere(
				$qb->expr()->eq('b.deleted', $qb->createNamedParameter($deleted, IQueryBuilder::PARAM_INT))
			);
		}
		$qb = $this->applyBillSearchTermCondition($qb, $term, 'b');
		$qb->orderBy('timestamp', 'ASC');
		$req = $qb->executeQuery();

		// bills by id
		$bills = [];
		while ($row = $req->fetch()){
			$dbBillId = (int) $row['id'];
			$dbAmount = (float) $row['amount'];
			$dbWhat = $row['what'];
			$dbTimestamp = (int) $row['timestamp'];
			$dbComment = $row['comment'];
			$dbPaymentMode = $row['paymentmode'];
			$dbPaymentModeId = (int) $row['paymentmodeid'];
			$dbCategoryId = (int) $row['categoryid'];
			$dbProjectCurrencyName = $row['currencyname'];
			$dbPayerName = $row['name'];
			$dbPayerUserId = $row['userid'];
			$bills[] = [
				'id' => $dbBillId,
				'projectId' => $projectId,
				'amount' => $dbAmount,
				'what' => $dbWhat,
				'timestamp' => $dbTimestamp,
				'comment' => $dbComment,
				'paymentmode' => $dbPaymentMode,
				'paymentmodeid' => $dbPaymentModeId,
				'categoryid' => $dbCategoryId,
				'currencyname' => $dbProjectCurrencyName,
				'payer_name' => $dbPayerName,
				'payer_user_id' => $dbPayerUserId,
			];
		}
		$req->closeCursor();
		$qb->resetQueryParts();

		return $bills;
	}
}
