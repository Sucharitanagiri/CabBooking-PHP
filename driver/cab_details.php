<?php 
if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 3){
    $qry = $conn->query("SELECT * FROM `cab_list` where id = '{$_settings->userdata('id')}'");


    if($qry->num_rows >0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k)){
                $$k = $v;
            }
        }
    }else{
        echo "<script> alert('You are not allowed to access this page. Unknown User ID.'); location.replace('./') </script>";
    }
}else{
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

?>
<style>
     #cimg{
        width:15vw;
        height:20vh;
        object-fit:scale-down;
        object-position:center center;
    }
</style>
<div class="content py-5 mt-5">
    <div class="container">
        <div class="card card-outline card-purple shadow rounded-0">
            <div class="card-header">
                <h4 class="card-title"><b>Cab Details</b></h4>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form action="" id="cab-form">
                        <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">

                        <div class="form-group">
                            <label for="body_no" class="control-label">Reg. Code </label>
                            <input name="reg_code" id="reg_code" type="text" class="form-control rounded-0" value="<?php echo isset($reg_code) ? $reg_code : ''; ?>" required readonly>
                        </div>

                        <div class="form-group">
                            <label for="category_id" class="control-label">Category</label>
                            <select name="category_id" id="category_id" class="custom-select select2">
                                <option value="" <?= !isset($category_id) ? "selected" : "" ?> disabled></option>
                                <?php 
                                $categorys = $conn->query("SELECT * FROM category_list where delete_flag = 0 ".(isset($category_id) ? " or id = '{$category_id}'" : "")." order by `name` asc ");
                                while($row= $categorys->fetch_assoc()):
                                ?>
                                <option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? "selected" : "" ?>><?= $row['name'] ?> <?= $row['delete_flag'] == 1 ? "<small>Deleted</small>" : "" ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cab_reg_no" class="control-label">Plate #/Vehicle Reg #</label>
                            <input name="cab_reg_no" id="cab_reg_no" type="text" class="form-control rounded-0" value="<?php echo isset($cab_reg_no) ? $cab_reg_no : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="cab_model" class="control-label">Vehicle Model</label>
                            <input name="cab_model" id="cab_model" type="text" class="form-control rounded-0" value="<?php echo isset($cab_model) ? $cab_model : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="body_no" class="control-label">Cab's Body #</label>
                            <input name="body_no" id="body_no" type="text" class="form-control rounded-0" value="<?php echo isset($body_no) ? $body_no : ''; ?>" required>
                        </div>


                        <div class="row">
                            <div class="form-group col-md-6">
                            <label for="" class="control-label">Cab's Image</label>
                            <div class="custom-file">
                                    <input type="file" class="custom-file-input rounded-0 form-control form-control-sm form-control-border" id="customFile" name="img1" onchange="displayImg(this,$(this))">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                            </div>
                        <div class="row">
                        </div>
                            <div class="form-group col-md-6 d-flex justify-content-center">
                            <img src="<?php echo validate_image(isset($cab_image_path) ? $cab_image_path : "") ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                            </div>
                        </div>



                       
                        <div class="form-group">
                            <label for="status" class="control-label">Status</label>
                            <select name="status" id="status" class="custom-select selevt">
                            <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                       
                    </form>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-flat btn-success" form="cab-form">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
	 window.displayImg = function(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
            $('#cimg').attr('src', "<?php echo validate_image(isset($image_path) ? $image_path : "") ?>");
            _this.siblings('.custom-file-label').html("Choose file")
        }
	}

	$(document).ready(function(){
		$('.select2').select2({
			width:'100%',
			placeholder:"Please Select Here"
		})
		$('.pass_view').click(function(){
			var group = $(this).closest('.input-group');
			var type = group.find('input').attr('type')
			if(type == 'password'){
				group.find('input').attr('type','text').focus()
				$(this).html('<i class="fa fa-eye"></i>')
			}else{
				group.find('input').attr('type','password').focus()
				$(this).html('<i class="fa fa-eye-slash"></i>')
			}
		})
		$('#cab-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=edit_cab",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = "./?p=cab_details";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

        $('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
	})
</script>