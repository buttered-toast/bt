<?php if (!defined('ABSPATH')) {exit;}

/* basic html structure *\
\* basic html structure */
?>
<!doctype html>
<html lang="en" >
  <head>
    <meta charset="UTF-8">
    <title><?php the_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
