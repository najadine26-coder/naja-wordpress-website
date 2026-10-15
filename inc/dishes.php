<?php
/**
 * Dishes: the real menu, managed in wp-admin (Dishes → Add New).
 *
 * Title = Arabic name. Featured image = dish photo. "Order" (Page Attributes)
 * sets the position in the menu. As soon as one dish is published, the menu,
 * category counts and search use these dishes instead of the samples in
 * js/menu-data.js.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Category ids must match najaMenuCategories in js/menu-data.js. */
function naja_dish_categories() {
	return array(
		'main'      => 'الأطباق الرئيسية — Main Course',
		'rice'      => 'الأرز — Rice',
		'grills'    => 'المشويات — Grills',
		'burgers'   => 'البرجر — Burgers',
		'fastfood'  => 'الوجبات السريعة — Fast Food',
		'pasta'     => 'الباستا — Pasta',
		'beverages' => 'المشروبات — Beverages',
		'desserts'  => 'الحلويات — Desserts',
	);
}

function naja_register_dishes() {
	register_post_type(
		'naja_dish',
		array(
			'labels'        => array(
				'name'          => __( 'Dishes', 'naja' ),
				'singular_name' => __( 'Dish', 'naja' ),
				'add_new_item'  => __( 'Add New Dish', 'naja' ),
				'edit_item'     => __( 'Edit Dish', 'naja' ),
				'all_items'     => __( 'All Dishes', 'naja' ),
				'menu_name'     => __( 'Dishes / الأطباق', 'naja' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => false,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-food',
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'naja_register_dishes' );

/** "Enter title here" → Arabic name hint. */
function naja_dish_title_placeholder( $text, $post ) {
	return 'naja_dish' === $post->post_type ? 'اسم الطبق بالعربية (Arabic name)' : $text;
}
add_filter( 'enter_title_here', 'naja_dish_title_placeholder', 10, 2 );

function naja_dish_meta_box() {
	add_meta_box( 'naja_dish_details', __( 'Dish details', 'naja' ), 'naja_dish_meta_box_html', 'naja_dish', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'naja_dish_meta_box' );

/**
 * Meta box fields.
 *
 * @param WP_Post $post Dish.
 */
function naja_dish_meta_box_html( $post ) {
	wp_nonce_field( 'naja_dish_save', 'naja_dish_nonce' );
	$get   = function ( $key ) use ( $post ) {
		return get_post_meta( $post->ID, '_naja_' . $key, true );
	};
	$is_new = 'auto-draft' === $post->post_status;
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="naja_name_en"><?php esc_html_e( 'English name', 'naja' ); ?></label></th>
			<td><input type="text" id="naja_name_en" name="naja_name_en" class="regular-text" dir="ltr" value="<?php echo esc_attr( $get( 'name_en' ) ); ?>"></td>
		</tr>
		<tr>
			<th><label for="naja_desc_ar"><?php esc_html_e( 'Arabic description', 'naja' ); ?></label></th>
			<td><textarea id="naja_desc_ar" name="naja_desc_ar" class="large-text" rows="2" dir="rtl"><?php echo esc_textarea( $get( 'desc_ar' ) ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="naja_desc_en"><?php esc_html_e( 'English description', 'naja' ); ?></label></th>
			<td><textarea id="naja_desc_en" name="naja_desc_en" class="large-text" rows="2" dir="ltr"><?php echo esc_textarea( $get( 'desc_en' ) ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="naja_category"><?php esc_html_e( 'Category', 'naja' ); ?></label></th>
			<td>
				<select id="naja_category" name="naja_category">
					<?php foreach ( naja_dish_categories() as $id => $label ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $get( 'category' ), $id ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="naja_price"><?php esc_html_e( 'Price (SAR)', 'naja' ); ?></label></th>
			<td><input type="number" id="naja_price" name="naja_price" min="0" step="0.01" class="small-text" value="<?php echo esc_attr( $get( 'price' ) ); ?>"> <span class="description"><?php esc_html_e( 'Leave empty to hide the price.', 'naja' ); ?></span></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Status', 'naja' ); ?></th>
			<td>
				<label><input type="checkbox" name="naja_placeholder" value="1" <?php checked( $is_new || '1' === $get( 'placeholder' ) ); ?>> <?php esc_html_e( 'Sample dish (not confirmed yet) — shows a "Sample" badge and is left out of Google structured data', 'naja' ); ?></label><br>
				<label><input type="checkbox" name="naja_signature" value="1" <?php checked( '1' === $get( 'signature' ) ); ?>> <?php esc_html_e( 'Signature dish on the homepage (needs a featured image; first 4 by Order are shown)', 'naja' ); ?></label>
			</td>
		</tr>
	</table>
	<p class="description"><?php esc_html_e( 'Title = Arabic name. Set the dish photo as the Featured image. Use "Order" in Page Attributes to arrange dishes.', 'naja' ); ?></p>
	<?php
}

/**
 * Save the meta box.
 *
 * @param int $post_id Dish ID.
 */
function naja_dish_save( $post_id ) {
	if ( ! isset( $_POST['naja_dish_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['naja_dish_nonce'] ) ), 'naja_dish_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$fields = array(
		'name_en' => 'sanitize_text_field',
		'desc_ar' => 'sanitize_textarea_field',
		'desc_en' => 'sanitize_textarea_field',
	);
	foreach ( $fields as $key => $sanitize ) {
		$value = isset( $_POST[ 'naja_' . $key ] ) ? call_user_func( $sanitize, wp_unslash( $_POST[ 'naja_' . $key ] ) ) : '';
		update_post_meta( $post_id, '_naja_' . $key, $value );
	}
	$category = isset( $_POST['naja_category'] ) ? sanitize_key( wp_unslash( $_POST['naja_category'] ) ) : 'main';
	update_post_meta( $post_id, '_naja_category', array_key_exists( $category, naja_dish_categories() ) ? $category : 'main' );

	$price = isset( $_POST['naja_price'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['naja_price'] ) ) ) : '';
	update_post_meta( $post_id, '_naja_price', is_numeric( $price ) && $price >= 0 ? (string) ( $price + 0 ) : '' );

	update_post_meta( $post_id, '_naja_placeholder', empty( $_POST['naja_placeholder'] ) ? '0' : '1' );
	update_post_meta( $post_id, '_naja_signature', empty( $_POST['naja_signature'] ) ? '0' : '1' );
}
add_action( 'save_post_naja_dish', 'naja_dish_save' );

/** Admin list columns. */
function naja_dish_columns( $columns ) {
	$columns['naja_category'] = __( 'Category', 'naja' );
	$columns['naja_price']    = __( 'Price', 'naja' );
	$columns['naja_status']   = __( 'Status', 'naja' );
	return $columns;
}
add_filter( 'manage_naja_dish_posts_columns', 'naja_dish_columns' );

function naja_dish_column( $column, $post_id ) {
	if ( 'naja_category' === $column ) {
		$cats = naja_dish_categories();
		$cat  = get_post_meta( $post_id, '_naja_category', true );
		echo esc_html( isset( $cats[ $cat ] ) ? $cats[ $cat ] : '—' );
	} elseif ( 'naja_price' === $column ) {
		$price = get_post_meta( $post_id, '_naja_price', true );
		echo esc_html( '' !== $price ? $price . ' SAR' : '—' );
	} elseif ( 'naja_status' === $column ) {
		$bits = array( '1' === get_post_meta( $post_id, '_naja_placeholder', true ) ? __( 'Sample', 'naja' ) : __( 'Confirmed', 'naja' ) );
		if ( '1' === get_post_meta( $post_id, '_naja_signature', true ) ) {
			$bits[] = __( 'Signature', 'naja' );
		}
		echo esc_html( implode( ' · ', $bits ) );
	}
}
add_action( 'manage_naja_dish_posts_custom_column', 'naja_dish_column', 10, 2 );

/**
 * Published dishes in the shape main.js expects, plus their photos.
 *
 * @return array{items: array, signature: array, images: array}
 */
function naja_dishes_data() {
	$posts = get_posts(
		array(
			'post_type'      => 'naja_dish',
			'post_status'    => 'publish',
			'posts_per_page' => 300,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	$data = array( 'items' => array(), 'signature' => array(), 'images' => array() );
	foreach ( $posts as $post ) {
		$meta  = function ( $key ) use ( $post ) {
			return (string) get_post_meta( $post->ID, '_naja_' . $key, true );
		};
		$price = $meta( 'price' );
		$item  = array(
			'id'          => 'wp' . $post->ID,
			'category'    => $meta( 'category' ) ? $meta( 'category' ) : 'main',
			'price'       => '' === $price ? null : (float) $price,
			'placeholder' => '1' === $meta( 'placeholder' ),
			'name'        => array( 'ar' => get_the_title( $post ), 'en' => $meta( 'name_en' ) ? $meta( 'name_en' ) : get_the_title( $post ) ),
			'desc'        => array( 'ar' => $meta( 'desc_ar' ), 'en' => $meta( 'desc_en' ) ? $meta( 'desc_en' ) : $meta( 'desc_ar' ) ),
		);
		$thumb = get_post_thumbnail_id( $post );
		if ( $thumb ) {
			$src = wp_get_attachment_image_url( $thumb, 'naja-dish' );
			if ( $src ) {
				$key                    = 'dish' . $post->ID;
				$alt                    = trim( (string) get_post_meta( $thumb, '_wp_attachment_image_alt', true ) );
				$data['images'][ $key ] = array(
					'src'      => $src,
					'alt'      => array( 'ar' => $alt ? $alt : $item['name']['ar'], 'en' => $alt ? $alt : $item['name']['en'] ),
					'position' => 'center',
				);
				$item['image']          = $key;
			}
		}
		if ( ! $item['desc']['ar'] && ! $item['desc']['en'] ) {
			unset( $item['desc'] );
		}
		$data['items'][] = $item;
		if ( '1' === $meta( 'signature' ) && ! empty( $item['image'] ) && count( $data['signature'] ) < 4 ) {
			$data['signature'][] = array_merge( $item, array( 'desc' => isset( $item['desc'] ) ? $item['desc'] : array( 'ar' => '', 'en' => '' ) ) );
		}
	}
	return $data;
}

/** JS that swaps the sample menu for the published dishes ("" when there are none). */
function naja_dishes_js() {
	$data = naja_dishes_data();
	if ( ! $data['items'] ) {
		return '';
	}
	$js = 'window.najaMenuItems=' . wp_json_encode( $data['items'] ) . ';';
	if ( $data['images'] ) {
		$js .= '(function(i,n){for(var k in n)i[k]=n[k];})(window.najaImages,' . wp_json_encode( $data['images'] ) . ');';
	}
	if ( $data['signature'] ) {
		$js .= 'window.najaSignatureDishes=' . wp_json_encode( $data['signature'] ) . ';';
	}
	return $js;
}
