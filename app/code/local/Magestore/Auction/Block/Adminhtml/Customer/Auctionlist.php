<?php

class Magestore_Auction_Block_Customer_Auctionlist extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        $return = parent::_prepareLayout();

        $type_biddername = Mage::getStoreConfig('auction/general/bidder_name_type');
        if ($type_biddername == '2') {//user create bidder name
            $customer = $this->getCustomer();

            if ($customer->getBidderName()) {
                $this->setTemplate('auction/customer/customerauctionlist.phtml');
            } else {
                $this->setTemplate('auction/customer/form_biddername.phtml');
            }
        } else { //system created bidder name
            $this->setTemplate('auction/customer/customerauctionlist.phtml');
        }
        return $return;
    }

    public function getCustomer() {
        if (!$this->hasData('customer')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }
        return $this->getData('customer');
    }
//    public function getListCustomerbid() {
//        if (!$this->hasData('listcustomerbid')) {
//            $customerId = $this->getCustomer()->getId();
//
//            $collection = Mage::getModel('auction/auction')
//                ->getListBidByCustomerId($customerId);
//
//            // $curr_page = $this->getRequest()->getParam('page');
//            // $curr_page = $curr_page ? $curr_page : 1;
//            // $collection->setPageSize(20);
//            // $collection->setCurPage($this->getRequest()->getParam('page'));
//
//            $this->setData('listcustomerbid', $collection);
//        }
//        return $this->getData('listcustomerbid');
//    }
    public function getAuctionList() {

        if (!$this->hasData('listcustomerauctionlist')) {
            $auction_id = $this->getRequest()->getParam('id');
            $curr_page = $this->getRequest()->getParam('page');
            $curr_page = $curr_page ? $curr_page : 1;

            $customerId = $this->getCustomer()->getId();

            $collection = Mage::getModel('auction/deposit')->getCollection()
                ->addFieldToFilter('customer_id',$customerId);
            $collection->getSelect()
                ->joinLeft( array('productauction' => 'auction_product'),
                            'productauction.productauction_id=main_table.productauction_id',
                            array('status2' => 'status'))
                ->where('productauction.status=5 or productauction.status=4');

            $collection->setPageSize(10);

            $collection->setCurPage($curr_page);

            $this->setData('listcustomerauctionlist', $collection);
        }
        return $this->getData('listcustomerauctionlist');
    }

    public function getStatusAuctionList($productauctionId){
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $auctionDeposit = Mage::getModel('auction/deposit')->getCollection()
            ->addFieldToFilter('productauction_id',$productauctionId)
            ->addFieldToFilter('customer_id',$customerId);
        if(count($auctionDeposit) == 0){
            return 0;
        }else{
            if($auctionDeposit->getFirstItem()->getStatus() == 1){
                return 1;
            }else{
                $productauction = Mage::getModel('auction/productauction')->load($productauctionId);
                $statusPA = $productauction->getStatus();
                if($statusPA == 4){
                    $auctionlastbidcustomer = Mage::getModel('auction/auction')->getCollection()
                        ->addFieldToFilter('productauction_id',$productauctionId)
                        ->addFieldToFilter('customer_id',$customerId);
                    if(count($auctionlastbidcustomer) == 0){
                        return 2;
                    }else{
                        return 3;
                    }
                }
                if($statusPA == 5){
                    $auctionlastbidcustomer = Mage::getModel('auction/auction')->getCollection()
                        ->addFieldToFilter('productauction_id',$productauctionId)
                        ->addFieldToFilter('customer_id',$customerId)->getLastItem();
                    return $auctionlastbidcustomer->getStatus();
                }
            }
        }
        return 0;
    }



    public function getNavHtml() {
        $auction_id = $this->getRequest()->getParam('id');
        $curr_page = $this->getRequest()->getParam('page');
        $curr_page = $curr_page ? $curr_page : 1;

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $collection = $this->getAuctionList();
        $collection->setPageSize(20);

        $last_page = $collection->getLastPageNumber();

        $html = ' ';

        if ($last_page > 1) {
            $html .= '<div class="auction-nav">' . $this->__('Pages') . ' ';

            for ($i = 1; $i <= $last_page; $i++) {
                if ($i != $curr_page)
                    $html .= '<a href="' . $this->getUrl('auction/index/customerbid', array('page' => $i)) . '" >' . $i . '</a>';
                else
                    $html .= '<span class="ative" >' . $i . '</span>';
            }

            $html .= '</div>';
        }
        return $html;
    }

}
