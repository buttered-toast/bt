<?php if (!defined('ABSPATH')) {exit;}

Class Helper {
	static protected $protected_methods = [
		'help_description',
		'handle_field',
		'get_field'
	];

	//======================================================================
	// GENERAL
	//======================================================================

	// php debugging tool
	static public function _dd ($value, $result_output_as = 'print', $die = false) {
		echo '<div class="dir="ltr""><pre>';

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

	// get css from files and minify
	static public function minify_css ($css_files = [], &$css_files_content = '') {
		if (empty($css_files)) {
			return;
		}

		foreach ($css_files as $css_file) {
			$css_files_content .= file_get_contents($css_file);
		}

		$css_files_content = preg_replace('/\/\*((?!\*\/).)*\*\//','',$css_files_content);
		$css_files_content = preg_replace('/\s{2,}/',' ',$css_files_content);
		$css_files_content = preg_replace('/\s*([:;{}])\s*/','$1',$css_files_content);
		$css_files_content = preg_replace('/;}/','}',$css_files_content);
	}
    
    // basic php error loger using print_r
    static public function _error_log ($value) {
        error_log(print_r($value, true), 3, BDIR . '/temp-log.txt');
        error_log("\r\n\r\n", 3, BDIR . '/temp-log.txt');
    }
	
	// get the image alt text
	static public function get_image_alt (&$image) {
		$image_alt = get_post_meta($image->ID);

		if (!empty($image_alt) && is_array($image_alt)) {
			$image->alt = $image_alt;

			if (isset($image_alt['_wp_attachment_image_alt']) && !empty($image_alt['_wp_attachment_image_alt'])) {
				if (is_array($image_alt['_wp_attachment_image_alt'])) {
					$image->alt = $image_alt['_wp_attachment_image_alt'][0];
				} else {
					$image->alt = $image_alt['_wp_attachment_image_alt'];
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
		$image = get_post($image_id);

		if (!empty($image)) {
			$image_src = wp_get_attachment_image_src($image_id, $size);

			if (!empty($image_src) && is_array($image_src)) {
				$image->src = $image_src[0];
			} else {
				$image->src = '';
			}

			self::get_image_alt($image);
		}

		$image_data = $image;
	}

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
				$field = $raw_field_data[0];
			}
		} else {
			self::handle_field($args[1], $raw_field_data, $formated_field_data);

			$field = $formated_field_data;
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
	// HELP - Helper class help
	//======================================================================

	// description builder for the help method
	static protected function help_description ($descriptions = []) {
		$method 			   = '<b><u>METHOD</u></b>:<br><br>';
		$description_paragraph = '<br><hr><br><b><u>DESCRIPTION</u></b>:<br><br>';
		$arguments_paragraph   = '<br><hr><br><b><u>ARGUMENTS</u></b>:<br><br>';
		$output_paragraph 	   = '<br><hr><br><b><u>OUTPUT</u></b>:<br><br>';

		foreach ($descriptions as $descriptions_section => $description) {

			switch ($descriptions_section) {
				case 'method':
					foreach ($description as $descriptions_line) {
						$method .= $descriptions_line . '<br>';
					}
					break;
				case 'description':
					foreach ($description as $descriptions_line) {
						$description_paragraph .= $descriptions_line . '<br>';
					}
					break;
				case 'arguments':
					foreach ($description as $descriptions_line) {
						$arguments_paragraph .= $descriptions_line . '<br>';
					}
					break;
				case 'output':
					foreach ($description as $descriptions_line) {
						$output_paragraph .= $descriptions_line . '<br>';
					}
					break;
			}
		}

		echo '<div class="helper-help-description" dir="ltr"><code>' . $method . $description_paragraph . $arguments_paragraph . $output_paragraph . '</code></div>';
	}

	// get a full description about the Helper class methods or a description about a selected method
	static public function help ($name = '') {
		$available_class_methods = get_class_methods('Helper');

		$general_help_description  = <<<'EOT'
The Helper class provides methods for common wordpress related tasks and general php development.<br><br>
If you want to know more about a method all you need to do is pass the method name to the help method.<br><br>
<b>Example</b>: Helper::help('help');<br><br>
This will retrieve a full description of the method, what arguments it accepts and what it will output/return.<br><br><br>
Here is the list of all available methods in the Helper class:<br>
<ul><li>help</li>
EOT;

		foreach ($available_class_methods as $available_class_method) {
			if ($available_class_method !== 'help') {
				if (!in_array($available_class_method, self::$protected_methods)) {
					if ($available_class_method === 'create_fields') {
						$general_help_description .= '<li>';
						$general_help_description .=   $available_class_method . '<br>';
						$general_help_description .=   '<span style="font-weight: 400;">For a full list of all available arguments pass the help method <span style="font-weight: 700;">' . $available_class_method . '|args</span>.</span><br>';
						$general_help_description .=   'Example: <span style="font-weight: 400;">Helper::help(\'' . $available_class_method . '|args\');</span>';
						$general_help_description .= '</li>';
					} else if ($available_class_method === 'create_repeater_fields') {
						$general_help_description .= '<li>';
						$general_help_description .=   $available_class_method . '<br>';
						$general_help_description .=   '<span style="font-weight: 400;">For a full list of all available arguments pass the help method <span style="font-weight: 700;">' . $available_class_method . '|args</span>.</span><br>';
						$general_help_description .=   'Example: <span style="font-weight: 400;">Helper::help(\'' . $available_class_method . '|args\');</span><br><hr>';
						$general_help_description .=   '<span style="font-weight: 400;">For a repeater example pass the help method <span style="font-weight: 700;">' . $available_class_method . '|repeater</span></span><br>';
						$general_help_description .=   'Example: <span style="font-weight: 400;">Helper::help(\'' . $available_class_method . '|repeater\');</span><br><hr>';
						$general_help_description .=   '<span style="font-weight: 400;">For a repeater inside a repeater example pass the help method <span style="font-weight: 700;">' . $available_class_method . '|repeater_inside_repeater</span></span><br>';
						$general_help_description .=   'Example: <span style="font-weight: 400;">Helper::help(\'' . $available_class_method . '|repeater_inside_repeater\');</span>';
						$general_help_description .= '</li>';
					} else {
						$general_help_description .= '<li>' . $available_class_method . '</li>';
					}
				}
			}
		}

		$general_help_description .= '</ul>';

		echo <<<'EOT'
<style>
.helper-help-description {
padding: 15px;
margin: 20px 0;
border: 1px solid #ddd;
border-radius: 15px;
box-sizing: border-box;
background-color: #fff;
font-size: 1.2em;
max-width: 800px;
/*position: fixed;*/
top: 50px;
left: 50px;
z-index: 2;
}

.helper-help-description ul {
line-height: 2;
font-weight: 700;
}
</style>
EOT;

		if (empty($name)) {
			echo '<div class="helper-help-description" dir="ltr"><code>' . $general_help_description . '</code></div>';
		} else {
			switch ($name) {
				case 'help':
					$descriptions = [
						'method' => [
							'Helper::help($name);'
						],
						'description' => [
							'The <b>help</b> method provides descriptions about the Helper class or the Helper class methods.'
						],
						'arguments'   => [
							'The <b>help</b> method accepts 1 argumnet:',
							'Argument 1 - string $name [optional]: The name of the method that is located inside the Helper class.'
						],
						'output' 	  => [
							'If an argument was passed you will get a full description of the method (if exists).<br>',
							'If no argument was passed you will get a full descripion of how the Helper class works.'
						]
					];

					self::help_description($descriptions);

					break;
				case '_dd':
					$descriptions = [
						'method' => [
							'Helper::_dd($value, $result_output_as = \'print\', $die = false);'
						],
						'description' => [
							'The <b>_dd</b> method is used for debugging purposes in php.'
						],
						'arguments'   => [
							'The <b>_dd</b> method accepts 3 argumnets:',
							'Argument 1 - mixed $value: The content that you want to be printed.<br>',
							'Argument 2 - string $result_output_as [optional] (default: print): The debugging function that you want to use.',
							'<u>Available options</u>: <b>print</b>, <b>dump</b>, <b>export</b>.<br>',
							'Argument 3 - bool $die [optional] (default: false): Stops further code execution if set to <b>true</b>.'
						],
						'output' 	  => [
							'Based on the second argument this method will display information about the first argument that was passed.'
						]
					];

					self::help_description($descriptions);

					break;
				case 'console_log':
					$descriptions = [
						'method' => [
							'Helper::console_log($value);'
						],
						'description' => [
							'The <b>console_log</b> method is used for debugging purposes in javascript.',
							'Similar to the <b>alert</b> method.'
						],
						'arguments'   => [
							'The <b>console_log</b> method accepts 1 argumnet:',
							'Argument 1 - mixed $value: The value you want to output in javascript console.log().',
							'The value is converted to <b>JSON</b> so there is no need to json_encode() the value beforehand.'
						],
						'output' 	  => [
							'The passed value is converted to <b>JSON</b> and stored inside the <b>helper_output</b> variable and then its outputed to the console using the javascript console.log().'
						]
					];

					self::help_description($descriptions);

					break;
				case 'alert':
					$descriptions = [
						'method' => [
							'Helper::alert($value);'
						],
						'description' => [
							'The <b>alert</b> method is used for debugging purposes in javascript.',
							'Similar to the <b>console_log</b> method.'
						],
						'arguments'   => [
							'The <b>alert</b> method accepts 1 argumnet:',
							'Argument 1 - mixed $value: The value you want to output in javascript alert().',
							'The value is converted to <b>JSON</b> so there is no need to json_encode() the value beforehand.'
						],
						'output' 	  => [
							'The passed value is converted to <b>JSON</b> and stored inside the <b>helper_output</b> variable and then its outputed to the console using the javascript alert().'
						]
					];

					self::help_description($descriptions);

					break;
				case 'minify_css':
					$descriptions = [
						'method' => [
							'Helper::minify_css($css_files = [], &$css_files_content = \'\');'
						],
						'description' => [
							'The <b>minify_css</b> method is used for retrieving and minifying css files.',
						],
						'arguments'   => [
							'The <b>minify_css</b> method accepts 2 argumnet:',
							'Argument 1 - array $css_files: An array of css paths.',
							'Argument 2 - variable &$css_files_content: The variable that will store the output.'
						],
						'output' 	  => [
							'A minified and combined version of all the css paths that were passed to the first argument.',
							'The result is not returned, instead using the <b>by reference second argument ($variable)</b> the output will be stored there.'
						]
					];

					self::help_description($descriptions);

					break;
				case 'get_image_alt':
					$descriptions = [
						'method' => [
							'Helper::get_image_alt(&$image);'
						],
						'description' => [
							'The <b>get_image_alt</b> method is used for retrieving the image alt description.',
						],
						'arguments'   => [
							'The <b>get_image_alt</b> method accepts 1 argumnet:',
							'Argument 1 - object &$image: The image object.'
						],
						'output' 	  => [
							'The image object with a new alt property, if the image has an alt description this property will hold that description.',
							'If the image doesn\'t have an alt text this property will be an empty string.'
						]
					];

					self::help_description($descriptions);

					break;
				case 'get_image':
					$descriptions = [
						'method' => [
							'Helper::get_image($image_id, $size, &$image_data);'
						],
						'description' => [
							'The <b>get_image</b> method is used for retrieving an image.',
						],
						'arguments'   => [
							'The <b>get_image</b> method accepts 3 argumnet:',
							'Argument 1 - int $image_id: The image ID.',
							'Argument 2 - string $size: The image size (Check "https://premium.wpmudev.org/blog/wordpress-image-sizes/" for reference of available image sizes).',
							'Argument 3 - variable &$image_data: The variable that will store the image object.'
						],
						'output' 	  => [
							'Based on the the ID provided, if an image was found this method will return the image object (standard wordpress image object) with 2 exceptions.',
							'The object will have 2 new properties, src and alt.',
							'The src will hold the source of the image based on the second argument that was provided (image size).',
							'The alt will hold the image alt description if there is one.',
							'If no image was found based on the ID provided the <b>by reference variable</b> will be NULL.'
						]
					];

					self::help_description($descriptions);

					break;
				case 'create_fields':
					$descriptions = [
						'method' => [
							'Helper::create_fields($args, &$output);'
						],
						'description' => [
							'The <b>create_fields</b> method is used for creating an array of meta properties, this method was made to work instead of the get_field() function of ACF plugin.',
						],
						'arguments'   => [
							'The <b>create_fields</b> method accepts 2 argumnet:',
							'Argument 1 - array $args: an array of all meta data that you want to retrieve.',
							'Argument 2 - variable &$output: The variable that will store all the returned meta data.'
						],
						'output' 	  => [
							'An array that holds all the meta data that was called by the $args argument.'
						]
					];

					self::help_description($descriptions);

					break;
				case 'create_repeater_fields':
					$descriptions = [
						'method' => [
							'Helper::create_repeater_fields($args, &$output);'
						],
						'description' => [
							'The <b>create_repeater_fields</b> method is used for creating an array of meta properties inside a loop, this method was made to work instead of the get_sub_field() function of ACF plugin.',
						],
						'arguments'   => [
							'The <b>create_repeater_fields</b> method accepts 2 argumnet:',
							'Argument 1 - array $args: an array of all meta data that you want to retrieve.',
							'Argument 2 - variable &$output: The variable that will store all the returned meta data.'
						],
						'output' 	  => [
							'An array that holds all the meta data that was called by the $args argument.'
						]
					];

					self::help_description($descriptions);

					break;
				case ($name === 'create_repeater_fields|args' || $name === 'create_fields|args'):
					$help_html  = '<div class="helper-help-description" dir="ltr"><code>';
					$help_html .= 'All available arguments for the <b>create_fields</b> OR <b>create_repeater_fields</b> methods<br><br><hr><br>';
					$help_html .= '<b><u>ARRAY STRUCTURE</u>:</b><br><br>';
					$help_html .= '[\'HANDLE\' => \'FIELD_NAME|FIELD_TYPE|FIELD_LOCATION|ID\']<br><br><hr><br>';
					$help_html .= '<b><u>ARGUMENTS</u>:</b><br><br>';
					$help_html .= '</code></div>';
					
					echo $help_html;
					
					break;
				default:
					echo '<div class="helper-help-description" dir="ltr"><code>There is no <b>' . $name . '</b> method in the Helper class.<br>Use the <b>help</b> method to see all available methods:<br><br>Helper::help();</code></div>';
					break;

			}
		}
	}
}
