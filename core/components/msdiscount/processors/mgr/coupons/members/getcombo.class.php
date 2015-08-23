<?php
/**
 * Get a list of Items
 */
class msdSaleGroupGetListProcessor extends modObjectGetListProcessor {
	public $objectType = '';
	public $classKey = '';
	public $linkedKey = '';
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'DESC';
	public $renderers = '';


	public function initialize() {
		switch ($this->getProperty('type')) {
			case 'users':
				$this->objectType = $this->classKey = 'modUserGroup';
				$this->linkedKey = 'msdUserGroup';
				break;
			case 'products':
				$this->objectType = $this->classKey = 'modResourceGroup';
				$this->linkedKey = 'msdProductGroup';
				break;
		}

		if (empty($this->classKey)) {
			return 'Wrong type of group';
		}

		return parent::initialize();
	}

	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin($this->linkedKey, $this->linkedKey, $this->classKey.'.id = '.$this->linkedKey.'.id');
		$c->leftJoin('msdCouponsMember', 'msdCouponsMember',
			array(
				$this->classKey.'.id = msdCouponsMember.group_id',
				'msdCouponsMember.type' => $this->getProperty('type'),
				'msdCouponsMember.coupons_id' => $this->getProperty('coupons_id'),
			)
		);
    	$c->select($this->modx->getSelectColumns($this->linkedKey, $this->linkedKey));
    	$c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
    	$c->where(array('msdCouponsMember.coupons_id' => null));
    	return $c;
	}


	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();

		return $array;
	}

}

return 'msdSaleGroupGetListProcessor';
