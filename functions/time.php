<?php

/**
 * Converts a UTC DateTime or timestamp to match the current site's timezone.
 * @param  DateTime/int $timestamp Either a DateTime object or timestamp of the UTC moment
 * @return DateTime                DateTime object adjusted to the correct timezone
 */
function cnp_utc_to_now($timestamp) {
	if (is_numeric($timestamp)) $timestamp = (int)$timestamp;
	elseif (is_string($timestamp)) $timestamp = new DateTime($timestamp);
	if ($timestamp instanceof DateTime) $timestamp = (int)$timestamp->format('U');
	if (!is_int($timestamp)) return false;

	$timestamp = $timestamp + (3600 * get_option('gmt_offset'));

	$output = new DateTime();
	$output->setTimestamp($timestamp);

	return $output;
}
