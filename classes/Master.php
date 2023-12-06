<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success'," New Category successfully saved.");
			else
				$this->settings->set_flashdata('success'," Category successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_query(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM contact_us WHERE id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Query successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function export_data(){
		$filename = 'booking_list.csv';
		$isWritten = false;
		
		if (file_exists($filename)) {
			unlink($filename);
		}
		
		$fp = fopen($filename, 'w');
		$header = array('Date', 'Amount Earned');
		fputcsv($fp, $header);
		
		$query = "SELECT DATE_FORMAT(date_created, '%Y-%m') AS month_year, SUM(amount * 0.10) AS total_earned_amount FROM booking_list WHERE     status >= 2 AND status != 5 
		GROUP BY month_year";
		$result = $this->conn->query($query);
		
		if ($result->num_rows > 0) {
			$isWritten = true;
			while ($row = $result->fetch_assoc()) {
				fputcsv($fp, $row);
			}
		}
		
		fclose($fp);
		
		if ($isWritten) {
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			readfile($filename);
		
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Exported Successfully");
			echo json_encode($resp);
		} else {
			$resp['status'] = 'error';
			$this->settings->set_flashdata('error', "Failed to export data");
			echo json_encode($resp);
		}
	}

	function save_cab(){
		if(!empty($_POST['password']))
			$_POST['password'] = md5($_POST['password']);
		// else
		// 	unset($_POST['password']);
		if(empty($_POST['id'])){
			$prefix = date('Ym-');
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `cab_list` where reg_code = '{$prefix}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$_POST['reg_code'] = $prefix.$code;
		}


		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','oldpassword'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($cab_reg_no)){
			$check = $this->conn->query("SELECT * FROM `cab_list` where `cab_reg_no` = '{$cab_reg_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = " Cab already exist.";
				return json_encode($resp);
				exit;
			}
		}
		if(isset($body_no)){
			$check = $this->conn->query("SELECT * FROM `cab_list` where `body_no` = '{$body_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = " Cab Body # already exist.";
				return json_encode($resp);
				exit;
			}
		}
		// if(isset($oldpassword)){
		// 	$cur_pass = $this->conn->query("SELECT `password` from `cab_list` where id = '{$this->settings->userdata('id')}'")->fetch_array()[0];
		// 	if(md5($oldpassword) != $cur_pass){
		// 		$resp['status'] = 'failed';
		// 		$resp['msg'] = " Current Password is Incorrect.";
		// 		return json_encode($resp);
		// 		exit;
		// 	}
		// }
		if(empty($id)){
			$sql = "INSERT INTO `cab_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `cab_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			$cid = empty($id) ? $this->conn->insert_id : $id;
			$resp['id'] = $cid ;
			if(empty($id))
				$resp['msg'] = " New Cab successfully saved.";
			else
				$resp['msg'] = " Cab successfully updated.";
				if($this->settings->userdata('id')  == $cid && $this->settings->userdata('login_type') == 3){
					foreach($_POST as $k => $v){
						if(!in_array($k,['password']))
						$this->settings->set_userdata($k,$v);
					}
					$resp['msg'] = " Account successfully updated.";
				}
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					if(!is_dir(base_app."uploads/dirvers/"))
						mkdir(base_app."uploads/dirvers/");
					$fname = 'uploads/dirvers/'.$cid.'.png';
					$dir_path =base_app. $fname;
					$upload = $_FILES['img']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg');
					if(!in_array($type,$allowed)){
						$resp['msg'].=" But Image failed to upload due to invalid file type.";
					}else{
						$new_height = 200; 
						$new_width = 200; 
				
						list($width, $height) = getimagesize($upload);
						$t_image = imagecreatetruecolor($new_width, $new_height);
						imagealphablending( $t_image, false );
						imagesavealpha( $t_image, true );
						$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
						imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						if($gdImg){
								if(is_file($dir_path))
								unlink($dir_path);
								$uploaded_img = imagepng($t_image,$dir_path);
								imagedestroy($gdImg);
								imagedestroy($t_image);
						}else{
						$resp['msg'].=" But Image failed to upload due to unkown reason.";
						}
					}
					if(isset($uploaded_img)){
						$this->conn->query("UPDATE cab_list set `image_path` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$cid}' ");
						if($id == $this->settings->userdata('id')){
								$this->settings->set_userdata('avatar',$fname);
						}
					}
				}
				if(isset($_FILES['img1']) && $_FILES['img1']['tmp_name'] != ''){
					if(!is_dir(base_app."uploads/drivers/")){
						mkdir(base_app."uploads/drivers/");
					}
					$fname = 'uploads/drivers/'.$_FILES['img1']['name']; 
					$dir_path = base_app.$fname;
					$upload = $_FILES['img1']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg');
					if(!in_array($type, $allowed)){
						$resp['msg'].=" But Image failed to upload due to invalid file type.";
					} else {
						$new_height = 200; 
						$new_width = 200; 
						list($width, $height) = getimagesize($upload);
						$t_image = imagecreatetruecolor($new_width, $new_height);
						imagealphablending( $t_image, false );
						imagesavealpha( $t_image, true );
						$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
						imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						if ($gdImg) {
							if (is_file($dir_path)) {
								unlink($dir_path);
								$uploaded_img = imagepng($t_image,$dir_path);
							}
							if (imagepng($t_image, $dir_path)) {
								imagedestroy($gdImg);
								imagedestroy($t_image);
							} else {
								$resp['msg'] = "Failed to save the image as PNG.";
							}
						} else {
							$resp['msg'] = "Failed to create image resource from the uploaded file.";
						}
					}
					if(isset($uploaded_img)){
						$this->conn->query("UPDATE cab_list SET `cab_image_path` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$cid}' ");
						if($id == $this->settings->userdata('id')){
							// $this->settings->set_userdata('avatar', $fname);
						}
					}				
				}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if(isset($resp['msg']) && $resp['status'] == 'success'){
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}

	function edit_cab(){
		// if(!empty($_POST['password']))
		// 	$_POST['password'] = md5($_POST['password']);
		// else
		// 	unset($_POST['password']);
		if(empty($_POST['id'])){
			$prefix = date('Ym-');
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `cab_list` where reg_code = '{$prefix}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$_POST['reg_code'] = $prefix.$code;
		}


		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','oldpassword'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($cab_reg_no)){
			$check = $this->conn->query("SELECT * FROM `cab_list` where `cab_reg_no` = '{$cab_reg_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = " Cab already exist.";
				return json_encode($resp);
				exit;
			}
		}
		if(isset($body_no)){
			$check = $this->conn->query("SELECT * FROM `cab_list` where `body_no` = '{$body_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = " Cab Body # already exist.";
				return json_encode($resp);
				exit;
			}
		}
		// if(isset($oldpassword)){
		// 	$cur_pass = $this->conn->query("SELECT `password` from `cab_list` where id = '{$this->settings->userdata('id')}'")->fetch_array()[0];
		// 	if(md5($oldpassword) != $cur_pass){
		// 		$resp['status'] = 'failed';
		// 		$resp['msg'] = " Current Password is Incorrect.";
		// 		return json_encode($resp);
		// 		exit;
		// 	}
		// }
		if(empty($id)){
			$sql = "INSERT INTO `cab_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `cab_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			$cid = empty($id) ? $this->conn->insert_id : $id;
			$resp['id'] = $cid ;
			if(empty($id))
				$resp['msg'] = " New Cab successfully saved.";
			else
				$resp['msg'] = " Cab successfully updated.";
				if($this->settings->userdata('id')  == $cid && $this->settings->userdata('login_type') == 3){
					foreach($_POST as $k => $v){
						if(!in_array($k,['password']))
						$this->settings->set_userdata($k,$v);
					}
					$resp['msg'] = " Cab Details successfully updated.";
				}
			
				if(isset($_FILES['img1']) && $_FILES['img1']['tmp_name'] != ''){
			

					if(!is_dir(base_app."uploads/drivers/")){
						mkdir(base_app."uploads/drivers/");
					}
					$fname = 'uploads/drivers/'.$_FILES['img1']['name']; 
					$dir_path = base_app.$fname;
					$upload = $_FILES['img1']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg');
					if(!in_array($type, $allowed)){
						$resp['msg'].=" But Image failed to upload due to invalid file type.";
					} else {
						$new_height = 200; 
						$new_width = 200; 
						list($width, $height) = getimagesize($upload);
						$t_image = imagecreatetruecolor($new_width, $new_height);
						imagealphablending( $t_image, false );
						imagesavealpha( $t_image, true );
						$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
						imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						$uploaded_img = imagepng($t_image,$dir_path);
						if ($gdImg) {
							if (is_file($dir_path)) {
								unlink($dir_path);
							}
							if (imagepng($t_image, $dir_path)) {
								imagedestroy($gdImg);
								imagedestroy($t_image);
							} else {
								$resp['msg'] = "Failed to save the image as PNG.";
							}
						} else {
							$resp['msg'] = "Failed to create image resource from the uploaded file.";
						}
					}

	
					if(isset($uploaded_img)){
						$this->conn->query("UPDATE cab_list SET `cab_image_path` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$cid}' ");

		
						if($id == $this->settings->userdata('id')){
							// $this->settings->set_userdata('avatar', $fname);
						}
					}				
				}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if(isset($resp['msg']) && $resp['status'] == 'success'){
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}

	function change_password(){
		if(!empty($_POST['password']))
			$_POST['password'] = md5($_POST['password']);
		else
			unset($_POST['password']);
		if(empty($_POST['id'])){
			$prefix = date('Ym-');
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `cab_list` where reg_code = '{$prefix}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$_POST['reg_code'] = $prefix.$code;
		}


		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','oldpassword'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($cab_reg_no)){
			$check = $this->conn->query("SELECT * FROM `cab_list` where `cab_reg_no` = '{$cab_reg_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = " Cab already exist.";
				return json_encode($resp);
				exit;
			}
		}
		if(isset($body_no)){
			$check = $this->conn->query("SELECT * FROM `cab_list` where `body_no` = '{$body_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = " Cab Body # already exist.";
				return json_encode($resp);
				exit;
			}
		}
		if(isset($oldpassword)){
			$cur_pass = $this->conn->query("SELECT `password` from `cab_list` where id = '{$this->settings->userdata('id')}'")->fetch_array()[0];
			if(md5($oldpassword) != $cur_pass){
				$resp['status'] = 'failed';
				$resp['msg'] = " Current Password is Incorrect.";
				return json_encode($resp);
				exit;
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `cab_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `cab_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			$cid = empty($id) ? $this->conn->insert_id : $id;
			$resp['id'] = $cid ;
			if(empty($id))
				$resp['msg'] = " New Cab successfully saved.";
			else
				$resp['msg'] = " Cab successfully updated.";
				if($this->settings->userdata('id')  == $cid && $this->settings->userdata('login_type') == 3){
					foreach($_POST as $k => $v){
						if(!in_array($k,['password']))
						$this->settings->set_userdata($k,$v);
					}
					$resp['msg'] = " Password Successfully Updated.";
				}
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					if(!is_dir(base_app."uploads/dirvers/"))
						mkdir(base_app."uploads/dirvers/");
					$fname = 'uploads/dirvers/'.$cid.'.png';
					$dir_path =base_app. $fname;
					$upload = $_FILES['img']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg');
					if(!in_array($type,$allowed)){
						$resp['msg'].=" But Image failed to upload due to invalid file type.";
					}else{
						$new_height = 200; 
						$new_width = 200; 
				
						list($width, $height) = getimagesize($upload);
						$t_image = imagecreatetruecolor($new_width, $new_height);
						imagealphablending( $t_image, false );
						imagesavealpha( $t_image, true );
						$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
						imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						if($gdImg){
								if(is_file($dir_path))
								unlink($dir_path);
								$uploaded_img = imagepng($t_image,$dir_path);
								imagedestroy($gdImg);
								imagedestroy($t_image);
						}else{
						$resp['msg'].=" But Image failed to upload due to unkown reason.";
						}
					}
					if(isset($uploaded_img)){
						$this->conn->query("UPDATE cab_list set `image_path` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$cid}' ");
						if($id == $this->settings->userdata('id')){
								$this->settings->set_userdata('avatar',$fname);
						}
					}
				}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if(isset($resp['msg']) && $resp['status'] == 'success'){
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_cab(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `cab_list` set `delete_flag` = 1  where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Cab successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_booking(){
		if(empty($_POST['id'])){
			$prefix = date('Ym-');
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `cab_list` where reg_code = '{$prefix}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$_POST['client_id'] = $this->settings->userdata('id');
			$_POST['ref_code'] = $prefix.$code;
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `booking_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `booking_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success'," Cab has been booked successfully.");
			else
				$this->settings->set_flashdata('success'," Booking successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_booking(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `booking_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Booking successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function update_booking_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `booking_list` set `status` = '{$status}' where id = '{$id}' ");
		if($update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Booking status successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function make_payment(){
		extract($_POST);
		$insert = $this->conn->query("INSERT INTO `payments` (`booking_id`, `name_on_card`, `card_no`, `expiry_date`, `cvv`) VALUES ('{$booking_id}', '{$name}', '{$card_no}', '{$expiry_date}', '{$cvv}')");

		$update = $this->conn->query("UPDATE `booking_list` set `status` = '2' where id = '{$booking_id}' ");
		if($insert && $update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Payment Done Successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function give_rating(){
		extract($_POST);
		$insert = $this->conn->query("INSERT INTO `ratings` (`booking_id`, `rating`, `review`) VALUES ('{$booking_id}', '{$rating}', '{$review}')");

		$update = $this->conn->query("UPDATE `booking_list` set `is_review_added` = '1' where id = '{$booking_id}' ");
		if($insert && $update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Review Added Successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function send_query(){
		extract($_POST);
		$insert = $this->conn->query("INSERT INTO `contact_us` (`name`, `email`, `contact`, `subject`, `message`) VALUES ('{$name}', '{$email}', '{$contact}', '{$subject}', '{$message}')");

		if($insert){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Query Sent Successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'delete_query':
		echo $Master->delete_query();
	break;
	case 'save_cab':
		echo $Master->save_cab();
	break;
	case 'change_password':
		echo $Master->change_password();
	break;
	case 'delete_cab':
		echo $Master->delete_cab();
	break;
	case 'save_booking':
		echo $Master->save_booking();
	break;
	case 'delete_booking':
		echo $Master->delete_booking();
	break;
	case 'update_booking_status':
		echo $Master->update_booking_status();
	break;
	case 'make_payment':
		echo $Master->make_payment();
	break;	
	case 'give_rating':
		echo $Master->give_rating();
	break;
	case 'edit_cab':
		echo $Master->edit_cab();
	break;
	case 'send_query':
		echo $Master->send_query();
	break;	
	case 'export_data':
		echo $Master->export_data();
	break;
	default:
		// echo $sysset->index();
		break;
}