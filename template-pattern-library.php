<?php
/**
 * Template Name: Pattern Library
 *
 * @package presscore
 * @since presscore 1.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$config = Presscore_Config::get_instance();
$config->set('template', 'page');
$config->base_init();

get_header(); ?>

		<?php if ( presscore_is_content_visible() ): ?>	

			<div id="content" class="content redirect-url redirect-value-<?php echo get_permalink();?>" role="main">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<?php do_action('presscore_before_loop'); ?>

					<?php the_content(); ?>

					<?php $filter_terms = null;
					if(isset($_GET['filter'])):
						$filter_terms = explode(",",str_replace("%2C",",",$_GET['filter']));
					endif;
					$taxes = array();
					$in = array();
					if($filter_terms):
						foreach($filter_terms as $term):
							$split = explode("-",$term);
							if(count($split)===2):
								$taxes[$split[0]] = preg_replace('/\[/',' ',preg_replace('/\]/','-',$split[1]));    
							endif;
						endforeach;
					endif;
					$args = array(
						'post_type'=>'pattern',
						'posts_per_page'=>30,
						'paged'=>$paged,
						'orderby'=>'meta_value',
						'order'=>'ASC',
						'meta_key'=>'wpcf-manufacturer'
					);
					if(!empty($taxes)):
						$prepare_string = "select post_id as ID, meta_value, meta_key from rtr_postmeta where ";
						$i=0;
						foreach($taxes as $meta_key=>$meta_value):
							$prepare_string.="(meta_key='wpcf-$meta_key' AND meta_value='$meta_value') ";
							$i++;
							if($i<count($taxes)):
								$prepare_string.='OR ';
							endif;
						endforeach;
						$prepare_string.="order by post_id ASC";
						$results = $wpdb->get_results($prepare_string);
						if($results):
							if(count($taxes)>1):
								$tax_count = count($taxes);
								for($i=0;$i<count($results);$i++):
									$result = $results[$i];
									$last = null;
									$count = 1;
									for($j=1;$j<=$tax_count;$j++):
										if($last == $result->ID):
											$count++;
										endif;
										if(!isset($results[$i+$j])):
											break;
										endif;
										$last = $results[$i+$j]->ID;
									endfor;
									if($count==$tax_count):
										$in[] = $result->ID;
									endif;
								endfor;
							else:
								foreach($results as $result):
									$in[] = $result->ID;
								endforeach;
							endif;
						endif;
						if(!empty($in)):
							$args['post__in']=$in;
						endif;
					endif;
					$select_fields = array('Manufacturer'=>'wpcf-manufacturer|ASC','Model'=>'wpcf-model|ASC','Year'=>'wpcf-year|DESC');
					foreach($select_fields as $key=>$value):
						$splits = explode("|",$value);
						$order = $splits[1];
						$value = $splits[0];
						$select_query = "SELECT DISTINCT meta_value as val FROM $wpdb->postmeta WHERE ";
						if(!empty($in)):
							$select_query .= "post_id IN (".implode(',',$in).") AND ";
						endif;
						$select_query.= "meta_key LIKE '$value' ORDER BY val $order";
						$results = $wpdb->get_results(  $select_query );?>
							<div class="select-box">
								<label for="<?php echo strtolower($key);?>">
									<?php echo $key;?>
								</label>
								<select class="terms filter-term" id="<?php echo strtolower($key);?>" name="<?php echo $value;?>">
									<option value=""><?php echo $key;?></option>
									<?php if(!empty($results)):?>
										<?php foreach($results as $row):
											if($row->val):
												$selected = false;
												if(isset($_GET['filter'])&&false!==strpos($_GET['filter'],strtolower($key)."-".preg_replace('/\s/','[',preg_replace('/\-/',']',$row->val)))): 
													$selected = true;
												endif;?>
												<option <?php if($selected) echo 'selected="selected"';?> value="<?php echo "value-".strtolower($key)."-".preg_replace('/\s/','[',preg_replace('/\-/',']',$row->val));?>">
													<?php echo $row->val;?>
												</option>
											<?php endif;
										endforeach;?>
									<?php endif;?>
								</select>
							</div><!--.select-box-->
					<?php endforeach;
					$query = new WP_Query($args);
					if($query->have_posts()):?>
						<table class="pattern-list">
							<thead>
								<th>Manufacturer</th>
								<th>Model</th>
								<th>Year</th>
								<th>Window Location</th>
								<th>Dimensions</th>
								<th>Image</th>
							<thead>
							<tbody>
								<?php while($query->have_posts()):$query->the_post();
									$manufacturer = (types_render_field( 'manufacturer'));
									$model = (types_render_field( 'model'));
									$year = (types_render_field( 'year'));
									$window_location = (types_render_field( 'window-location'));
									$dimensions = (types_render_field( 'dimensions'));
									$image = (types_render_field( 'image'));?>
									<tr class="pattern">
										<?php if($manufacturer):?>
											<td class="manufacturer">
												<?php echo $manufacturer;?>
											</td>
										<?php endif;?>
										<?php if($model):?>
											<td class="model">
												<?php echo $model;?>
											</td>
										<?php endif;?>
										<?php if($year):?>
											<td class="year">
												<?php echo $year;?>
											</td>
										<?php endif;?>
										<?php if($window_location):?>
											<td class="window-location">
												<?php echo $window_location;?>
											</td>
										<?php endif;?>
										<?php if($dimensions):?>
											<td class="dimensions">
												<?php echo $dimensions;?>
											</td>
										<?php endif;?>
										<?php if($image):?>
											<td class="image">
												<?php echo $image;?>
											</td>
										<?php endif;?>
									</tr><!--.pattern-->
								<?php endwhile;?>
							<tbody>
						</table><!--.pattern-list-->
						<?php bella_pagi_posts_nav($query);
						wp_reset_postdata();
					endif;?>
				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'no-results', 'page' ); ?>

			<?php endif; ?>

			</div><!-- #content -->

			<?php do_action('presscore_after_content'); ?>

		<?php endif; // if content visible ?>

<?php get_footer(); ?>