<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-purple rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">List of Queries</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-bordered table-stripped table-hover">
				<colgroup>
					<col width="5%">
					<!-- <col width="20%"> -->
					<col width="15%">
					<col width="30%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
				<tr class="bg-gradient-dark text-light">
						<th>#</th>
						<!-- <th>Date Created</th> -->
						<th>Name</th>
						<th>Email</th>
						<th>Contact</th>
						<th>Subject</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `contact_us` order by `id` desc");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['name'] ?></td>
							<td><p class="m-0 truncate-1"><?php echo $row['email'] ?></p></td>
							<td><p class="m-0 truncate-1"><?php echo $row['contact'] ?></p></td>
							<td><p class="m-0 truncate-1"><?php echo $row['subject'] ?></p></td>
							
							<td align="center">
								<button type="button" class="btn btn-flat btn-info btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Action
								<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
								<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){

		$('.view_data').click(function(){
			uni_modal("View Query","queries/view_query.php?id="+$(this).attr('data-id'));
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this query permanently?","delete_query",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_query($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_query",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>