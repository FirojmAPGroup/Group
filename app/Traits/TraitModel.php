<?php
namespace App\Traits;

use Request, Validator;
use App\Helpers\FileHelper;

trait TraitModel{

	// Get Data / Set Data
	public function getId(){ return $this->id ?: 0; }

	public function getProductId(){ return $this->i_product_id; }
	public function getSlug(){ return $this->v_slug; }

	public function getName(){ return $this->name; }
	public function getEmail(){ return $this->email; }
	public function getUsername(){ return $this->username; }
	public function getNameWithStatus(){ return $this->getName().''.(!$this->isActive() ? ' ('.$this->printStatus().')' : ''); }
	public function getDescription(){ return $this->l_description; }

	public function getStatus(){ return $this->getId() ? $this->ti_status : NULL; }
	public function printStatus(){ return $this->isActive() ? 'Active' : 'Inactive'; }
	public function isActive(){ return $this->getStatus() == 1; }
	public function makeActive(){ $this->ti_status = 1; }
	public function makeInactive(){ $this->ti_status = 0; }
	public function getRehabId(){	return $this->rehab_id;	}
	public function getOrder(){ return $this->i_order; }
	public function printOrder(){ return $this->i_order; }

	public function setAdded(){ if(!$this->getId()) $this->created_at = dbDate(); }
	public function getCreated(){ return $this->created_at; }
	public function showCreated($datetime=0){ return frDate($this->getCreated(), $datetime); }

	public function setUpdated(){ $this->updated_at = dbDate(); }
	public function getUpdated(){ return $this->updated_at; }
	public function showUpdated($datetime=0){ return frDate($this->getUpdated(), $datetime); }

	public static function folderName($folder=NULL){
		if($folder) return [$folder, self::FOLDER];
		return [self::FOLDER];
	}
	public function putFile($key, $file, $folder){
		if($file){
			$oldName = $this->$key;
			if($newName = FileHelper::upload($file, self::folderName($folder))){
				if($oldName)
				FileHelper::delete($oldName, self::folderName($folder));
				$this->$key = $newName;
			}
		}
	}


	public static function routeBase() {
		return self::BASE_ROUTE;
	}
	public static function makeUrl($name, $args = []) {
		return routePut(self::routeBase().$name, $args);
	}
	public function urlEdit() { return self::makeUrl('edit', ['id' => encrypt($this->getId())]); }
	public function urlView() { return self::makeUrl('view', ['id' => encrypt($this->getId())]); }
	public function urlDelete() { return self::makeUrl('delete', ['id' => encrypt($this->getId())]); }
	public function urlSave() { return self::makeUrl('save', ['id' => encrypt($this->getId())]); }
	public function urlAdmissionEdit() { return self::makeUrl('admission.edit', ['patient_id'=>encrypt($this->patient_id),'id' => encrypt($this->getId()),]); }
	public function urlAdmissionView() { return self::makeUrl('admission.view', ['patient_id'=>encrypt($this->patient_id),'id' => encrypt($this->getId()),]); }
	public function listStatusBadge(){
		return '<span class="' . ($this->isActive() ? 'badge badge-success' : 'badge badge-danger') . '">' . $this->printStatus() . '</span>';
	}

    public function leadStatus(){
        if($this->getStatus() == 1){
            return '<span class="badge badge-success">Completed</span>';
        }elseif($this->getStatus() == 2){
            return '<span class="badge badge-warning">Pending</span>';
        } elseif($this->getStatus() == 3) {
            return '<span class="badge badge-info">Hold</span>';
        } elseif($this->getStatus() == 4){
            return '<span class="badge badge-danger">Reject</span>';
        }elseif($this->getStatus() == 5){
            return '<span class="badge badge-secondary">Assigned</span>';
        } else {
            return '<span class="badge badge-dark">Not Assign</span>';
        }
     }
}
