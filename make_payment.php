<?php
require_once('./config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `booking_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
        $qry2 = $conn->query("SELECT c.*, cc.name as category from `cab_list` c inner join category_list cc on c.category_id = cc.id where c.id = '{$cab_id}' ");
        if($qry2->num_rows > 0){
            foreach($qry2->fetch_assoc() as $k => $v){
                if(!isset($$k))
                $$k=$v;
            }
        }
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
        <form action="" id="payment-form">
            <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="form-group">
                <label for="name" class="control-label">Name on Card</label>
                <input name="name" id="name" type="text" class="form-control rounded-0 form no-resize" value="<?php echo isset($name) ? $name : ''; ?>" required>
            </div>
            <input type="hidden" name="booking_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
            <div class="form-group">
                <label for="name" class="control-label">Card Number</label>
                <input name="card_no" type="text" id="card_no" class="form-control rounded-0 form no-resize" value="<?php echo isset($card_no) ? $card_no : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="name" class="control-label">Expiry Date</label>
                <input name="expiry_date" type="date" id="expiry_date" class="form-control rounded-0 form no-resize" value="<?php echo isset($exp_date) ? $exp_date : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="name" class="control-label">CVV</label>
                <input name="cvv" type="password" id="cvv" class="form-control rounded-0 form no-resize" value="<?php echo isset($cvv) ? $cvv : ''; ?>"  minlength="3" maxlength="3" required>
            </div>
         
            <button class="btn btn-flat btn-success" form="payment-form">Make Payment</button>
        </form>
        
            
        </div>

    </div>
    
    
    <div class="text-right">
        
        <?php if(isset($status) && $status == 0): ?>
        <button class="btn btn-danger btn-flat bg-gradient-danger" type="button" id="cancel_booking">Cancel Bookings</button>
        <?php endif; ?>
        <button class="btn btn-dark btn-flat bg-gradient-dark" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>
<script>
    $(function(){
        $('#cancel_booking').click(function(){
            _conf("Are you sure to cancel your cab booking [Ref. Code: <b><?= isset($ref_code) ? $ref_code : "" ?></b>]?", "cancel_booking",["<?= isset($id) ? $id : "" ?>"])
        })
    })
    
    $('#payment-form').submit(function(e){
        e.preventDefault();
        start_loader();
        var formData = new FormData(this); 
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=make_payment",
            method: "POST",
            data: formData, 
            processData: false,
            contentType: false, 
            dataType: "json",
            error: function(err){
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp){
                if(typeof resp === 'object' && resp.status === 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    });


</script>