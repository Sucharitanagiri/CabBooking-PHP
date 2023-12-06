<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `contact_us` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
    <dl>
        <dt class="text-muted"><b>Name</b></dt>
        <dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
        <dt class="text-muted"><b>Email</b></dt>
        <dd class="pl-4"><?= isset($email) ? $email : '' ?></dd>
        <dt class="text-muted"><b>Contact</b></dt>
        <dd class="pl-4"><?= isset($contact) ? $contact : '' ?></dd>
        <dt class="text-muted"><b>Subject</b></dt>
        <dd class="pl-4"><?= isset($subject) ? $subject : '' ?></dd>
        <dt class="text-muted"><b>Message</b></dt>
        <dd class="pl-4"><?= isset($message) ? $message : '' ?></dd>
        
    </dl>
    <div class="clear-fix mb-3"></div>
    <div class="text-right">
        <button class="btn btn-dark bg-gradient-dark btn-flat" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>