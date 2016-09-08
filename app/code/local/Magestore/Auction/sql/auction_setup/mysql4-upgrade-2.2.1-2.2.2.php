<?php
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE {$this->getTable('auction_product')} 
        ADD `last_date_for_auction` DATE NOT NULL;
 ");
$collection = Mage::getModel('auction/productauction')->getCollection();
foreach ($collection as $auction){
    $auction->setLastDateForAuction($auction->getEndDate());
    $auction->save();
}
$installer->endSetup(); 