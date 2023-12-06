 <!-- Header-->
 <!-- <header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-center justify-content-center w-100">
        <div class="text-center text-white w-100">
        <h1 class="display-4 fw-bolder">Available Cabs</h1>
            <p class="lead fw-normal text-white-50 mb-0">We will take care of your vehicle</p>
        </div>
    </div>
</header> -->
<!-- Section-->
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5 card rounded-0 card-outline card-purple shadow">
        <div class="row">
            <div class="col-md-12">
            <center>
            <h1 class="display-4 fw-bolder">Available Cabs</h1>
            <hr>
            </center>
                <div class="form-group">
                <div class="input-group mb-3">
                    <input type="search" id="search" class="form-control" placeholder="Search Here..." aria-label="Search Here" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <span class="input-group-text bg-success" id="basic-addon2"><i class="fa fa-search"></i></span>
                    </div>
                </div>
                <hr>
                </div>
                <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-3" id="cab_list">
                    <?php 
                        $cabs = $conn->query("SELECT c.*, cc.name as category, AVG(r.rating) as overall_rating 
                        FROM `cab_list` c 
                        INNER JOIN category_list cc ON c.category_id = cc.id 
                        LEFT JOIN booking_list b ON c.id = b.cab_id 
                        LEFT JOIN ratings r ON b.id = r.booking_id 
                        WHERE c.delete_flag = 0 and c.status = 1
                        AND c.id NOT IN (SELECT cab_id FROM `booking_list` WHERE `status` IN (0,1,2)) 
                        GROUP BY c.id 
                        ORDER BY c.`reg_code`");


                    while($row= $cabs->fetch_assoc()):
                    ?>
                    <a class="col item text-decoration-none text-dark book_cab" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-bodyno="<?php echo $row['body_no'] ?>">
                        <div class="callout callout-primary border-success rounded-0">
                            <dl>
                            <img style="width: 100%;" src="<?php echo validate_image(isset($row['cab_image_path']) ? $row['cab_image_path'] : "") ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                            
                                <dt class="h2"><i class="fa fa-taxi"></i> <?php echo $row['body_no'] ?></dt>
                                <h4 class="truncate-3 text-muted lh-1">
                                    <small><?php echo $row['category'] ?></small><br>
                                    <small><?php echo $row['cab_model'] ?></small>

                                    <h4>
                                    <?php
                                    $overall_rating = $row['overall_rating']; 
                                    $rounded_rating = floor($overall_rating); 
                                    $hasHalfStar = $overall_rating - $rounded_rating > 0; 

                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rounded_rating) {
                                            echo '<span class="fa fa-star checked" style="color: #c7b207;"></span>'; 
                                        } elseif ($hasHalfStar && $i == $rounded_rating + 1) {
                                            echo '<span class="fa fa-star-half checked" style="color: #c7b207;"></span>';
                                        } else {
                                            echo '<span class="empty-star">&#9734;</span>';
                                        }
                                    }
                                    
                                    ?>
                                    </h4>
                                </h4>
                            </dl>
                        </div>
                    </a>
                    <?php endwhile; ?>
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

//     function validate_image($file){
//     $file = explode("?", $file)[0];
//     if(!empty($file)){
//         if(is_file(base_app.$file)){
//             return base_url.$file;
//         } else {
//             return base_url.'dist/img/no-image-available.png';
//         }
//     } else {
//         return base_url.'dist/img/no-image-available.png';
//     }
// }

</script>