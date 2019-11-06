<?php

namespace Unilab\Benefits\Cron;

class Autorefreshperiod {
 
    protected $_resourceConnection;
 
    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection) {
        $this->_resourceConnection = $resourceConnection;
    }
	public function execute()
	{
		try{
			$res = $this->refreshBenefits();
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cronEmpBenefitsRefreshPeriod.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($res);
		}catch(\Exception $e){
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cronEmpBenefitsRefreshPeriod.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($e->getMessage());
		}
		

		return $this;

	}
    protected function _getConnection()
    {
		$this->_Connection = $this->_resourceConnection->getConnection('core_write');	
        return $this->_Connection;
    }	
			
	public function _getresfreshPeriod($option_id)
	{	
		$unilabrefreshperiod 	= "SELECT value FROM eav_attribute_option_value WHERE option_id='$option_id'";
		$RPresult 				= $this->_getConnection()->fetchRow($unilabrefreshperiod);	
		return $RPresult['value'];		
	}

    public function refreshBenefits() {
		
		date_default_timezone_set('Asia/Taipei');
		
		try{
			$TodayDay 	= date("d");
			$Todayhour 	= date("G:i");
			$is_create 	= false;
			$SqlBenefits = $this->_getConnection()->select()->from('rra_emp_benefits', array('*'))
								->where('entity_id >?',0);     
			$BenefitsResult = $this->_getConnection()->fetchAll($SqlBenefits); 
			
			foreach($BenefitsResult as $refreshPeriodID):
				$refreshPeriod 	= $this->_getresfreshPeriod($refreshPeriodID['refresh_period']);
				$refresh_date	= strtotime($refreshPeriodID['refresh_date']);
				$RPdate 		= date('Y-m-d',$refresh_date);
				$TodayDate		= date('Y-m-d');
					if(strtotime($TodayDate)  >= strtotime($RPdate)):
						$dataToupdate 				= array();
						$dataToupdate['p_limit'] 	= $refreshPeriodID['purchase_cap_limit'];
						$dataToupdate['id'] 		= $refreshPeriodID['id'];
						$DateNextMonth				= null;
						if(strtolower($refreshPeriod) == "monthly"):
							$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +1 month");
						elseif(strtolower($refreshPeriod) == "semi-monthly"):
							$month = date('n');
							$exploded = explode('-',$RPdate);
							$year = $exploded[1];
							$month = $exploded[1];
							$day = $exploded[2];
							if($day==1):
								$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +15 days");
							else:
								if($month==2):
									$numberofdays = date('t',strtotime($RPdate));
									if($numberofdays == 28):
										$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +13 days");
									else:
										$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +14 days");
									endif;
								else:
									if($month % 2 == 0):
										$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +16 days");
									else:
										$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +15 days");	
									endif;
								endif;
							endif;
						elseif(strtolower($refreshPeriod) == "yearly"):
							$DateNextMonth = strtotime(date("Y-m-d", strtotime($refreshPeriodID['refresh_date'])) . " +12 month");
						endif;
						if(!empty($DateNextMonth)):				
							$dataToupdate['refreshPeriod'] 	= date("Y-m-d", $DateNextMonth);
							$this->updateBenefits($dataToupdate);
							$is_create 	= true;
						endif;
					endif;
			endforeach;
			if($is_create 	= true):
				$this->createaudittrail($is_create);
			endif;
			$response['msg'] 	= "Done updating Refresh Date.";
		}catch(\Exception $e){			
			$response['msg'] 	= "Error updating Refresh Date.";
		}
		return $response;
    }
	
	
	///********** Update benefit and refresh date -->>>
	
	protected function updateBenefits($dataToupdate)
	{
		$id 			= $dataToupdate['id'];
		$p_limit 		= $dataToupdate['p_limit'];
		$refresh_date 	= $dataToupdate['refreshPeriod'];
        $date           = date("Y-m-d h:i:s");
		
		$sqlUpdate 	= "UPDATE rra_emp_benefits SET available='$p_limit', consumed=0, update_time='$date', refresh_date='$refresh_date' WHERE id=$id";
		$this->_getConnection()->query($sqlUpdate);	
						
		return $this;
	}
	
	///********** Create Audit Trail -->>>

	protected function createaudittrail($is_create)
	{
		if($is_create == true):
			$TodayDay 	= date("Y-m-d h:i:s");
			$sqlUpdate 	= "INSERT INTO unilab_audit_trail_refreshbenefist (date_created, created_by) VALUES ('$TodayDay','system-cronjob')";
			$this->_getConnection()->query($sqlUpdate);	
		endif;
		
		return $this;		
	}
}