<?php

class msdCouponGroupCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'msdCouponGroup';
	public $classKey = 'msdCouponGroup';
	public $languageTopics = array('msdiscount');
	public $permission = 'msdiscount_save';


	/** {inheritDoc} */
	public function beforeSet() {
		/** @var msDiscount $msDiscount */
		$msDiscount = $this->modx->getService('msDiscount');

		$properties = $this->getProperties();
		foreach ($properties as $k => $v) {
			$properties[$k] = $msDiscount->sanitize($k, $v);
		}
		$this->setProperties($properties);

		$required = array('name', 'discount', 'coupons');
		foreach ($required as $v) {
			$value = trim($this->getProperty($v));
			if (empty($value) || $value == '0%') {
				$this->modx->error->addField($v, $this->modx->lexicon('msd_err_ns'));
			}
		}

		$unique = array('name');
		foreach ($unique as $v) {
			if ($this->modx->getCount($this->classKey, array($v => $this->getProperty($v)))) {
				$this->modx->error->addField($v, $this->modx->lexicon('msd_err_ae'));
			}
		}

        $prefix_accees = true;
        $prefix = $this->getProperty('prefix');
        $disposable = $this->getProperty('disposable');
        $prefix_length = $disposable == true ? 5 : $this->modx->getOption('msd_coupons_prefix_length', NULL, 5);


        if($disposable != true){


            if(strlen($prefix) > $prefix_length and strlen($prefix) != $prefix_length){
                $this->modx->error->addField('prefix', $this->modx->lexicon('msd_err_prefix', array('prefix_length' => $prefix_length)));
                $prefix_accees = false;
            }

            if(!preg_match('~^[a-z0-9_\-]*$~i', $prefix)){
                $this->modx->error->addField('prefix', $this->modx->lexicon('msd_err_prefix', array('prefix_length' => $prefix_length)));
                $prefix_accees = false;
            }

        } else {

            if(strlen($prefix) > $prefix_length and strlen($prefix) != $prefix_length){
                $this->modx->error->addField('prefix', $this->modx->lexicon('msd_err_prefix', array('prefix_length' => $prefix_length)));
                $prefix_accees = false;
            }

            if (!empty($prefix) && !preg_match('#[A-Z0-9]{5}#i', $prefix)) {
                $this->modx->error->addField('prefix', $this->modx->lexicon('msd_err_prefix'));
                $prefix_accees = false;
            }

        }

        if($prefix_accees == true){
            $this->setProperty('prefix', strtoupper($prefix));
        }

		return parent::beforeSet();
	}

}

return 'msdCouponGroupCreateProcessor';