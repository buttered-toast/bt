<?php if (!defined('ABSPATH')) {exit;}

Class Helper {
	//======================================================================
	// GENERAL
	//======================================================================

	// php debugging tool
	static public function _dd ($value, $result_output_as = 'print', $die = false) {
		echo '<div dir="ltr"><pre>';

		switch (strtolower($result_output_as)) {
			case 'print':
				print_r($value);

				break;
			case 'dump':
				var_dump($value);

				break;
			case 'export':
				var_export($value);

				break;
			default:
				print_r($value);

				break;
		}

		echo '</pre></div>';

		if ($die) {
			die;
		}
	}

	// javascript debugging tool
	static public function console_log ($value) {
		echo '<script>var helper_output = ' . json_encode($value) . ';console.log(helper_output);</script>';
	}

	// javascript debugging tool
	static public function alert ($value) {
		echo '<script>var helper_output = ' . json_encode($value) . ';alert(helper_output);</script>';
	}

	// basic php error loger using print_r
	static public function _error_log ($value) {
		error_log(print_r($value, true), 3, BDIR . '/temp-log.txt');
		error_log("\r\n\r\n", 3, BDIR . '/temp-log.txt');
	}

	// output the content
	static public function the_content () {
		if (have_posts()) {
			while (have_posts()) {
				the_post();

				the_content();
			}
		}
	}

	// return the content
	static public function get_the_content () {
		if (have_posts()) {
			while (have_posts()) {
				the_post();

				return get_the_content();
			}
		}
	}

	// get the image alt text
	static public function get_image_alt (&$image) {
		$image_alt = get_post_meta($image->ID);

		if (!empty($image_alt) && is_array($image_alt)) {
			$image->alt = $image_alt;

			if (isset($image_alt['_wp_attachment_image_alt']) && !empty($image_alt['_wp_attachment_image_alt'])) {
				if (is_array($image_alt['_wp_attachment_image_alt'])) {
					$image->alt = esc_attr($image_alt['_wp_attachment_image_alt'][0]);
				} else {
					$image->alt = esc_attr($image_alt['_wp_attachment_image_alt']);
				}
			} else {
				$image->alt = '';
			}
		} else {
			$image->alt = '';
		}
	}

	// get the image by id
	static public function get_image ($image_id, $size, &$image_data) {
		$image = new stdClass();

		$image_src = wp_get_attachment_image_src($image_id, $size);

		if (!empty($image_src) && is_array($image_src)) {
			$image->ID = $image_id;
			
			$image->src = $image_src[0];

			self::get_image_alt($image);
		}

		$image_data = $image;
	}
	
	// escapse the title property and and default when target is empty
	static public function process_link_array (&$link) {
		if (isset($link['title'])) {
			$link['title'] = esc_html($link['title']);
		}
		
		if (isset($link['target'])) {
			$link['target'] = (!empty($link['target'])) ? $link['target'] : '_self';
		}
		
		if (isset($link['url'])) {
			$link['url'] = urldecode(esc_url($link['url']));
		}
	}
	
	// output/get the current wordpress version
	static public function wp_version ($return = false) {
		$wp_version = get_bloginfo('version');
		
		if ($return) {
			return $wp_version;
		}
		
		echo $wp_version;
	}

	//======================================================================
	// GENERAL - END
	//======================================================================

	//======================================================================
	// ADVANCED CUSTOM FIELDS - acf helpers
	//======================================================================

	static protected function handle_field ($field_type, $field_output, &$return_field) {
		$carriage_return = "\r\n";
		$wrapper_tags 	 = [
			'p',
			'div',
			'span'
		];

		if (is_array($field_output)) {
			if (!empty($field_output)) {
				$field_output = $field_output[0];
			} else {
				$field_output = '';
			}
		}

		switch ($field_type) {
			case ($field_type === 'textarea' || (strpos($field_type, 'textarea') !== false)):
				switch ($field_type) {
					case 'textarea':
						$return_field = $field_output;

						break;
					case (strpos($field_type, ',') !== false):
						$field_type_args = explode(',', $field_type);

						switch ($field_type_args[1]) {
							case 'br':
								$return_field = str_replace($carriage_return, '<br>', $field_output);
								break;
							case (in_array($field_type_args[1], $wrapper_tags)):
								$field_output_parts = explode($carriage_return, $field_output);

								foreach ($field_output_parts as $field_output_part) {
									$return_field .= "<{$field_type_args[1]}>{$field_output_part}</{$field_type_args[1]}>";
								}

								break;
							default:
								$return_field = $field_output;

								break;
						}

						break;
				}

				break; // end textarea
			case ($field_type === 'image' || (strpos($field_type, 'image') !== false)):
				switch ($field_type) {
					case 'image':
						self::get_image($field_output, 'full', $image);

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);

						self::get_image($field_output, $field_output_parts[1], $image);

						break;
				}
				$return_field = $image;

				break; // end image
			case 'file':
				$return_field = get_post($field_output);

				break; // end file
			case ($field_type === 'wysiwyg' || (strpos($field_type, 'wysiwyg') !== false)):
				switch ($field_type) {
					case 'wysiwyg':
						$return_field = $field_output;

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);

						if (function_exists($field_output_parts[1])) {
							$return_field = $field_output_parts[1]($field_output);
							break;
						}

						$return_field = $field_output;

						break;
				}

				break; // end wysiwyg
			case ($field_type === 'gallery' || (strpos($field_type, 'gallery') !== false)):
				$gallery_images = [];

				if (!empty($field_output)) {
					switch ($field_type) {
						case 'gallery':
							foreach ($field_output as $image_id) {
								self::get_image($image_id, 'full', $image);
								$gallery_images[] = $image;
							}

							break;
						case (strpos($field_type, ',') !== false):
							$field_output_parts = explode(',', $field_type);

							foreach ($field_output as $image_id) {
								self::get_image($image_id, $field_output_parts[1], $image);
								$gallery_images[] = $image;
							}

							break;
					}
				}

				$return_field = $gallery_images;

				break; // end gallery
			case ($field_type === 'post_object' || (strpos($field_type, 'post_object') !== false)):
				$posts = [];

				switch ($field_type) {
					case 'post_object':
						$return_field = get_post($field_output);

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);

						if ($field_output_parts[1] === 'multi') {
							foreach ($field_output as $post_id) {
								$posts[] = get_post($post_id);
							}

							$return_field = $posts;
						}

						break;
				}

				break; // end post object
			case ($field_type === 'page_link' || (strpos($field_type, 'page_link') !== false)):
				$posts = [];

				switch ($field_type) {
					case 'page_link':
						if (is_numeric($field_output)) {
							$return_field = get_post($field_output);
						} else {
							$return_field = $field_output;
						}

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);

						if ($field_output_parts[1] === 'multi') {
							foreach ($field_output as $post_id) {
								if (is_numeric($post_id)) {
									$posts[] = get_post($post_id);
								} else {
									$posts[] = $post_id; // $post_id holds URL (archive)
								}
							}

							$return_field = $posts;
						}

						break;
				}

				break; // end page link
			case 'relationship':
				$posts = [];

				foreach ($field_output as $post_id) {
					$posts[] = get_post($post_id);
				}

				$return_field = $posts;

				break; // end relationship
			case ($field_type === 'taxonomy' || (strpos($field_type, 'taxonomy') !== false)):
				$terms = [];

				if (!is_array($field_output)) {
					$field_output = [$field_output];
				}
				
				switch ($field_type) {
					case 'taxonomy':
						foreach ($field_output as $term_id) {
							$terms[] = get_term_by('id', $term_id, 'category');
						}

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);

						foreach ($field_output as $term_id) {
							$terms[] = get_term_by('id', $term_id, $field_output_parts[1]);
						}

						break;
				}

				$return_field = $terms;

				break;
			case ($field_type === 'date' || (strpos($field_type, 'date') !== false && strpos($field_type, 'date_time') === false)):
				$splited_date = str_split($field_output);
				$day = $month = $year = '';

				for ($x = 0; $x < 8; $x++) {
					if ($x < 4) {
						$year .= $splited_date[$x];
					} else if ($x < 6) {
						$month .= $splited_date[$x];
					} else if ($x < 8) {
						$day .= $splited_date[$x];
					}
				}

				switch ($field_type) {
					case 'date':
						$date_format = date('d/m/Y', strtotime($day . '-' . $month . '-'. $year));

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);
						$date_format 		= date($field_output_parts[1], strtotime($day . '-' . $month . '-'. $year));

						break;
				}

				$return_field = $date_format;

				break; // end date
			case ($field_type === 'date_time' || (strpos($field_type, 'date_time') !== false)):
				$date_time_parts = explode(' ', $field_output);
				$date_parts 	 = explode('-', $date_time_parts[0]);
				$time_parts 	 = explode(':', $date_time_parts[1]);

				$day   = $date_parts[2];
				$month = $date_parts[1];
				$year  = $date_parts[0];

				$seconds = $time_parts[2];
				$minutes  = $time_parts[1];
				$hour 	 = $time_parts[0];

				switch ($field_type) {
					case 'date_time':
						$date_time_format = date('d/m/Y H:i:s', strtotime($day . '-' . $month . '-'. $year . ' ' . $hour . ':' . $minutes . ':' . $seconds));

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts 	= explode(',', $field_type);
						$date_time_format 		= date($field_output_parts[1], strtotime($day . '-' . $month . '-'. $year . ' ' . $hour . ':' . $minutes . ':' . $seconds));

						break;
				}

				$return_field = $date_time_format;

				break; // end datetime
			case ($field_type === 'time' || (strpos($field_type, 'time') !== false && strpos($field_type, 'date_time') === false)):
				$time_parts = explode(':', $field_output);

				$seconds = $time_parts[2];
				$minutes = $time_parts[1];
				$hour 	 = $time_parts[0];

				switch ($field_type) {
					case 'time':
						$time_format = date('H:i:s', strtotime($hour . ':' . $minutes . ':' . $seconds));

						break;
					case (strpos($field_type, ',') !== false):
						$field_output_parts = explode(',', $field_type);
						$time_format 		= date($field_output_parts[1], strtotime($hour . ':' . $minutes . ':' . $seconds));

						break;
				}

				$return_field = $time_format;

				break;
			default:
				$return_field = $field_output;

				break;
		}
	}

	/*
* $args[0] = field name
* $args[1] = field type
* $args[2] = field location (options, term, null)
* $args[3] = ID from where to get the field
* 
*/

	static protected function get_field ($args, &$field) {
		$current_id 	= get_the_ID();
		$options_prefix = 'options_';
		$basic_fields 	= [
			'text',
			'number',
			'range',
			'email',
			'url',
			'bool',
			'link'
		];

		if (defined('ICL_LANGUAGE_CODE')) {
			if (BASE_LANG != ICL_LANGUAGE_CODE) {
				$options_prefix .= ICL_LANGUAGE_CODE . '_';
			}
		}

		$raw_field_data = '';
		
		if ((isset($args[2]) && $args[2] !== 'null') && isset($args[3])) {
			switch ($args[2]) {
				case 'option':
					$raw_field_data = get_option($options_prefix . $args[0]);

					break;
				case 'term':
					$raw_field_data = get_term_meta($args[3], $args[0]);

					break;
			}
		} elseif ((isset($args[2]) && $args[2] === 'null') && isset($args[3])) {
			$raw_field_data = get_post_meta($args[3], $args[0]);
		} elseif ((isset($args[2]) && $args[2] !== 'null') && !isset($args[3])) {
			switch ($args[2]) {
				case 'option':
					$raw_field_data = get_option($options_prefix . $args[0]);

					if ($args[1] === 'post_object' || (strpos($args[1], 'post_object') !== false)) {
						$raw_field_data = [$raw_field_data];
					}

					break;
				case 'term':
					$raw_field_data = get_term_meta($current_id, $args[0]);

					break;
			}
		} else {
			$raw_field_data = get_post_meta($current_id, $args[0]);
		}

		if (in_array($args[1], $basic_fields)) {
			if (isset($args[2]) && $args[2] === 'option') {
				if ($args[1] === 'link') {
					$field = $raw_field_data;
				} else {
					self::handle_field($args[1], $raw_field_data, $formated_field_data);

					$field = $formated_field_data;
				}
			} else {
				if (is_array($raw_field_data) && !empty($raw_field_data)) {
					$field = $raw_field_data[0];
				} else {
					$field = $raw_field_data;
				}
			}
		} else {
			if ($args[1] === 'relationship') {
				if ($args[2] === 'option') {
					$raw_field_data = [$raw_field_data];
				}
			}
			
			self::handle_field($args[1], $raw_field_data, $formated_field_data);

			$field = $formated_field_data;
		}
		
		if ($args[1] === 'link') {
			self::process_link_array($field);
		}
	}

	static public function create_fields ($args = [], &$output) {
		if (empty($args)) {
			return;
		}

		$output = [];

		foreach ($args as $handler => $arg) {
			self::get_field(explode('|', $arg), $field);

			$output[$handler] = $field;
		}
	}

	static public function create_repeater_fields ($args, &$output) {
		if (empty($args)) {
			return;
		}

		$output 	 = [];
		$rules_parts = explode('|', $args[0]);

		unset($args[0]);

		foreach ($args as $handler => $arg) {
			$arg_parts 	  = explode('|', $arg);
			$arg_parts[0] = $rules_parts[0] . '_' . $rules_parts[1] . '_' . $arg_parts[0];

			if (isset($rules_parts[2])) {
				$arg_parts[] = $rules_parts[2];
			}

			if (isset($rules_parts[3])) {
				$arg_parts[] = $rules_parts[3];
			}

			self::get_field($arg_parts, $field);

			$output[$handler] = $field;
		}
	}

	//======================================================================
	// ADVANCED CUSTOM FIELDS - acf helpers - END
	//======================================================================
}

// Alias for Helper::_dd($value); | For ziv
if (!function_exists('dd')) {
	function dd ($value) {
		Helper::_dd($value);
	}
}
