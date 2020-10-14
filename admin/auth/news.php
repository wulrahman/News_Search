 
<?php

if($user->admin == 1) {

	require_once('include/header.php');

	require_once('include/main_header.php');

	require_once('include/main_nav.php');
    
    $page=intval($_GET['page']);

    if ($page == 0) {

        $page = 1;

    }

    $limit = 16;
    
    ?>


	<!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Content Management</h2>
										<p>Here you will find all post currently on the site, please note this includes published and unpublished post, unpublished being post with little to no reading value.</p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button data-toggle="tooltip" data-placement="left" title="Download Report" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->
    <!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                            <p>It's just that simple. Turn your simple table into a sophisticated data table and offer your users a nice experience and great features without any effort.</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Summary</th>
                                        <th>Category</th>
                                        <th>Sentiment</th>
                                        <th>Published</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                <?php
                                
                                $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `title`,`id`, `category`, `published` as `date`, `sentiment`, `summary` FROM `news` WHERE `news`.`publish` = 1 
                                ORDER BY `news`.`title`  DESC");

                                $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

                                if($count > 0) { 

                                    while ($row = mysqli_fetch_object($query)) { 

                                        $category_row = $get_content_article->get_category_name($row->category);

                                        $sentiment_row = $get_content_article->get_sentiment_name($row->sentiment);

                                        ?>

                                        <tr>
                                            <td><a href="?type=editnews&id=<?=$row->id?>"><?=$row->title?></a></td>
                                             <td><?=$row->summary?></td>
                                            <td><?=$category_row['name']?></td>
                                            <td><?=$sentiment_row['sentiment']?></td>
                                            <td><?=$row->date?></td>
                                        </tr>

                                        <?php
                                    }
                                }

                                ?>
                               
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>Summary</th>
                                        <th>Category</th>
                                        <th>Sentiment</th>
                                        <th>Published</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data Table area End-->

    <?php 
    
    require_once("include/main_footer.php");
    
	require_once("include/footer.php");
}
else {

	require_once('../common/pages/404.php');

}

?>
