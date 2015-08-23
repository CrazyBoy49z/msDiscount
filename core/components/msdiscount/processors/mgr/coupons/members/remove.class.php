<?php

class msdCouponsMemberRemoveProcessor extends modObjectRemoveProcessor {
	public $objectType = 'msdCouponsMember';
	public $classKey = 'msdCouponsMember';
	public $languageTopics = array('msdiscount');
	public $permission = 'msdiscount_save';

	public function initialize() {
		$this->object = $this->modx->getObject($this->classKey, array(
			'coupons_id' => $this->getProperty('coupons_id'),
			'group_id' => $this->getProperty('group_id'),
			'type' => $this->getProperty('type'),
		));

		if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs');

		if ($this->permission && $this->object instanceof modAccessibleObject && !$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return true;
	}

}

return 'msdCouponsMemberRemoveProcessor';
