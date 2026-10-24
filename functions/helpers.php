<?php

function my_opt($key) {
    return function_exists('carbon_get_theme_option')
        ? carbon_get_theme_option($key)
        : '';
}

function the_opt($key)
{
	echo esc_html(my_opt($key));
}


function the_opt_br($key)
{
	echo nl2br(esc_html(my_opt($key)));
}


function the_opt_html($key)
{
	echo wp_kses_post(my_opt($key));
}

function clean_phone($phone)
{
	return preg_replace('/[^\d+]/', '', $phone);
}

function opt_tel_href($key)
{
	echo esc_attr(preg_replace('/[^\d+]/', '', my_opt($key)));
}

function opt_map_href($key)
{
	$val = my_opt($key);
	echo esc_attr('https://yandex.ru/maps/?text=' . rawurlencode($val));
}

function opt_email_href($key)
{
	echo esc_attr(my_opt($key));
}

