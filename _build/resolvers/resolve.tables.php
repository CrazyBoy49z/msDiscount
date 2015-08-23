<?php
/**
 * @var xPDOObject $object
 * @var array $options
 * @var modX $modx
  */
if ($object->xpdo) {
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modelPath = $modx->getOption('msdiscount_core_path',null,$modx->getOption('core_path').'components/msdiscount/').'model/';
			$modx->addPackage('msdiscount', $modelPath);

			$manager = $modx->getManager();
			$tmp = array(
				'msdSale',
				'msdUserGroup',
				'msdProductGroup',
				'msdSaleMember',
				'msdCouponGroup',
				'msdCoupon',
				'msdCouponsMember',
			);
			foreach ($tmp as $v) {
				$manager->createObjectContainer($v);
			}

			$groups = $modx->getCollection('modUserGroup');
			foreach ($groups as $group) {
				if (!$modx->getCount('msdUserGroup', $group->id)) {
					$new = $modx->newObject('msdUserGroup');
					$new->fromArray(array(
						'id' => $group->id,
						'discount' => '0%',
						'joinsum' => 0
					), '', true, true);
					$new->save();
				}
			}

			$groups = $modx->getCollection('modResourceGroup');
			foreach ($groups as $group) {
				if (!$modx->getCount('msdProductGroup', $group->id)) {
					$new = $modx->newObject('msdProductGroup');
					$new->fromArray(array(
						'id' => $group->id,
						'discount' => '0%',
					), '', true, true);
					$new->save();
				}
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
