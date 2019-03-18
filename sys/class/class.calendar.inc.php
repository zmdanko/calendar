<?php

/**
 * 创建和维护日程表
 */

class Calendar extends DB_Connect{
	//根据此日期创建日历
	private $useDate;
	//日历显示年份
	private $year;
	//日历显示月份
	private $month;
	//当月天数
	private $daysInMonth;
	//当月起始星期几
	private $monthStartDay;

	//连接数据库
	public function __construct($dbo=NULL,$useDate=NULL){
		//调用父类构造函数连接数据库
		parent::__construct($dbo);
		//收集用户日期,如果没有使用当前日期
		if($useDate!=NULL){
			$this->useDate = $useDate;
		}else{
			$this->useDate = strtotime(date('Y-m-d H:i:s'));
		}
		//将日期时间解析为Unix时间戳
		//$UnixUseDate = strtotime($this->useDate); 
		$this->month = date('m',$this->useDate);
		$this->year = date('Y',$this->useDate);
		//确定当月天数
		$this->daysInMonth = cal_days_in_month(CAL_GREGORIAN,$this->month,$this->year);
		//确定起始星期几
		$UnixStartDay = mktime(0,0,0,$this->month,1,$this->year);
		$this->monthStartDay = date('w',$UnixStartDay);
	}

	//读取活动信息载入数组
	private function loadEventData($id=NULL){
		$sql = 'SELECT `event_id`,`event_title`,`event_desc`,`event_start`,`event_end` FROM `events`';
		//如果提供id，返回该活动，否则载入当月所有活动
		if(!empty($id)){
			$sql .= 'WHERE `event_id`=:id LIMIT 1';
		}else{
			//找出当月第一天和最后一天
			$start_mk = mktime(0,0,0,$this->month,1,$this->year);
			$end_mk = mktime(23,59,59,$this->month+1,0,$this->year);
			$startDate = date('Y-m-d H:i:s',$start_mk);
			$endDate = date('Y-m-d H:i:s',$end_mk);
			//找出当月活动
			$sql .= "WHERE `event_start` BETWEEN '$startDate' AND '$endDate' ORDER BY `event_start`";
		}
		try{
			$stmt = $this->db->prepare($sql);
			if(!empty($id)){
				$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			}
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			return $results;
		}catch(Exception $e){
			die($e->getMessage());
		}
	}

	//按照活动发生日期将活动数据重新组织到新数组
	private function createEvent(){
		//载入活动数组
		$arr = $this->loadEventData();
		$events = array();
		foreach($arr as $event){
			$day = date('j',strtotime($event['event_start']));
			try{
				$events[$day][] = new Event($event);
			}
			catch(Exception $e){
				die($e->getMessage());
			}
		}
		return $events;
	}

	//根据ID得到活动对象
	private function loadEventById($id){
		if(empty($id)){
			return NULL;
		}
		$event = $this->loadEventData($id);
		if(isset($event[0])){
			return new Event($event[0]);
		}else{
			return NULL;
		}
	}

	//生成日历HTML
	public function buildCalendar(){
		$cal_month = date('Y-m',$this->useDate);
		$weekdays = array('日','一','二','三','四','五','六');
		$html = "<h2 id=\"month-$cal_month\"><a href=\"setDate.php\">$cal_month</a></h2>";
		for($d=0,$labels=NULL;$d<7;++$d){
			$labels .= "\n\t<li>".$weekdays[$d].'</li>';
		}
		$html .= "\n<ul class=\"weekdays\">".$labels."\n</ul>\n<ul>";
		//载入活动数据
		$events = $this->createEvent();
		for($i=1,$c=1,$j=date('j'),$m=date('m'),$y=date('Y');$c<=$this->daysInMonth;$i++){
			//起始日之前添加class fill，填充空格
			$class = $i<=$this->monthStartDay ? 'fill' : NULL;
			//为今天日期添加class today
			if($c==$j && $m==$this->month && $y==$this->year){
				$class = 'today';
			}
			//生成<li>标记
			$liStart = sprintf("\n\t<li class=\"%s\">",$class);
			$liEnd = '</li>';
			//日历主体
			$event_info = NULL;
			if($this->monthStartDay<$i && $this->daysInMonth>=$c){	
				if(isset($events[$c])){
					foreach ($events[$c] as $event) {
						$event_info .= '<a href="view.php?event_id='.$event->id.'">'.$event->title.'</a>';
					}
				}
				$date = sprintf('<strong>%02d</strong>',$c++);
			}else{
				$date='&nbsp;';
			}
			//周六后换行
			$isSat = $i!=0&&$i%7==0 ? "\n</ul>\n<ul>" : NULL;
			$html .= $liStart . $date . $event_info . $liEnd . $isSat;
		}
		//填充最后一周
		while($i%7!=1){
			$html .= "\n\t<li class=\"fill\">&nbsp;</li>";
			++$i;
		}
		$html .= "\n</ul>\n";
		$add = $this->adminButton();
		return $html.$add;
	}

	//生成活动详细信息HTML
	public function displayEvent($id){
		if(empty($id)){
			return NULL;
		}
		$id = preg_replace("/[^0-9]/",'',$id);
		//载入活动数据
		$event = $this->loadEventById($id);
		$startDate = strtotime($event->start);
		$endDate = strtotime($event->end);
		$start = date('Y m d G:i',$startDate);
		$end = date('Y m d G:i',$endDate);
		$modify = $this->modifyButton($id);
		//生成HTML标记
		return "<h2>$event->title</h2>"."\n<p class=\"dates\">$start-$end</p>"."\n<p>$event->description</p>$modify";

	}

