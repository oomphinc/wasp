<?php

namespace OomphInc\WASP\Event;

final class Events {

	private function __construct() {}

	const PLUGINS_LOADED = 'wasp_plugins_loaded';
	const REGISTER_TRANSFORMS = 'wasp_register_transforms';
	const PRE_COMPILE = 'wasp_pre_compile';
	const POST_COMPILE = 'wasp_post_compile';
	const PRE_TRANSFORM = 'wasp_pre_transform';
	const POST_TRANSFORM = 'wasp_post_transform';
	const TRANSFORMER_SETUP = 'wasp_transformer_setup';

}
