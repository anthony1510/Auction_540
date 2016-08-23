<?php
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE {$this->getTable('auction_product')} 
        ADD `deposit` decimal(12,4) NOT NULL default '0.000';
	
	DROP TABLE IF EXISTS {$this->getTable('auction_deposit')};
	
	CREATE TABLE {$this->getTable('auction_deposit')}  (
	  `auctiondeposit_id` int(11) NOT NULL auto_increment,
	  `productauction_id` int(11) NOT NULL default '0',
	  `product_id` int(11) NOT NULL default '0',
	  `product_name` varchar(255) NOT NULL default '',
	  `customer_id` int(11) NOT NULL default '0',
	  `customer_name` varchar(255) NOT NULL default '',
	  `customer_email` varchar(255) NOT NULL default '',
	  `customer_phone` varchar(50) default NULL,
	  `customer_address` text default NULL,    
	  `deposit` decimal(12,4) NOT NULL default '0.000',  
	  `status` tinyint(1) default '1',
	  `store_id` smallint(5) unsigned NOT NULL,
	  INDEX(`store_id`),
      FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
	  FOREIGN KEY ( `productauction_id` ) REFERENCES {$this->getTable('auction_product')} ( `productauction_id` ) ON DELETE CASCADE ON UPDATE CASCADE,
	  PRIMARY KEY  (`auctiondeposit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;
 ");
 $bids = Mage::getModel('auction/auction')->getCollection();
        if (count($bids)) {
            foreach ($bids as $bid) {


                if(count(Mage::getModel('auction/deposit')->getCollection()
                        ->addFieldToFilter('customer_id', $bid->getCustomerId())
                        ->addFieldToFilter('productauction_id',$bid->getProductauctionId())) == 0)
                {
                    $model = Mage::getModel('auction/deposit');
                    $model->setProductauctionId($bid->getProductauctionId());
                    $model->setProductId($bid->getProductId());
                    $model->setProductName($bid->getProductName());
                    $model->setCustomerId($bid->getCustomerId());
                    $model->setCustomerName($bid->getCustomerName());
                    $model->setCustomerEmail($bid->getCustomerEmail());
                    $model->setCustomerPhone($bid->getCustomerPhone());
                    $model->setCustomerAddress($bid->getCustomerAddress());
                    $model->setStatus('2');
                    $model->setStoreId($bid->getStoreId());
                    $model->save();
                };

            }

        }
		

$installer->endSetup(); 