	//生成或修改活动表单
	public function displayForm(){
		if(isset($_POST['event_id'])){
			$id = (int) $_POST['event_id'];
		}else{
			$id = NULL;
		}
		$submit = '创建活动';
		//如果传入ID显示活动数据
		if(!empty($id)){
			$event = $this->loadEventById($id);
			if(!is_object($event)){
				return NULL;
			}
			$submit = '编辑活动';
		}
		//生成HTML
		return<<<EOF
			<form action='assets/inc/process.inc.php' method='post'>
				<fieldset>
					<legend>{$submit}</legend>
						<label for='event_title'>活动标题</label>
						<input type='text' name='event_title' id='event_title' value='$event->title' autocomplete="off" />
						<label for='event_start'>开始时间</label>
						<input type='text' name='event_start' id='event_start' value='$event->start' autocomplete="off" />
						<label for='event_end'>结束时间</label>
						<input type='text' name='event_end' id='event_end' value='$event->end' autocomplete="off" />
						<label for='event_description'>活动详情</label>
						<textarea name='event_description' id='event_description'>$event->description</textarea>
						<input type='hidden' name='event_id' value='$event->id' />
						<input type='hidden' name='token' value='$_SESSION[token]' />
						<input type='hidden' name='action' value='event_edit' />
						<input type='submit' name='event_submit' value='$submit' />
						/<a href='./'>取消</a>
				</fieldset>
			</form>
EOF;
	}

	//活动保存到数据库
	public function processForm(){
		if($_POST['action']!='event_edit'){
			return '方法错误';
		}
		$title = htmlentities($_POST['event_title'],ENT_QUOTES);
		$desc = htmlentities($_POST['event_description'],ENT_QUOTES);
		$start = htmlentities($_POST['event_start'],ENT_QUOTES);
		$end = htmlentities($_POST['event_end'],ENT_QUOTES);
		if(!$this->validDate($start)||!$this->validDate($end)){
			return "日期格式错误，请输入 0000-00-00 00:00:00格式日期";
		}
		//如果活动ID为空，创建新活动
		if(empty($_POST['event_id'])){
			$sql = 'INSERT INTO `events` (`event_title`,`event_desc`,`event_start`,`event_end`) VALUES (:title,:description,:start,:end)';
		}else{
			$id = (int) $_POST['event_id'];
			$sql = "UPDATE `events` SET 
				`event_title`=:title,
				`event_desc`=:description,
				`event_start`=:start,
				`event_end`=:end
				WHERE `event_id`=$id";
		}
		try{
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':title',$title,PDO::PARAM_STR);
			$stmt->bindParam(':description',$desc,PDO::PARAM_STR);
			$stmt->bindParam(':start',$start,PDO::PARAM_STR);
			$stmt->bindParam(':end',$end,PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return $this->db->lastInsertId();
		}catch (Exception $e){
			return $e->getMessage();
		}
	}

	//新建活动按钮
	private function adminButton(){
		if(isset($_SESSION['user'])){			
			return <<<EOF
				<a href="admin.php" class="admin" name="event_form">新建活动</a>
				<form action="assets/inc/process.inc.php" method="post">
					<div>
						<input type="submit" value="注销" class="log" />
						<input type="hidden" name="token" value="$_SESSION[token]" />
						<input type="hidden" name="action" value="user_logout" />
					</div>
				</form>
EOF;
		}else{
			return '<a href="login.php">登陆</a>';
		}

	}

	//编辑和删除活动按钮
	private function modifyButton($id){
		if(isset($_SESSION['user'])){
			return <<<EOF
				<div class="adminButton">
					<form action="admin.php" method="post">
						<p>
							<input type="submit" name="event_form" value="编辑活动" />
							<input type="hidden" name="event_id" value="$id" />
						</p>
					</form>
					<form action="confirmDelete.php" method="post">
						<p>
							<input type="submit" name="delete_event" value="删除活动" />
							<input type="hidden" name="event_id" value="$id" />
						</p>
					</form>
				</div>
EOF;
		}
	}

	//确认删除活动界面
	public function confirmDelete($id){
		if(empty($id)){
			return NULL;
		}
		$id = preg_replace('/[^0-9]/' , '' , $id);
		if(isset($_POST['sure_delete']) && $_POST['token']==$_SESSION['token']){
			$sql = 'DELETE FROM `events` WHERE `event_id`=:id LIMIT 1';
			try{
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				return TRUE;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}
		$event = $this->loadEventById($id);
		if(!is_object($event)){
			header("Location:./");
		}
		return <<<EOF
			<form id="delete" action="confirmdelete.php" method="post">
				<h2>确定删除{$event->title}?</h2>
				<input type="submit" name="confirm_delete" value="确认删除" />
				/<a href='./'>取消</a>
				<input type="hidden" name="sure_delete" value="sure_delete" />
				<input type="hidden" name="action" value="confirm_delete" />
				<input type="hidden" name="event_id" value="$event->id" />
				<input type="hidden" name="token" value="$_SESSION[token]" />
			</form>
EOF;
	}

	//验证日期
	private function validDate($date){
		$pattern = '/^(\d{4}(-\d{2}){2} (\d{2})(:\d{2}){2})$/';

		return preg_match($pattern, $date)==1 ? TRUE : FALSE;
	}
}
?>