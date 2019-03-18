<?php
/**
 * 保存活动信息
 */
class Event{
	//活动id
	public $id;
	//活动标题
	public $title;
	//活动描述
	public $description;
	//活动开始时间
	public $start;
	//活动结束时间
	public $end;
	//接受活动数据并储存
	public function __construct($event){
		if(is_array($event)){
			$this->id = $event['event_id'];
			$this->title = $event['event_title'];
			$this->description = $event['event_desc'];
			$this->start = $event['event_start'];
			$this->end = $event['event_end'];
		}else{
		throw new Exception('没有活动');
		}
	}
}