
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5 card rounded-0 card-outline card-purple shadow">
        <div class="row">
            <div class="col-md-12">
            <center>
            <h1 class="display-4 fw-bolder">Dashboard</h1>
            <hr>
            </center>
            
                <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-3" id="cab_list">
                    <?php 
                       $qry = $conn->query("SELECT * FROM `booking_list` where cab_id = '{$_settings->userdata('id')}' AND status = '4'");
                       $qry_p = $conn->query("SELECT * FROM `booking_list` where cab_id = '{$_settings->userdata('id')}' AND status = '0'");
                       $qry_c = $conn->query("SELECT * FROM `booking_list` where cab_id = '{$_settings->userdata('id')}' AND status = '1'");
                       $qry_rating = $conn->query("SELECT AVG(rating) AS overall_rating FROM `ratings` 
                            INNER JOIN `booking_list` ON ratings.booking_id = booking_list.id 
                            WHERE booking_list.cab_id = '{$_settings->userdata('id')}' 
                            AND booking_list.status = '4'");

                            
                        if ($qry_rating) {
                            $row = $qry_rating->fetch_assoc();
                            $overall_rating = $row['overall_rating'];
                        } 

                       $count = $qry->num_rows;
                       $count_p = $qry_p->num_rows;
                       $count_c = $qry_c->num_rows;

                       $total_amt = 0;

                       while($row = $qry->fetch_assoc()){
                            $total_amt = $total_amt +  $row['amount'];
                        }

                    ?>
                        <div class="callout callout-primary rounded-0 bg-success">
                            <dl>
                                <dt class="h3 text-center">Dropped Off Booking</dt>
                                <dd class="truncate-3">
                                   <h3 class="h3 text-center"><?php echo $count ?></h3>
                                </dd>
                            </dl>
                        </div>
                        <div class="callout callout-primary rounded-0 bg-danger">
                            <dl>
                                <dt class="h3 text-center">Pending Booking</dt>
                                <dd class="truncate-3">
                                   <h3 class="h3 text-center"><?php echo $count_p ?></h3>
                                </dd>
                            </dl>
                        </div>
                        <div class="callout callout-primary rounded-0 bg-primary">
                            <dl>
                                <dt class="h3 text-center">Confirmed Booking</dt>
                                <dd class="truncate-3">
                                   <h3 class="h3 text-center"><?php echo $count_c ?></h3>
                                </dd>
                            </dl>
                        </div>
                        <div class="callout callout-primary rounded-0 bg-secondary">
                            <dl>
                                <dt class="h3 text-center">Total Earning</dt>
                                <dd class="truncate-3">
                                   <h3 class="h3 text-center">$<?php echo $total_amt ?></h3>
                                </dd>
                            </dl>
                        </div>
                        <div class="callout callout-primary rounded-0 bg-warning">
                            <dl>
                                <dt class="h3 text-center">Overall Rating</dt>
                                <dd class="truncate-3">
                                <h3 class="h3 text-center"><?php echo $overall_rating ?></h3>

                                <h3 class="h3 text-center">
                                    <?php
                                    $overall_rating = $overall_rating; 
                                    $rounded_rating = floor($overall_rating); 
                                    $hasHalfStar = $overall_rating - $rounded_rating > 0; 

                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rounded_rating) {
                                            echo '<span class="fa fa-star checked" style="color: black;"></span>'; 
                                        } elseif ($hasHalfStar && $i == $rounded_rating + 1) {
                                            echo '<span class="fa fa-star-half checked" style="color: black;"></span>';
                                        } else {
                                            echo '<span class="empty-star">&#9734;</span>';
                                        }
                                    }
                                    
                                    ?>
                                </h3>


                                </dd>
                            </dl>
                        </div>
                    </a>
                </div>
                <div id="noResult" style="display:none" class="text-center"><b>No Results!!</b></div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('#search').on('input',function(){
            var _search = $(this).val().toLowerCase().trim()
            $('#cab_list .item').each(function(){
                var _text = $(this).text().toLowerCase().trim()
                    _text = _text.replace(/\s+/g,' ')
                    console.log(_text)
                if((_text).includes(_search) == true){
                    $(this).toggle(true)
                }else{
                    $(this).toggle(false)
                }
            })
            if( $('#cab_list .item:visible').length > 0){
                $('#noResult').hide('slow')
            }else{
                $('#noResult').show('slow')
            }
        })
        $('#cab_list .item').hover(function(){
            $(this).find('.callout').addClass('shadow')
        })
        $('#cab_list .book_cab').click(function(){
            if("<?= $_settings->userdata('id') && $_settings->userdata('login_type') == 2 ?>" == 1)
                uni_modal("Book Cab - "+$(this).attr('data-bodyno'),"booking.php?cid="+$(this).attr('data-id'),'mid-large');
            else
            location.href = './login.php';
        })
        $('#send_request').click(function(){
            if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" == 1)
            uni_modal("Fill the cab Request Form","send_request.php",'mid-large');
            else
            alert_toast(" Please Login First.","warning");
        })

    })
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
</script>