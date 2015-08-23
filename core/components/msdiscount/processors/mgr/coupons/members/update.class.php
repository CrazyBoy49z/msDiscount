<?php

class msdCouponsMemberUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'msdCouponsMember';
	public $classKey = 'msdCouponsMember';
	public $languageTopics = array('msdiscount');
	public $permission = 'msdiscount_save';


	/** {inheritDoc} */
	public function initialize() {
		if (!$this->object = $this->modx->getObject($this->classKey,
			array(
				'coupons_id' => $this->getProperty('coupons_id'),
				'group_id' => $this->getProperty('group_id'),
				'type' => $this->getProperty('type'),
			)
		)) {
			return $this->modx->lexicon($this->objectType.'_err_nfs');
		};

		if ($this->checkSavePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('save')) {
			return $this->modx->lexicon('access_denied');
		}
		return true;
	}


	/** {inheritDoc} */
	public function beforeSet() {
		$required = array('relation');
		foreach ($required as $v) {
			if ($this->getProperty($v) == '') {
				$this->modx->error->addField($v, $this->modx->lexicon('msd_err_ns'));
			}
		}

		return parent::beforeSet();
	}

}

return 'msdCouponsMemberUpdateProcessor';
