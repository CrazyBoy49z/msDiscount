<?php

/**
 * Get a list of Items
 */
class msdCouponsMemberGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'msdCouponsMember';
	public $classKey = 'msdCouponsMember';
	public $linkedKey1 = '';
	public $linkedKey2 = '';
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'DESC';
	protected $_modx23;


	public function initialize() {
		/** @var msDiscount $msDiscount */
		$msDiscount = $this->modx->getService('msDiscount');
		$this->_modx23 = $msDiscount->systemVersion();

		switch ($this->getProperty('type')) {
			case 'users':
				$this->linkedKey1 = 'msdUserGroup';
				$this->linkedKey2 = 'modUserGroup';
				break;
			case 'products':
				$this->linkedKey1 = 'msdProductGroup';
				$this->linkedKey2 = 'modResourceGroup';
				break;
		}

		return parent::initialize();
	}


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
			'type' => $this->getProperty('type'),
			'coupons_id' => $this->getProperty('coupons_id'),
		));

		$c->leftJoin($this->linkedKey1, $this->linkedKey1, $this->classKey . '.group_id = ' . $this->linkedKey1 . '.id');
		$c->leftJoin($this->linkedKey2, $this->linkedKey2, $this->classKey . '.group_id = ' . $this->linkedKey2 . '.id');

		$c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
		$c->select($this->modx->getSelectColumns($this->linkedKey1, $this->linkedKey1));
		$c->select($this->modx->getSelectColumns($this->linkedKey2, $this->linkedKey2));

		return $c;
	}


	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();

		$icon = $this->_modx23 ? 'icon' : 'fa';
		$array['actions'] = array();

		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-exchange",
			'title' => $this->modx->lexicon('msd_action_switch_mode'),
			'multiple' => $this->modx->lexicon('msd_action_switch_mode'),
			'action' => 'switchMember',
			'button' => true,
			'menu' => true,
		);

		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o action-red",
			'title' => $this->modx->lexicon('msd_action_remove'),
			'multiple' => $this->modx->lexicon('msd_action_remove'),
			'action' => 'removeMember',
			'button' => true,
			'menu' => true,
		);

		return $array;
	}

}

return 'msdCouponsMemberGetListProcessor';
