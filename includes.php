<?php
require_once('includes/post.php');
require_once('includes/taxonomy.php');
require_once('includes/constants.php');
require_once('includes/common.php');
require_once('includes/response.php');
require_once('includes/error_code.php');
require_once('includes/status_code.php');

require_once('profile/profile_authenticate.php');
require_once('profile/profile_create.php');
require_once('profile/profile_delete.php');
require_once('profile/profile_get.php');
require_once('profile/profile_update.php');
require_once('profile/profile_validate.php');

// Models
require_once('models/comment.php');
require_once('models/coordinate.php');
require_once('models/featured_image.php');
require_once('models/photographer.php');
require_once('models/timestamp.php');
require_once('models/comment_author.php');
require_once('models/discussion.php');
require_once('models/photograph.php');
require_once('models/taxonomy.php');

// Controllers
require_once('controllers/photograph_controller.php');
// DataSource
require_once('data_source/photograph_data_source.php');
require_once('data_source/implementation/wp_photograph_data_source.php');