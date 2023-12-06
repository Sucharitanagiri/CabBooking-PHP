 <!-- Header-->
 <!-- <header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-end justify-content-center w-100">
        <div class="text-center text-white w-100">
            <h1 class="display-4 fw-bolder mx-5">About Us</h1>
        </div>
    </div>
</header> -->
<section class="py-5">
    <div class="container">
        <div class="card rounded-0 card-outline card-purple shadow px-4 px-lg-5 mt-5">
            <div class="row">
                <div class="card-header">
                    <h4 class="card-title"><b>Contact Form</b></h4>
                </div>
                <div class="card-body">
                    <form action="" id="contact-form">
                    
                        <div class="form-group">
                            <label for="cab_model" class="control-label">Name</label>
                            <input name="name" type="text" class="form-control rounded-0"  required>
                        </div>

                        <div class="form-group">
                            <label for="cab_model" class="control-label">Email</label>
                            <input name="email" type="email" class="form-control rounded-0"  required>
                        </div>

                        <div class="form-group">
                            <label for="cab_model" class="control-label">Contact</label>
                            <input name="contact" type="text" class="form-control rounded-0"  required>
                        </div>

                        <div class="form-group">
                            <label for="cab_model" class="control-label">Subject</label>
                            <input name="subject" type="text" class="form-control rounded-0"  required>
                        </div>

                        <div class="form-group">
                            <label for="cab_model" class="control-label">Message</label>
                            <textarea name="message" type="text" class="form-control rounded-0" rows="6"  required></textarea>
                        </div>
                    </form>
                </div>

                
                <div class="card-footer">
                    <button class="btn btn-flat btn-success" form="contact-form">Send Message</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).scroll(function() { 
        $('#topNavBar').removeClass('bg-purple navbar-light navbar-dark bg-gradient-purple text-light')
        if($(window).scrollTop() === 0) {
           $('#topNavBar').addClass('navbar-dark bg-purple text-light')
        }else{
           $('#topNavBar').addClass('navbar-dark bg-gradient-purple ')
        }
    });
    $(function(){
        $(document).trigger('scroll')
    })

    $('#contact-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=send_query",
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
						location.href = "./?p=contact";
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

</script>