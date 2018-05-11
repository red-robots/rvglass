<?php 
function bella_pagi_posts_nav($query) {

global $wp_query;

$query_holder = $wp_query;
$wp_query = $query;

/** Stop execution if there's only 1 page */
if( $wp_query->max_num_pages <= 1 ){
    $wp_query = $query_holder;
    return;
}

$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
$max   = intval( $wp_query->max_num_pages );

/**	Add current page to the array */
if ( $paged >= 1 )
    $links[] = $paged;

/**	Add the pages around the current page to the array */
if ( $paged >= 3 ) {
    $links[] = $paged - 1;
    $links[] = $paged - 2;
}

if ( ( $paged + 2 ) <= $max ) {
    $links[] = $paged + 2;
    $links[] = $paged + 1;
}

echo '<div class="bella-pl-navigation"><ul>' . "\n";

/**	Previous Post Link */
if ( get_previous_posts_link() )
    printf( '<li>%s</li>' . "\n", get_previous_posts_link() );

/**	Link to first page, plus ellipses if necessary */
if ( ! in_array( 1, $links ) ) {
    $class = 1 == $paged ? ' class="active"' : '';

    printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

    if ( ! in_array( 2, $links ) )
        echo '<li>…</li>';
}

/**	Link to current page, plus 2 pages in either direction if necessary */
sort( $links );
foreach ( (array) $links as $link ) {
    $class = $paged == $link ? ' class="active"' : '';
    printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
}

/**	Link to last page, plus ellipses if necessary */
if ( ! in_array( $max, $links ) ) {
    if ( ! in_array( $max - 1, $links ) )
        echo '<li>…</li>' . "\n";

    $class = $paged == $max ? ' class="active"' : '';
    printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
}

/**	Next Post Link */
if ( get_next_posts_link() )
    printf( '<li>%s</li>' . "\n", get_next_posts_link() );

echo '</ul></div>' . "\n";

$wp_query = $query_holder;

}
    function bella_pl_shortcode(){
        global $wpdb;
        global $paged;
        
        ob_start();

        $filter_terms = null;
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
        endif;?>
        <div class="select-boxes redirect-url redirect-value-<?php echo get_permalink();?>">
            <?php $select_fields = array('Manufacturer'=>'wpcf-manufacturer|ASC','Model'=>'wpcf-model|ASC','Year'=>'wpcf-year|DESC');
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
            <?php endforeach;?>
            <div class="select-box bella-clear">
                clear
            </div><!--.select-box-->
            <div class="clearfix"></div>
        </div><!--.select-boxes-->
        <?php $query = new WP_Query($args);
        if($query->have_posts()):?>
            <table class="pattern-list">
                <thead>
                    <th>Manufacturer</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Window Location</th>
                    <th>Dimensions</th>
                    <th class="image">Image</th>
                    <th>Part</th>
                </thead>
                <tbody>
                    <?php while($query->have_posts()):$query->the_post();
                        $manufacturer = (types_render_field( 'manufacturer'));
                        $model = (types_render_field( 'model'));
                        $year = (types_render_field( 'year'));
                        $number = (types_render_field( 'partnum'));
                        $window_location = (types_render_field( 'window-location'));
                        $dimensions = (types_render_field( 'dimensions'));
                        $image = (types_render_field( 'image'));?>
                        <tr class="pattern">
                            <td class="manufacturer">
                                <?php if($manufacturer):?>
                                    <?php echo $manufacturer;?>
                                <?php endif;?>
                            </td>
                            <td class="model">
                                <?php if($model):?>
                                    <?php echo $model;?>
                                <?php endif;?>
                            </td>
                            <td class="year">
                                <?php if($year):?>
                                    <?php echo $year;?>
                                <?php endif;?>
                            </td>
                            <td class="window-location">
                                <?php if($window_location):?>
                                    <?php echo $window_location;?>
                                <?php endif;?>
                            </td>
                            <td class="dimensions">
                                <?php if($dimensions):?>
                                    <?php echo $dimensions;?>
                                <?php endif;?>
                            </td>
                            <td class="image">
                                <?php if($image):?>
                                    <?php echo $image;?>
                                <?php endif;?>
                            </td>
                            <td><a href="<?php the_permalink();?>"><?php echo !empty($number)? $number: 'Link';?></a></td>
                        </tr><!--.pattern-->
                    <?php endwhile;?>
                </tbody>
            </table><!--.pattern-list-->
            <?php bella_pagi_posts_nav($query);
            wp_reset_postdata();
        endif;
        return ob_get_clean();
    }
    add_shortcode('bella_pl', 'bella_pl_shortcode');
?>