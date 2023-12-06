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
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-start;
        }

        .star-rating input {
        display: none;
        }

        .star-rating label {
        font-size: 30px;
        color: #ddd;
        cursor: pointer;
        }

        .star-rating label:before {
        content: '\2605';
        }

        .star-rating input:checked ~ label {
        color: #ffcc00;
        }

        .star-rating input:checked ~ label:before {
        color: #ffcc00;
        }

        .star-rating input:checked ~ label ~ input ~ label:before {
        color: #ffcc00;
        }

</style>
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
        <form action="" id="rating-form">
            <input type="hidden" name="booking_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
            <div class="form-group">
                <label for="review" class="control-label">Review</label>
                <textarea name="review" id="review" class="form-control rounded-0 no-resize" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="rating" class="control-label">Rating</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required/>
                    <label for="star5" title="5 stars"></label>
                    <input type="radio" id="star4" name="rating" value="4" required/>
                    <label for="star4" title="4 stars"></label>
                    <input type="radio" id="star3" name="rating" value="3" required/>
                    <label for="star3" title="3 stars"></label>
                    <input type="radio" id="star2" name="rating" value="2" required/>
                    <label for="star2" title="2 stars"></label>
                    <input type="radio" id="star1" name="rating" value="1" required/>
                    <label for="star1" title="1 star"></label>
                </div>
            </div>
            <button type="submit" class="btn btn-flat btn-success"  form="rating-form">Submit Review</button>
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
    
    $('#rating-form').submit(function(e){
        e.preventDefault();
        start_loader();
        var formData = new FormData(this); 
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=give_rating",
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