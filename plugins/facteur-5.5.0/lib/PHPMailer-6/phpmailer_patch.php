<?php

namespace PHPMailer\PHPMailer;

function ini_set(...$args) {
	if (!function_exists('\ini_set')) {
		return false;
	}
	return \ini_set(...$args);
}
