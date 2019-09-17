<?php
function h($value) {
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function hf($value) {
	return htmlspecialchars(filter_input(INPUT_POST, $value), ENT_QUOTES, 'UTF-8');
}
?>